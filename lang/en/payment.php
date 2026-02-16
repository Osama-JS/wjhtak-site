<?php

return [
    // Success
    'success' => 'Payment completed successfully!',

    // Card issues
    'card_declined' => 'Card was declined. Please check your card details or use a different card.',
    'card_invalid_cvv' => 'Invalid CVV code. Please check the number on the back of your card.',
    'card_expired' => 'Card has expired. Please use a valid card.',
    'card_holder_invalid' => 'Invalid cardholder name. Please verify your details.',
    'card_stolen' => 'Transaction declined for security reasons. Please contact your bank.',
    'card_fraud' => 'Transaction declined for security reasons. Please contact your bank.',
    'card_not_enrolled_3ds' => 'Card is not enrolled in 3D Secure. Please activate it with your bank.',
    'card_limit_exceeded' => 'Card limit exceeded. Please contact your bank or use a different card.',
    'card_restriction' => 'Card is restricted for this type of transaction. Please contact your bank.',
    'card_declined_issuer' => 'Transaction declined by the issuing bank. Please contact your bank.',

    // Balance
    'insufficient_funds' => 'Insufficient funds. Please ensure your account has enough balance.',

    // Session/Checkout
    'checkout_expired' => 'Payment session has expired. Please try again.',
    'checkout_already_used' => 'This transaction has already been processed.',

    // Security
    '3ds_failed' => '3D Secure verification failed. Please try again.',
    'risk_rejected' => 'Transaction rejected by security policy. Please contact support.',

    // Technical
    'duplicate_request' => 'This request has already been submitted. Please wait.',
    'network_error' => 'A connection error occurred. Please try again.',
    'timeout' => 'Connection timed out. Please check your internet and try again.',
    'bank_unavailable' => 'Bank is currently unavailable. Please try again later.',
    'internal_error' => 'A system error occurred. Please try again later.',

    // General
    'general_failure' => 'Payment failed. Please try again or use a different payment method.',
];
