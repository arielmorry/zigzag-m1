<?php

class ZigZag_Double_Model_Carrier_Double
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * @var int
     */
    const ZIGZAG_SHIPPING_TYPE_CODE = 2;

    /**
     * @var string
     */
    const ZIGZAG_SHIPPING_ORDER_STATUSES_PATH = 'carriers/zigzagdouble/order_statuses';

    /**
     * @var string
     */
    const ZIGZAG_SHIPPING_NAME_PATH = 'carriers/zigzagdouble/name';

    /**
     * @var string
     */
    protected $_code = 'zigzagdouble';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * Custom Shipping Rates Collector
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = Mage::getModel('shipping/rate_result');

        if ($request->getPackageValueWithDiscount() >= $this->getConfigData('free_shipping_subtotal')) {
            /** @var Mage_Shipping_Model_Rate_Result_Method $method */
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            $shippingCost = $request->getFreeShipping() ? '0.00' : (float)$this->getConfigData('shipping_cost');

            $method->setPrice($shippingCost);
            $method->setCost($shippingCost);

            $result->append($method);
        } elseif ($this->getConfigData('showmethod')) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $errorMsg = $this->getConfigData('specificerrmsg');
            $error->setErrorMessage(
                $errorMsg ? $errorMsg : Mage::helper('shipping')->__(
                    'Sorry, but this shipping method is not applicable due to destination country or minimum cart amount.'
                )
            );
            return $error;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @param $trackingNumber
     * @return mixed
     */
    public function getTrackingInfo($trackingNumber)
    {
        return Mage::helper('zigzagbase/shipping')->getTrackingInfo($trackingNumber, $this->_code, $this->getConfigData('title'));
    }
}