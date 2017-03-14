<?php
// Load WeFact database connection and settings
chdir('../');
require_once "config.php";

// Load payment provider class
require_once "paynl.ideal/payment_provider.php";
$tmp_payment_provider = new paynl_ideal();
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