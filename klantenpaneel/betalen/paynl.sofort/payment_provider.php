<?php
require_once(dirname(dirname(__FILE__)) . '/paynl/payment_provider.php');

class paynl_sofort extends paynl
{
    public static function getBackofficeSettings()
    {
        $settings = parent::getBackofficeSettings();

        $settings['InternalName'] = 'Pay.nl - Sofort';
        $settings['Advanced']['Title'] = "Betaal met Sofort";
        $settings['Advanced']['Image'] = "paynl.sofort.png";

        return $settings;
    }

    function loadConfPay()
    {
        $this->conf['PaymentDirectory'] = 'paynl.sofort';
        $this->conf['PaymentMethod'] = 'other'; // ideal / paypal / other
        $this->conf['pay_method_id'] = '559';
    }

}