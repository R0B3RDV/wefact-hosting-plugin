<?php
require_once(dirname(dirname(__FILE__)) . '/paynl/payment_provider.php');

class paynl_ideal extends paynl
{
    public static function getBackofficeSettings()
    {
        $settings = parent::getBackofficeSettings();

        $settings['InternalName'] = 'Pay.nl - iDEAL';
        $settings['Advanced']['Title'] = "Betaal met iDEAL";
        $settings['Advanced']['Image'] = "paynl.ideal.png";
        $settings['Advanced']['Extra'] = "Selecteer je bank:";

        return $settings;
    }

    function loadConfPay()
    {
        $this->conf['PaymentDirectory'] = 'paynl.ideal';
        $this->conf['PaymentMethod'] = 'ideal'; // ideal / paypal / other
        $this->conf['pay_method_id'] = '10';
    }

    public function choosePaymentMethod()
    {
        $this->loginSDK();
        $banks = \Paynl\Paymentmethods::getBanks();
//        var_dump($banks);
        // Or get the payment methods and create HTML with options.
        $html = "<select name=\"paynl_bank\">";
        $html .= "<option value=\"\"></option>";
        foreach ($banks as $bank) {
            $html .= "<option value=\"".$bank['id']."\">".$bank['visibleName']."</option>";
        }
        $html .= "</select>";

        return $html;
    }
}