<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Saa
 * Date: 24.09.13
 * Time: 13:37
 * To change this template use File | Settings | File Templates.
 */

class Apruve_ApruvePayment_Block_Payment_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('apruvepayment/payment/form.phtml');
    }

    /**
     * Check if payment method has title
     * @return bool
     */
    public function hasMethodTitle()
    {
        return true;
    }

    /**
     * Set Mark template
     */
    public function getMethodTitle()
    {
        $locale = Mage::app()->getLocale();

        $mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('apruvepayment/payment/mark.phtml');

        $this->setMethodLabelAfterHtml($mark->toHtml());
    }
}