<?php
// Load WeFact database connection and settings
chdir('../');
require_once "config.php";

// Load payment provider class
require_once "paynl/payment_provider.php";
$tmp_payment_provider = new paynl();

if(isset($_GET['orderId']))
{
    // Validate transaction
    $tmp_payment_provider->validateTransaction($_GET['orderId']);
}
else
{
    // If no GET-variable
    $tmp_payment_provider->paymentStatusUnknown('transaction id unknown');
}
?>