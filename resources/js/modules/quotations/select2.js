export function bootQuotationSelects() {
    window.addEventListener('livewire:navigated', () => {
        window.dispatchEvent(new CustomEvent('quotation-select2-refresh'));
    });

    document.addEventListener('livewire:update', () => {
        window.dispatchEvent(new CustomEvent('quotation-select2-refresh'));
    });
}
