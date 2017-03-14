<?php
require_once(dirname(dirname(__FILE__)) . '/paynl/payment_provider.php');

class paynl_bancontact extends paynl
{
    public static function getBackofficeSettings()
    {
        $settings = parent::getBackofficeSettings();

        $settings['InternalName'] = 'Pay.nl - Bancontact';
        $settings['Advanced']['Title'] = "Betaal met Bancontact";
        $settings['Advanced']['Image'] = "paynl.bancontact.png";

        return $settings;
    }

    function loadConfPay()
    {
        $this->conf['PaymentDirectory'] = 'paynl.ideal';
        $this->conf['PaymentMethod'] = 'other'; // ideal / paypal / other
        $this->conf['pay_method_id'] = '436';
    }

}