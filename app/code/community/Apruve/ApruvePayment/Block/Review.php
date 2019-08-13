<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Saa
 * Date: 01.10.13
 * Time: 13:09
 * To change this template use File | Settings | File Templates.
 */

class Apruve_ApruvePayment_Block_Review extends Mage_Core_Block_Template
{
    /**
     * Get url for place order action
     * @return string
     */
    public function getPlaceOrderUrl()
    {
        return $this->getUrl('apruvepayment/payment/placeOrder');
    }


    private function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get error
     * @return mixed
     */
    public function getErrors()
    {
        $errors = $this->getApruveSession()->getErrors();
        $this->getApruveSession()->unsetData('errors');
        return $errors;
    }


    /**
     * @return Apruve_ApruvePayment_Model_Session
     */
    public function getApruveSession()
    {
        return Mage::getSingleton('apruvepayment/session');
    }

}