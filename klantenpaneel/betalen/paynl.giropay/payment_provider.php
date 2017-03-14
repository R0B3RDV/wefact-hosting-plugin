<?php
require_once(dirname(dirname(__FILE__)) . '/paynl/payment_provider.php');

class paynl_giropay extends paynl
{
    public static function getBackofficeSettings()
    {
        $settings = parent::getBackofficeSettings();

        $settings['InternalName'] = 'Pay.nl - Giropay';
        $settings['Advanced']['Title'] = "Betaal met Giropay";
        $settings['Advanced']['Image'] = "paynl.giropay.png";

        return $settings;
    }

    function loadConfPay()
    {
        $this->conf['PaymentDirectory'] = 'paynl.giropay';
        $this->conf['PaymentMethod'] = 'other'; // ideal / paypal / other
        $this->conf['pay_method_id'] = '694';
    }
}