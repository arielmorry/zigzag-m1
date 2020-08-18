<?php

class ZigZag_Base_Model_Service_Base
{
    /**
     * Base URI for Vendor's Web Service
     */
    const WS_BASE_URI = 'https://api.zig-zag.co.il/ZigZag_WS3/Service.asmx/';

    /**
     * @param array $params
     * @param string $requestMethod
     * @return Zend_Http_Response|void
     * @throws Zend_Http_Client_Exception
     */
    protected function doRequest(
        array $params = [],
        $requestMethod = Varien_Http_Client::POST)
    {
        if (isset($params['username']) && isset($params['password'])) {
            $credentials = [
                'UserName' => $params['username'],
                'Password' => $params['password'],
            ];
        } else {
            $credentials = [
                'UserName' => Mage::helper('zigzagbase')->getConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_USERNAME_PATH),
                'Password' => Mage::helper('zigzagbase')->getConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_PASSWORD_PATH),
            ];
        }

        $data = array_merge($credentials, $params);

        $client = new Varien_Http_Client();
        $client->setUri(self::WS_BASE_URI . static::WS_ENDPOINT);

        try {
            $client->setMethod(Zend_Http_Client::POST);

            foreach ($data as $key => $value) {
                if ($requestMethod == Varien_Http_Client::GET) {
                    $client->setParameterGet($key, $value);
                } else {
                    $client->setParameterPost($key, $value);
                }
            }

            return $client->request();

        } catch (\Exception $e) {
            $msg = "Error while making request to ZigZag";
            Mage::helper('zigzagbase')->log('error', $msg, $e, true);
        }
    }
}