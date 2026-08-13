/**
 * Media Picker Component
 * Handles the media library modal: browse, search, upload, select.
 */
import { openModal, closeModal } from './modal.js';

/**
 * Broadcast a media selection change so other UI (e.g. the live logo preview)
 * can react. Derives the setting key from the picker's input name, so
 * `settings[site_logo]` emits with key `site_logo`.
 */
function emitPickerChange(picker, url) {
  const input = picker.querySelector('[data-media-picker-input]');
  if (!input) return;

  const match = (input.name || '').match(/\[([^\]]+)\]$/);
  const key = match ? match[1] : input.name;

  document.dispatchEvent(
    new CustomEvent('media-picker:changed', { detail: { key, url } })
  );
}

document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('mediaLibraryModal');
  if (!modal) return;

  const grid = modal.querySelector('[data-media-grid]');
  const searchInput = modal.querySelector('[data-media-search]');
  const typeTabs = modal.querySelectorAll('[data-media-type]');
  const uploadZone = modal.querySelector('[data-media-upload-zone]');
  const uploadInput = modal.querySelector('[data-media-upload-input]');
  const uploadProgress = modal.querySelector('[data-media-upload-progress]');
  const uploadBar = modal.querySelector('[data-media-upload-bar]');
  const selectBtn = modal.querySelector('[data-media-select-btn]');
  const emptyState = modal.querySelector('[data-media-empty]');
  const loadingState = modal.querySelector('[data-media-loading]');
  const loadMoreWrap = modal.querySelector('[data-media-load-more]');
  const loadMoreBtn = loadMoreWrap ? loadMoreWrap.querySelector('button') : null;

  const browseUrl = modal.dataset.browseUrl;
  const uploadUrl = modal.dataset.uploadUrl;
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // State
  let activePicker = null;    // The picker element that opened the modal
  let selectedMedia = null;   // Currently selected media object
  let currentType = '';       // Type filter
  let currentSearch = '';     // Search query
  let currentPage = 1;
  let hasMore = false;
  let isLoading = false;
  let searchTimeout = null;

  // ── Open Modal ──
  document.addEventListener('click', function (e) {
    const trigger = e.target.closest('[data-media-picker-trigger]');
    if (!trigger) return;
    if (e.target.closest('[data-media-picker-remove]')) return;

    e.preventDefault();
    activePicker = trigger.closest('[data-media-picker]');
    selectedMedia = null;

    // Set accept filter from picker
    const accept = activePicker?.dataset.mediaAccept || '';
    if (accept && accept !== 'all') {
      currentType = accept;
      typeTabs.forEach(function (tab) {
        tab.classList.toggle('active', tab.dataset.mediaType === accept);
      });
    } else {
      currentType = '';
      typeTabs.forEach(function (tab) {
        tab.classList.toggle('active', tab.dataset.mediaType === '');
      });
    }

    // Get current value to pre-select
    const input = activePicker?.querySelector('[data-media-picker-input]');
    const currentValue = input ? input.value : '';

    updateSelectBtn();
    currentPage = 1;
    if (searchInput) searchInput.value = '';
    currentSearch = '';

    openModal('mediaLibraryModal');
    loadMedia(false, currentValue);
  });

  // ── Remove Media ──
  document.addEventListener('click', function (e) {
    const removeBtn = e.target.closest('[data-media-picker-remove]');
    if (!removeBtn) return;

    e.preventDefault();
    const picker = removeBtn.closest('[data-media-picker]');
    if (!picker) return;

    const input = picker.querySelector('[data-media-picker-input]');
    const preview = picker.querySelector('[data-media-picker-preview]');
    const placeholder = picker.querySelector('[data-media-picker-placeholder]');

    if (input) input.value = '';
    if (preview) preview.innerHTML = '';
    if (placeholder) placeholder.hidden = false;
    removeBtn.hidden = true;
    picker._stagedFile = null;

    emitPickerChange(picker, '');
  });

  // ── Direct Drag & Drop onto a Picker Dropzone ──
  document.addEventListener('dragover', function (e) {
    const zone = e.target.closest('[data-media-picker-dropzone]');
    if (!zone) return;
    e.preventDefault();
    zone.classList.add('drag-active');
  });

  document.addEventListener('dragleave', function (e) {
    const zone = e.target.closest('[data-media-picker-dropzone]');
    if (zone && !zone.contains(e.relatedTarget)) {
      zone.classList.remove('drag-active');
    }
  });

  document.addEventListener('drop', function (e) {
    const zone = e.target.closest('[data-media-picker-dropzone]');
    if (!zone) return;
    e.preventDefault();
    zone.classList.remove('drag-active');

    const file = e.dataTransfer.files && e.dataTransfer.files[0];
    if (!file) return;
    stageFile(zone.closest('[data-media-picker]'), file);
  });

  // ── Deferred Upload: staged files are sent when the form is saved ──
  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;

    if (form.dataset.mediaUploadsDone) {
      delete form.dataset.mediaUploadsDone;
      return;
    }

    const staged = Array.from(form.querySelectorAll('[data-media-picker]'))
      .filter(function (picker) { return picker._stagedFile; });
    if (!staged.length) return;

    e.preventDefault();
    const submitter = e.submitter;

    Promise.all(staged.map(function (picker) {
      const zone = picker.querySelector('[data-media-picker-dropzone]');
      if (zone) zone.classList.add('uploading');

      return uploadStagedFile(picker).finally(function () {
        if (zone) zone.classList.remove('uploading');
      });
    }))
      .then(function () {
        form.dataset.mediaUploadsDone = '1';
        if (submitter) {
          form.requestSubmit(submitter);
        } else {
          form.requestSubmit();
        }
      })
      .catch(function (err) {
        if (window.showToast) {
          window.showToast('Upload failed', err.message || 'The dropped file could not be uploaded.', 'error');
        }
      });
  });

  // ── Type Tabs ──
  typeTabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      typeTabs.forEach(function (t) { t.classList.remove('active'); });
      this.classList.add('active');
      currentType = this.dataset.mediaType;
      currentPage = 1;
      loadMedia(false);
    });
  });

  // ── Search ──
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function () {
        currentSearch = searchInput.value.trim();
        currentPage = 1;
        loadMedia(false);
      }, 300);
    });
  }

  // ── Load More ──
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function () {
      if (hasMore && !isLoading) {
        currentPage++;
        loadMedia(true);
      }
    });
  }

  // ── Select Item (delegated) ──
  grid.addEventListener('click', function (e) {
    const item = e.target.closest('[data-media-item]');
    if (!item) return;

    grid.querySelectorAll('[data-media-item]').forEach(function (el) {
      el.classList.remove('selected');
    });
    item.classList.add('selected');

    selectedMedia = JSON.parse(item.dataset.mediaItem);
    updateSelectBtn();
  });

  // ── Confirm Select ──
  selectBtn.addEventListener('click', function () {
    if (!selectedMedia || !activePicker) return;

    applyToPicker(activePicker, selectedMedia);
    closeModal('mediaLibraryModal');
  });

  // ── Apply a Media Selection to a Picker ──
  function applyToPicker(picker, media) {
    const input = picker.querySelector('[data-media-picker-input]');
    const preview = picker.querySelector('[data-media-picker-preview]');
    const placeholder = picker.querySelector('[data-media-picker-placeholder]');
    const removeBtn = picker.querySelector('[data-media-picker-remove]');

    if (input) input.value = media.id;
    if (placeholder) placeholder.hidden = true;
    if (removeBtn) removeBtn.hidden = false;
    picker._stagedFile = null;

    if (preview) {
      if (media.type === 'image') {
        preview.innerHTML = '<img src="' + media.url + '" alt="' + (media.name || '') + '">';
      } else {
        preview.innerHTML = '<div class="media-picker-file-icon"><i class="ph ph-file-text"></i><span>' + media.original_name + '</span></div>';
      }
    }

    emitPickerChange(picker, media.url);
  }

  // ── Stage a Dropped File (preview now, upload on form save) ──
  function stageFile(picker, file) {
    if (!picker) return;

    const input = picker.querySelector('[data-media-picker-input]');
    const preview = picker.querySelector('[data-media-picker-preview]');
    const placeholder = picker.querySelector('[data-media-picker-placeholder]');
    const removeBtn = picker.querySelector('[data-media-picker-remove]');

    picker._stagedFile = file;

    if (input) input.value = '';
    if (placeholder) placeholder.hidden = true;
    if (removeBtn) removeBtn.hidden = false;

    const isImage = file.type.indexOf('image/') === 0;
    const objectUrl = URL.createObjectURL(file);

    if (preview) {
      if (isImage) {
        preview.innerHTML = '<img src="' + objectUrl + '" alt="' + file.name + '">';
      } else {
        preview.innerHTML = '<div class="media-picker-file-icon"><i class="ph ph-file-text"></i><span>' + file.name + '</span></div>';
      }
    }

    emitPickerChange(picker, isImage ? objectUrl : '');
  }

  // ── Upload a Picker's Staged File ──
  function uploadStagedFile(picker) {
    const formData = new FormData();
    formData.append('file', picker._stagedFile);

    return fetch(uploadUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: formData,
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (!json.success || !json.data) {
          throw new Error(json.message || 'The file could not be uploaded.');
        }
        applyToPicker(picker, json.data);
        return json.data;
      });
  }

  // ── Upload Zone ──
  if (uploadZone && uploadInput) {
    // Click to browse
    uploadZone.addEventListener('click', function (e) {
      if (e.target.closest('input')) return;
      uploadInput.click();
    });

    // Drag and drop
    uploadZone.addEventListener('dragover', function (e) {
      e.preventDefault();
      this.classList.add('drag-active');
    });

    uploadZone.addEventListener('dragleave', function () {
      this.classList.remove('drag-active');
    });

    uploadZone.addEventListener('drop', function (e) {
      e.preventDefault();
      this.classList.remove('drag-active');
      if (e.dataTransfer.files.length) {
        uploadFiles(e.dataTransfer.files);
      }
    });

    // File input change
    uploadInput.addEventListener('change', function () {
      if (this.files.length) {
        uploadFiles(this.files);
        this.value = '';
      }
    });
  }

  // ── Load Media via AJAX ──
  function loadMedia(append, preSelectId) {
    if (isLoading) return;
    isLoading = true;

    if (!append) {
      grid.innerHTML = '';
      showLoading(true);
    }

    var params = new URLSearchParams();
    params.set('page', currentPage);
    if (currentType) params.set('type', currentType);
    if (currentSearch) params.set('search', currentSearch);

    fetch(browseUrl + '?' + params.toString(), {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (res) {
      if (!res.ok) throw new Error(res.status);
      return res.json();
    })
    .then(function (json) {
      isLoading = false;
      showLoading(false);

      if (!json.success) {
        showEmpty(true);
        showLoadMore(false);
        return;
      }

      var items = json.data.items;
      hasMore = json.data.has_more;

      if (items.length === 0 && !append) {
        showEmpty(true);
        showLoadMore(false);
        return;
      }

      showEmpty(false);
      showLoadMore(hasMore);

      items.forEach(function (item) {
        var el = createGridItem(item);
        if (preSelectId && String(item.id) === String(preSelectId)) {
          el.classList.add('selected');
          selectedMedia = item;
          updateSelectBtn();
        }
        grid.appendChild(el);
      });
    })
    .catch(function () {
      isLoading = false;
      showLoading(false);
      showEmpty(true);
      showLoadMore(false);
    });
  }

  // ── Upload Files ──
  function uploadFiles(files) {
    var total = files.length;
    var completed = 0;

    showUploadProgress(true);
    updateUploadBar(0);

    Array.from(files).forEach(function (file) {
      var formData = new FormData();
      formData.append('file', file);

      var xhr = new XMLHttpRequest();
      xhr.open('POST', uploadUrl);
      xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
      xhr.setRequestHeader('Accept', 'application/json');
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

      xhr.upload.addEventListener('progress', function (e) {
        if (e.lengthComputable) {
          var fileProgress = (e.loaded / e.total) * 100;
          var totalProgress = ((completed * 100) + fileProgress) / total;
          updateUploadBar(totalProgress);
        }
      });

      xhr.addEventListener('load', function () {
        completed++;
        updateUploadBar((completed / total) * 100);

        if (xhr.status === 201 || xhr.status === 200) {
          var json = JSON.parse(xhr.responseText);
          if (json.success && json.data) {
            var el = createGridItem(json.data);
            grid.insertBefore(el, grid.firstChild);
            showEmpty(false);

            // Auto-select the last uploaded file
            if (completed === total) {
              grid.querySelectorAll('[data-media-item]').forEach(function (item) {
                item.classList.remove('selected');
              });
              el.classList.add('selected');
              selectedMedia = json.data;
              updateSelectBtn();
            }
          }
        }

        if (completed === total) {
          setTimeout(function () { showUploadProgress(false); }, 500);
        }
      });

      xhr.addEventListener('error', function () {
        completed++;
        if (completed === total) {
          setTimeout(function () { showUploadProgress(false); }, 500);
        }
      });

      xhr.send(formData);
    });
  }

  // ── Create Grid Item ──
  function createGridItem(item) {
    var div = document.createElement('div');
    div.className = 'media-grid-item';
    div.setAttribute('data-media-item', JSON.stringify(item));

    if (item.type === 'image') {
      div.innerHTML = '<img src="' + item.thumbnail_url + '" alt="' + (item.name || '') + '" loading="lazy">' +
        '<div class="media-grid-item-info"><span>' + item.name + '</span><span class="text-neutral-400">' + item.human_size + '</span></div>';
    } else {
      var iconClass = getFileIcon(item.extension);
      div.innerHTML = '<div class="media-grid-item-icon"><i class="' + iconClass + '"></i></div>' +
        '<div class="media-grid-item-info"><span>' + item.name + '.' + item.extension + '</span><span class="text-neutral-400">' + item.human_size + '</span></div>';
    }

    return div;
  }

  // ── File Icon ──
  function getFileIcon(ext) {
    var map = {
      pdf: 'ph ph-file-pdf', doc: 'ph ph-file-doc', docx: 'ph ph-file-doc',
      xls: 'ph ph-file-xls', xlsx: 'ph ph-file-xls', csv: 'ph ph-file-csv',
      ppt: 'ph ph-file-ppt', pptx: 'ph ph-file-ppt',
      zip: 'ph ph-file-zip', rar: 'ph ph-file-zip', tar: 'ph ph-file-zip',
      mp4: 'ph ph-file-video', avi: 'ph ph-file-video', mov: 'ph ph-file-video',
      mp3: 'ph ph-file-audio', wav: 'ph ph-file-audio',
      svg: 'ph ph-file-svg', txt: 'ph ph-file-text'
    };
    return map[ext] || 'ph ph-file';
  }

  // ── UI Helpers ──
  function updateSelectBtn() {
    if (selectBtn) selectBtn.disabled = !selectedMedia;
  }

  function showLoading(show) {
    if (loadingState) loadingState.classList.toggle('hidden', !show);
  }

  function showEmpty(show) {
    if (emptyState) emptyState.classList.toggle('hidden', !show);
  }

  function showLoadMore(show) {
    if (loadMoreWrap) loadMoreWrap.classList.toggle('hidden', !show);
  }

  function showUploadProgress(show) {
    if (uploadProgress) uploadProgress.classList.toggle('hidden', !show);
    var content = uploadZone?.querySelector('.media-upload-zone-content');
    if (content) content.classList.toggle('hidden', show);
  }

  function updateUploadBar(percent) {
    if (uploadBar) uploadBar.style.width = Math.round(percent) + '%';
  }
});
