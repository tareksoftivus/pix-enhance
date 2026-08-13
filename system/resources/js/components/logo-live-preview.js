/**
 * Live Logo Preview
 *
 * When a logo media field changes in Settings, update the on-page logo images
 * (e.g. the sidebar brand) immediately — before the form is saved. Images opt
 * in with data-live-logo="<setting-key>" and data-default-src="<fallback>".
 */
document.addEventListener('media-picker:changed', function (e) {
  const { key, url } = e.detail || {};
  if (!key) return;

  document.querySelectorAll('[data-live-logo="' + key + '"]').forEach(function (img) {
    img.src = url || img.dataset.defaultSrc || img.src;
  });
});
