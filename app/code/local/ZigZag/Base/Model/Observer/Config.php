<?php

/**
 * Class ZigZag_Base_Model_Observer_Config
 */
class ZigZag_Base_Model_Observer_Config
{
    /**
     * @param $observer
     * @return ZigZag_Base_Model_Observer_Config
     */
    public function setShippingTypes($observer)
    {
        $config = $observer->getObject();
        if ($config->getSection() == 'carriers') {
            $options             = [];
            $username            = null;
            $password            = null;
            $currentShippingType = Mage::helper('zigzagbase')->getConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_TYPES_PATH);

            $groups = $config->getGroups();
            if (isset($groups['zigzagbase']['fields']['username']['value'])) {
                $username = $groups['zigzagbase']['fields']['username']['value'];
            }
            if (isset($groups['zigzagbase']['fields']['password']['value'])) {
                $password = $groups['zigzagbase']['fields']['password']['value'];
            }

            if ($username && $password) {
                /**
                 * @var array|SimpleXMLElement[] $options array($shippingTypeCode => $shippingTypeName, ...)
                 */
                $result = Mage::getModel('zigzagbase/service_ws_shippingmethods')->get([
                    'UserName' => $username,
                    'Password' => $password
                ]);

                if ($result) {
                    $values = [];
                    foreach ($result as $optionId => $option) {
                        $options[] = ['value' => $optionId, 'label' => $option];
                        $values[]  = $optionId;
                    }
                    $shippingTypeCodes = implode(',', $values);
                    if ($options && ($currentShippingType !== $shippingTypeCodes)) {
                        Mage::helper('zigzagbase')->setConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_TYPES_PATH, $shippingTypeCodes);
                        Mage::helper('zigzagbase')->setConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_TYPES_FULL_PATH, json_encode($options));
                    }
                }
            }

            if (!$options) {
                Mage::helper('zigzagbase')->setConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_TYPES_PATH, null);
                Mage::helper('zigzagbase')->setConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_TYPES_FULL_PATH, null);
            }

            $this->clearCacheAndReload();
        }

        return $this;
    }

    /**
     * @return void
     */
    protected function clearCacheAndReload()
    {
        Mage::app()->getCacheInstance()->cleanType('config');
        Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => 'config'));
    }
}