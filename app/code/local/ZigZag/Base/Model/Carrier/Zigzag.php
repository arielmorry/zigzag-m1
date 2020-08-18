<?php

class ZigZag_Base_Model_Carrier_Zigzag
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * @var string
     */
    const ZIGZAG_SHIPPING_TYPES_PATH = 'carriers/zigzagbase/shipping_types';

    /**
     * @var string
     */
    const ZIGZAG_SHIPPING_TYPES_FULL_PATH = 'carriers/zigzagbase/shipping_types_full';

    /**
     * @var string
     */
    const ZIGZAG_SHIPPING_USERNAME_PATH = 'carriers/zigzagbase/username';

    /**
     * @var string
     */
    const ZIGZAG_SHIPPING_PASSWORD_PATH = 'carriers/zigzagbase/password';

    /**
     * @var string
     */
    const ZIGZAG_SHIPPING_EMAIL_PATH = 'carriers/zigzagbase/email';

    /**
     * @var string
     */
    protected $_code = 'zigzagbase';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * Custom Shipping Rates Collector
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        return false;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}