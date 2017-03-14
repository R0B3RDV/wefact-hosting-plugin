<?php
require_once(dirname(__FILE__) . '/vendor/autoload.php');

class paynl extends Payment_Provider_Base
{

    function loadConfPay(){
        $this->conf['PaymentDirectory'] = 'paynl';
        $this->conf['PaymentMethod'] = 'other'; // ideal / paypal / other
        $this->conf['pay_method_id'] = '961';
    }

    function __construct()
    {
        // Load parent constructor
        parent::__construct();

        // Load configuration
        $this->loadConfPay();
        $this->loadConf();

        $this->conf['notify_url'] = IDEAL_EMAIL . $this->conf['PaymentDirectory'] . '/notify.php';
        $this->conf['return_url'] = IDEAL_EMAIL . $this->conf['PaymentDirectory'] . '/return.php';
    }

    public static function getBackofficeSettings()
    {
        $settings = array();


        $settings['MerchantID']['Title'] = "Service Id";
        $settings['MerchantID']['Value'] = "";

        $settings['Password']['Title'] = "API token";
        $settings['Password']['Value'] = "";

        $settings['InternalName'] = 'Pay.nl';
        $settings['Advanced']['Title'] = "Betaal met Pay.nl";
        $settings['Advanced']['Image'] = "paynl.png";
        $settings['Advanced']['Extra'] = "";

        $settings['Hint'] = "Maak eerst een account aan op <a  target='_blank' href='https://www.pay.nl/registreren'>pay.nl/registreren</a>.<br />Ga hierna naar <a target='_blank' href='https://admin.pay.nl/programs/programs'>Diensten</a> en klik bij uw dienst op 'gegevens'";

        return $settings;
    }

    public function choosePaymentMethod()
    {
        // If we don't need to ask for payment method upfront, just return false;
        return false;

        // Or get the payment methods and create HTML with options.
        $html = "<select name=\"paynl_bank\">";
        $html .= "<option value=\"\"></option>";
        $html .= "</select>";

        return $html;
    }

    public function validateChosenPaymentMethod()
    {
        // Or check the chosen payment methods and store in session
        if (isset($_POST['paynl_bank']) && $_POST['paynl_bank']) {
            $_SESSION['paynl_bank'] = htmlspecialchars($_POST['paynl_bank']);
        } else {
            unset($_SESSION['paynl_bank']);
        }
        return true;
    }

    public function startTransaction()
    {
        $this->loginSDK();

        $paynl_bank = (isset($_SESSION['paynl_bank']) && $_SESSION['paynl_bank']) ? $_SESSION['paynl_bank'] : null;

        if ($this->Type == 'invoice') {
            $orderID = $this->InvoiceCode;
            $description = __('description prefix invoice') . ' ' . $this->InvoiceCode;
        } else {
            $orderID = $this->OrderCode;
            $description = __('description prefix order') . ' ' . $this->OrderCode;
        }

        $amount = $this->Amount;

        try {
            // Start transaction
            $transaction = \Paynl\Transaction::start(array(
                'amount' => $amount,
                'returnUrl' => $this->conf['return_url'],
                'exchangeUrl' => $this->conf['notify_url'],
                'description' => $description,
                'extra1' => $orderID,
                'ipaddress' => \Paynl\Helper::getIp(),
                'paymentMethod' => $this->conf['pay_method_id'],
                'bank' => $paynl_bank
            ));

            $this->updateTransactionID($transaction->getTransactionId());

            header("Location: " . $transaction->getRedirectUrl());
            exit;
        } catch(Exception $e){
            // Return error message for consumer
            $this->paymentStatusUnknown($e->getMessage());
            exit;
        }
    }

    protected function loginSDK()
    {
        $serviceId = $this->conf['MerchantID'];
        $apiToken = $this->conf['Password'];
        \Paynl\Config::setApiToken($apiToken);
        \Paynl\Config::setServiceId($serviceId);
    }

    public function validateTransaction($transactionID)
    {
        $this->loginSDK();

        $transaction = \Paynl\Transaction::get($transactionID);

        if ($this->isNotificationScript === true) {
            // Check if payment is succeeded
            if ($transaction->isPaid()) {
                // Update database for successfull transaction
                echo "TRUE| Paid";
                $this->paymentProcessed($transactionID);
                die();
            } elseif($transaction->isCanceled()) {
                echo "TRUE| Canceled";
                // Update database for failed transaction
                $this->paymentFailed($transactionID);
                die();
            }
            die('TRUE| Not updated');
        } else {
            // For consumer (in this case the status is already changed by server-to-server notification script)
            if ($this->getType($transactionID) && $this->Paid > 0) {
                if ($this->Type == 'invoice') {
                    $_SESSION['payment']['type'] = 'invoice';
                    $_SESSION['payment']['id'] = $this->InvoiceID;
                } elseif ($this->Type == 'order') {
                    $_SESSION['payment']['type'] = 'order';
                    $_SESSION['payment']['id'] = $this->OrderID;
                }

                // Because type is found, we know it is paid
                $_SESSION['payment']['status'] = 'paid';
                $_SESSION['payment']['paymentmethod'] = $this->conf['PaymentMethod'];
                $_SESSION['payment']['transactionid'] = $transactionID;
                $_SESSION['payment']['date'] = date('Y-m-d H:i:s');
            } else {
                unset($_SESSION['payment']['type'], $_SESSION['payment']['id']);

                $_SESSION['payment']['status'] = 'failed';
                $_SESSION['payment']['paymentmethod'] = $this->conf['PaymentMethod'];
                $_SESSION['payment']['transactionid'] = $transactionID;
                $_SESSION['payment']['date'] = date('Y-m-d H:i:s');
            }


            header("Location: " . IDEAL_EMAIL);
            exit;

        }

    }
}