<?php
require_once(dirname(dirname(__FILE__)) . '/paynl/payment_provider.php');

class paynl_visamastercard extends paynl
{
    public static function getBackofficeSettings()
    {
        $settings = parent::getBackofficeSettings();

        $settings['InternalName'] = 'Pay.nl - Visa/Mastercard';
        $settings['Advanced']['Title'] = "Betaal met Visa/Mastercard";
        $settings['Advanced']['Image'] = "paynl.visamastercard.png";

        return $settings;
    }

    function loadConfPay()
    {
        $this->conf['PaymentDirectory'] = 'paynl.visamastercard';
        $this->conf['PaymentMethod'] = 'other'; // ideal / paypal / other
        $this->conf['pay_method_id'] = '706';
    }

}