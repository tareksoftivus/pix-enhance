/**
 * Toast Notification Component
 * @module components/toast
 */

/**
 * Shows a toast notification.
 * @param {string} title - The title of the toast message.
 * @param {string} message - The body message of the toast.
 * @param {string} type - The type of toast ('success', 'error', 'warning', 'info'). Use 'success' as default.
 */
export function showToast(title, message, type = "success") {
  const container = document.getElementById("toastContainer");
  if (!container) {
    console.error("Toast container not found!");
    return;
  }

  const toast = document.createElement("div");
  toast.className = "toast";

  let icon = "ph-check-circle";
  let iconStatusClass = "toast-success-icon";

  switch (type) {
    case "error":
      icon = "ph-warning-circle";
      iconStatusClass = "toast-error-icon";
      break;
    case "warning":
      icon = "ph-warning";
      iconStatusClass = "toast-warning-icon";
      break;
    case "info":
      icon = "ph-info";
      iconStatusClass = "toast-info-icon";
      break;
    default:
      icon = "ph-check-circle";
      iconStatusClass = "toast-success-icon";
  }

  toast.innerHTML = `
      <div class="toast-icon ${iconStatusClass}">
          <i class="ph ${icon} text-lg"></i>
      </div>
      <div class="flex-1">
          <p class="text-sm font-bold text-neutral-950">${title}</p>
          <p class="text-xs text-neutral-500">${message}</p>
      </div>
      <button class="text-neutral-300 hover:text-neutral-500" onclick="this.parentElement.remove()">
          <i class="ph ph-x"></i>
      </button>
    `;

  container.appendChild(toast);
  requestAnimationFrame(() => toast.classList.add("show"));
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Expose globally for inline scripts (flash messages, AJAX callbacks)
window.showToast = showToast;

// Event Delegation for Toasts
document.addEventListener("click", (e) => {
  const trigger = e.target.closest("[data-toast-trigger]");
  if (trigger) {
    const { title, message, type } = trigger.dataset;
    showToast(title, message, type);
  }
});
