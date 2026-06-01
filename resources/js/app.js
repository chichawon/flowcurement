import './bootstrap';
import { useAdminShell } from './modules/admin';
import { bootQuotationSelects } from './modules/quotations/select2';
import { bootDeliveryReceiptSelects } from './modules/sales/delivery-receipts/select2';
import './modules/sales/orders/select2';

window.useAdminShell = useAdminShell;
bootQuotationSelects();
bootDeliveryReceiptSelects();
