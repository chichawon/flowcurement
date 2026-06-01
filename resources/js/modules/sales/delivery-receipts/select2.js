export function bootDeliveryReceiptSelects() {
    window.addEventListener('livewire:navigated', () => {
        window.dispatchEvent(new CustomEvent('delivery-receipt-select-refresh'));
    });

    document.addEventListener('livewire:update', () => {
        window.dispatchEvent(new CustomEvent('delivery-receipt-select-refresh'));
    });
}

