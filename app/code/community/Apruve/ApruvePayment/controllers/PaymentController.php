<?php

class Apruve_ApruvePayment_PaymentController extends Mage_Core_Controller_Front_Action
{
    public function reviewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    private function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }


    public function placeOrderAction()
    {
        $checkoutSession = $this->_getCheckoutSession();
        $errors = array();
        //call rest api and get result if its ok, proceed
        $quote = $checkoutSession->getQuote();

        $payment = $quote->getPayment();
        $method = $payment->getMethodInstance();
        $infoIstance = $method->getInfoInstance();

        $token = $infoIstance->getAdditionalInformation('aprt');
        $amount = $quote->getBaseGrandTotal();

        $rest = Mage::getModel('apruvepayment/api_rest');

        $session = Mage::getSingleton('apruvepayment/session');
        if($session->getAddressUpdated()) {
            $tax = $quote->getShippingAddress()->getTaxAmount();
            $shipping = $quote->getShippingAddress()->getShippingAmount();
            if(!$rest->updatePaymentRequest($token, $amount, $shipping, $tax))
            {
                //show errors
                $errors['couldnt_update'] = 'Couldn\'t update order totals to Apruve';
                $session->setErrors($errors);
                return $this->_redirect("*/*/review");
            }
        }

        if(!$rest->postPayment($token, $amount)) {
            //show errors
            $errors['couldnt_rest_payment'] = 'Apruve couldn\'t process order information';
            $session->setErrors($errors);
            return $this->_redirect("*/*/review");
        }

        $quote = $checkoutSession->getQuote();
        $quote->collectTotals();

        $service = Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();

        $quote->save();

        $checkoutSession->clearHelperData();

        // last successful quote
        $quoteId = $quote->getId();

        $checkoutSession->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

        $order = $service->getOrder();
        if ($order) {
            $checkoutSession->setLastOrderId($order->getId())
                ->setLastRealOrderId($order->getIncrementId());
        } else {
            //error
        }

        $session->unsetData('address_updated');
        return $this->_redirect('checkout/onepage/success');
    }


    /*public function detailsAction()
    {
        $session = $this->_getCheckoutSession();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }

        //$session->clear();
        $this->loadLayout();
        //$this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }*/


    public function updateBillingAddressAction()
    {
        $data = $this->getRequest()->getPost('billing', array());
        $customerAddressId = '';//$data['address_id'];

        if (isset($data['email'])) {
            $data['email'] = trim($data['email']);
        }
        $onePage = Mage::getSingleton('checkout/type_onepage');
        $result = $onePage->saveBilling($data, $customerAddressId);

        if(!empty($result)) {
            Mage::getSingleton('apruvepayment/session')->setErrors($result['message']);
        } else {
                $this->_setAddressUpdated();
        }

        $this->_redirect('*/*/review');
    }

    public function updateShippingAddressAction()
    {
        $data = $this->getRequest()->getPost('shipping', array());
        $customerAddressId = '';//$data['address_id'];
        $onePage = Mage::getSingleton('checkout/type_onepage');

        $result = $onePage->saveShipping($data, $customerAddressId);

        if(!empty($result)) {
            Mage::getSingleton('apruvepayment/session')->setErrors($result['message']);
        } else {
            $this->_setAddressUpdated();
        }


        $this->_redirect('*/*/review');
    }


    public function updateShippingMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $onePage = Mage::getSingleton('checkout/type_onepage');
            $result = $onePage->saveShippingMethod($data);
            $onePage->getQuote()->collectTotals()->save();
        }

        $this->_setAddressUpdated();
        $this->_redirect('*/*/review');
    }


    private function _setAddressUpdated()
    {
        $session = Mage::getSingleton('apruvepayment/session');
        $session->setAddressUpdated(1);
    }


    public function ajaxSetAddressUpdatedAction()
    {
        $this->_setAddressUpdated();
        die(true);
    }
}

