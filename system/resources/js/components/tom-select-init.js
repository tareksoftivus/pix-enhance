/**
 * Tom Select Initialization
 *
 * Replaces jQuery Select2 with vanilla JS Tom Select.
 * Supports both legacy class names (.select2-basic, .select2-multi)
 * and new class names (.ts-basic, .ts-multi) for backward compatibility.
 */

import TomSelect from 'tom-select';

function initTomSelect() {
    const dir = document.documentElement.dir || 'ltr';

    // Basic searchable select
    document.querySelectorAll('.select2-basic, .ts-basic').forEach(el => {
        if (el.tomselect) return; // Already initialized
        new TomSelect(el, {
            allowEmptyOption: true,
            controlClass: 'ts-control',
            dropdownClass: 'ts-dropdown',
        });
    });

    // Multi-select with tags
    document.querySelectorAll('.select2-multi, .ts-multi').forEach(el => {
        if (el.tomselect) return; // Already initialized
        new TomSelect(el, {
            plugins: ['remove_button'],
            controlClass: 'ts-control',
            dropdownClass: 'ts-dropdown',
            placeholder: el.getAttribute('placeholder') || undefined,
        });
    });
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    initTomSelect();
});

// Re-initialize on RTL toggle
document.addEventListener('rtl-toggled', () => {
    document.querySelectorAll('.select2-basic, .ts-basic, .select2-multi, .ts-multi').forEach(el => {
        if (el.tomselect) {
            el.tomselect.destroy();
        }
    });
    initTomSelect();
});

// Re-initialize after datatable content swap
document.addEventListener('datatable:updated', () => {
    initTomSelect();
});

export { initTomSelect };
