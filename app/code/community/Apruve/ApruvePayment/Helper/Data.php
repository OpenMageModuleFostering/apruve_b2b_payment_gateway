<?php


class Apruve_ApruvePayment_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return Apruve_ApruvePayment_Model_Api_PaymentRequest
     */
    public function getPaymentRequestApiModel()
    {
        return Mage::getModel('apruvepayment/api_PaymentRequest');
    }


    /**
     * @return string url
     */
    public function getAjaxAddressUpdatedUrl()
    {
        $secure = Mage::app()->getStore()->isCurrentlySecure() ? true : false;
        return Mage::getUrl('apruvepayment/payment/ajaxSetAddressUpdated', array('_secure' => $secure));
    }
}