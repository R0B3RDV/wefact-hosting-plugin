<?php
// Load WeFact database connection and settings
chdir('../');
require_once "config.php";

// Load payment provider class
require_once "paynl.sofort/payment_provider.php";
$tmp_payment_provider = new paynl_sofort();
$tmp_payment_provider->isNotificationScript = true;



if(isset($_REQUEST['order_id']))
{
    // Validate transaction
    $tmp_payment_provider->validateTransaction($_GET['order_id']);
}
else
{
    // If no GET-variable
    $tmp_payment_provider->paymentStatusUnknown('transaction id unknown');
}
?>