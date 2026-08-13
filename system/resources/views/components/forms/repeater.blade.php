@props([
    'label' => '',
    'name',
    'items' => [],
    'schema' => [],
    'hint' => '',
    'errorKey' => null,
])

@php
    $componentId = 'repeater-' . md5($name . json_encode($schema));
    $schemaCollection = collect($schema);
    $hasEditorField = $schemaCollection->contains(fn ($field) => ($field['type'] ?? 'text') === 'editor');
    $initialItems = old($name, $items);
    if (is_string($initialItems)) {
        $decoded = json_decode($initialItems, true);
        $initialItems = is_array($decoded) ? $decoded : [];
    }
    $errorName = $errorKey ?? $name;
@endphp

<div
    id="{{ $componentId }}"
    data-repeater-field
    data-name="{{ $name }}"
    data-schema='@json($schema)'
    data-initial-items='@json($initialItems)'
    class="space-y-3"
>
    @if($label)
        <label class="form-label">{{ $label }}</label>
    @endif

    <input type="hidden" name="{{ $name }}" value='@json($initialItems)' data-repeater-input>

    <div class="space-y-3" data-repeater-items></div>

    <button
        type="button"
        class="inline-flex items-center gap-2 rounded-lg border border-dashed border-neutral-300 px-3 py-2 text-sm font-medium text-neutral-700 transition hover:border-primary hover:text-primary"
        data-repeater-add
    >
        <i class="ph ph-plus"></i>
        {{ __('Add Item') }}
    </button>

    @if($hint)
        <p class="form-hint">{{ $hint }}</p>
    @endif

    @error($errorName)
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

@if($hasEditorField)
    @once
        @push('styles')
            <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet" />
        @endpush
    @endonce

    @once
        @push('scripts')
            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        @endpush
    @endonce
@endif

@once
    @push('scripts')
        <script>
            (function () {
              if (window.__frontendRepeaterInitialized) return;
              window.__frontendRepeaterInitialized = true;

              let repeaterSequence = 0;

              function escapeHtml(value) {
                return String(value ?? '')
                  .replace(/&/g, '&amp;')
                  .replace(/</g, '&lt;')
                  .replace(/>/g, '&gt;')
                  .replace(/"/g, '&quot;')
                  .replace(/'/g, '&#039;');
              }

              function normalizeText(value) {
                if (value === null || value === undefined || value === '') {
                  return '';
                }

                if (Array.isArray(value)) {
                  return value.map(normalizeText).filter(Boolean).join(', ');
                }

                if (typeof value === 'object') {
                  if (value.key) {
                    return String(value.key);
                  }

                  return Object.values(value).map(normalizeText).filter(Boolean).join(', ');
                }

                return String(value);
              }

              function renderHint(fieldDef) {
                const hint = normalizeText(fieldDef.hint || '');
                if (!hint) {
                  return '';
                }

                return `<p class="form-hint">${escapeHtml(hint)}</p>`;
              }

              function renderField(fieldKey, fieldDef, rowData, index, rowKey) {
                const value = rowData?.[fieldKey] ?? '';
                const label = escapeHtml(normalizeText(fieldDef.label || fieldKey));
                const fieldType = fieldDef.type || 'text';
                const fieldName = escapeHtml(fieldKey);
                const hintMarkup = renderHint(fieldDef);
                const inputType = ['email', 'url', 'password', 'tel', 'search', 'number', 'color'].includes(fieldType) ? fieldType : 'text';

                if (fieldType === 'editor') {
                  const editorId = `repeater-editor-${rowKey}-${fieldName}-${index}`;

                  return `
                    <div class="space-y-2">
                      <label class="form-label">${label}</label>
                      <div class="editor-wrapper" data-repeater-editor-wrapper>
                        <div id="${editorId}" data-repeater-editor="${fieldName}">${value ?? ''}</div>
                        <textarea id="${editorId}-html" class="editor-html-source"></textarea>
                      </div>
                      <input type="hidden" value="${escapeHtml(value)}" data-repeater-prop="${fieldName}" data-repeater-editor-input="${fieldName}">
                      ${hintMarkup}
                    </div>
                  `;
                }

                if (fieldType === 'textarea') {
                  return `
                    <div class="space-y-2">
                      <label class="form-label">${label}</label>
                      <textarea class="textarea-field" rows="4" data-repeater-prop="${fieldName}">${escapeHtml(value)}</textarea>
                      ${hintMarkup}
                    </div>
                  `;
                }

                if (fieldType === 'select') {
                  const options = Object.entries(fieldDef.options || {}).map(function ([optionValue, optionLabel]) {
                    const selected = String(value ?? '') === String(optionValue) ? ' selected' : '';
                    return `<option value="${escapeHtml(optionValue)}"${selected}>${escapeHtml(normalizeText(optionLabel))}</option>`;
                  }).join('');

                  return `
                    <div class="space-y-2">
                      <label class="form-label">${label}</label>
                      <select class="select-field" data-repeater-prop="${fieldName}">
                        ${options}
                      </select>
                      ${hintMarkup}
                    </div>
                  `;
                }

                if (fieldType === 'feature' || fieldType === 'boolean') {
                  const checked = value ? ' checked' : '';

                  return `
                    <div class="space-y-2">
                      <label class="checkbox-label">
                        <input type="checkbox" class="checkbox-field" data-repeater-prop="${fieldName}" value="1"${checked}>
                        <span class="checkbox-text">${label}</span>
                      </label>
                      ${hintMarkup}
                    </div>
                  `;
                }

                if (fieldType === 'number') {
                  return `
                    <div class="space-y-2">
                      <label class="form-label">${label}</label>
                      <input type="number" value="${escapeHtml(value)}" class="input-field" data-repeater-prop="${fieldName}">
                      ${hintMarkup}
                    </div>
                  `;
                }

                if (fieldType === 'color') {
                  return `
                    <div class="space-y-2">
                      <label class="form-label">${label}</label>
                      <input type="color" value="${escapeHtml(value || '#000000')}" class="input-field h-12 p-2" data-repeater-prop="${fieldName}">
                      ${hintMarkup}
                    </div>
                  `;
                }

                return `
                  <div class="space-y-2">
                    <label class="form-label">${label}</label>
                    <input type="${inputType}" value="${escapeHtml(value)}" class="input-field" data-repeater-prop="${fieldName}">
                    ${hintMarkup}
                  </div>
                `;
              }

              function sync(container) {
                const hidden = container.querySelector('[data-repeater-input]');
                const rows = Array.from(container.querySelectorAll('[data-repeater-row]')).map(function (row) {
                  const rowData = {};

                  row.querySelectorAll('[data-repeater-prop]').forEach(function (field) {
                    if (field.type === 'checkbox') {
                      rowData[field.dataset.repeaterProp] = field.checked;
                      return;
                    }

                    rowData[field.dataset.repeaterProp] = field.value ?? '';
                  });

                  return rowData;
                }).filter(function (row) {
                  return Object.values(row).some(function (value) {
                    return String(value ?? '').trim() !== '';
                  });
                });

                hidden.value = JSON.stringify(rows);
              }

              function initEditorField(row, editorElement) {
                if (!editorElement || editorElement.dataset.editorReady === '1' || typeof Quill === 'undefined') {
                  return;
                }

                const prop = editorElement.dataset.repeaterEditor;
                const hiddenInput = row.querySelector('[data-repeater-editor-input="' + prop + '"]');
                const wrapper = editorElement.closest('[data-repeater-editor-wrapper]');
                const htmlTextarea = wrapper?.querySelector('.editor-html-source');

                if (!hiddenInput) {
                  return;
                }

                editorElement.dataset.editorReady = '1';

                const quill = new Quill(editorElement, {
                  theme: 'snow',
                  placeholder: '{{ __('Type your content here...') }}',
                  modules: {
                    toolbar: {
                      container: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'direction': 'rtl' }],
                        [{ 'align': [] }],
                        ['link', 'image', 'video'],
                        ['clean'],
                      ],
                    },
                  },
                });

                let htmlMode = false;

                hiddenInput.value = quill.root.innerHTML;

                quill.on('text-change', function () {
                  if (!htmlMode) {
                    hiddenInput.value = quill.root.innerHTML;
                    sync(row.closest('[data-repeater-field]'));
                  }
                });

                if (wrapper) {
                  const toolbar = wrapper.querySelector('.ql-toolbar');

                  if (toolbar && htmlTextarea) {
                    const htmlBtnGroup = document.createElement('span');
                    htmlBtnGroup.className = 'ql-formats';
                    const htmlBtn = document.createElement('button');
                    htmlBtn.type = 'button';
                    htmlBtn.className = 'ql-html-btn';
                    htmlBtn.innerHTML = '&lt;/&gt;';
                    htmlBtn.title = 'HTML Source';
                    htmlBtnGroup.appendChild(htmlBtn);
                    toolbar.appendChild(htmlBtnGroup);

                    htmlBtn.addEventListener('click', function () {
                      htmlMode = !htmlMode;
                      htmlBtn.classList.toggle('ql-active', htmlMode);

                      const editorSurface = wrapper.querySelector('.ql-editor');
                      if (htmlMode) {
                        htmlTextarea.value = quill.root.innerHTML;
                        htmlTextarea.classList.add('active');
                        if (editorSurface) {
                          editorSurface.style.display = 'none';
                        }
                      } else {
                        quill.root.innerHTML = htmlTextarea.value;
                        hiddenInput.value = htmlTextarea.value;
                        htmlTextarea.classList.remove('active');
                        if (editorSurface) {
                          editorSurface.style.display = '';
                        }
                        sync(row.closest('[data-repeater-field]'));
                      }
                    });

                    htmlTextarea.addEventListener('input', function () {
                      hiddenInput.value = htmlTextarea.value;
                      sync(row.closest('[data-repeater-field]'));
                    });
                  }
                }
              }

              function initEditors(row) {
                row.querySelectorAll('[data-repeater-editor]').forEach(function (editorElement) {
                  initEditorField(row, editorElement);
                });
              }

              function addRow(container, rowData = {}) {
                const schema = JSON.parse(container.dataset.schema || '{}');
                const itemsRoot = container.querySelector('[data-repeater-items]');
                const index = itemsRoot.querySelectorAll('[data-repeater-row]').length;
                const rowKey = repeaterSequence++;
                const row = document.createElement('div');

                row.className = 'rounded-2xl border border-neutral-100 bg-neutral-0 p-4';
                row.dataset.repeaterRow = '1';
                row.innerHTML = `
                  <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm font-semibold text-neutral-950">{{ __('Item') }} <span>${index + 1}</span></p>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>
                      <i class="ph ph-trash"></i>
                      {{ __('Remove') }}
                    </button>
                  </div>
                  <div class="space-y-4">
                    ${Object.entries(schema).map(([fieldKey, fieldDef]) => renderField(fieldKey, fieldDef, rowData, index, rowKey)).join('')}
                  </div>
                `;

                itemsRoot.appendChild(row);
                initEditors(row);
                sync(container);
              }

              function initRepeater(container) {
                if (container.dataset.bound === '1') return;
                container.dataset.bound = '1';

                let initialItems = [];
                try {
                  initialItems = JSON.parse(container.dataset.initialItems || '[]');
                } catch (error) {
                  initialItems = [];
                }

                if (Array.isArray(initialItems) && initialItems.length) {
                  initialItems.forEach(function (item) {
                    addRow(container, item);
                  });
                }

                container.querySelector('[data-repeater-add]')?.addEventListener('click', function () {
                  addRow(container);
                });

                container.addEventListener('click', function (event) {
                  const removeBtn = event.target.closest('[data-repeater-remove]');
                  if (!removeBtn) return;

                  removeBtn.closest('[data-repeater-row]')?.remove();
                  sync(container);
                });

                container.addEventListener('input', function (event) {
                  if (!event.target.matches('[data-repeater-prop]')) return;
                  sync(container);
                });
              }

              function boot() {
                document.querySelectorAll('[data-repeater-field]').forEach(initRepeater);
              }

              if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', boot);
              } else {
                boot();
              }
            })();
        </script>
    @endpush
@endonce
