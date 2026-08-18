<?php

return [
    'invoice_prefix' => env('BILLING_INVOICE_PREFIX', 'INV'),
    'invoice_due_days' => env('BILLING_INVOICE_DUE_DAYS', 7),
    'receipt_company' => env('APP_NAME', 'Enhance'),
    'receipt_email' => env('MAIL_FROM_ADDRESS'),
    'receipt_address' => env('BILLING_RECEIPT_ADDRESS'),
    'currency' => env('PAYMENT_CURRENCY', 'USD'),
];
