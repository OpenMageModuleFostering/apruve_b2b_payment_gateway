<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Saa
 * Date: 14.10.13
 * Time: 12:08
 * To change this template use File | Settings | File Templates.
 */

class Apruve_ApruvePayment_Block_Admin_Webhook extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $merchantKey = Mage::getStoreConfig('payment/apruvepayment/merchant');
        $apiKey = Mage::getStoreConfig('payment/apruvepayment/api');
        $baseFrontUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $moduleControllerAction = 'apruvepayment/webhook/updateOrderStatus?';
        if(!is_null($merchantKey) && !is_null($apiKey)) {
            $data = $apiKey.$merchantKey;
            $q = hash('sha256', $data);
            $url = $baseFrontUrl.$moduleControllerAction.$q;
            return $url;
        } else {

            $message = 'Please, specify merchant and api key';
            return $message;
        }
    }
}