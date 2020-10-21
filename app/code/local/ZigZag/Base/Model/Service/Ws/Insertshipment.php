<?php

class ZigZag_Base_Model_Service_Ws_Insertshipment extends ZigZag_Base_Model_Service_Base
{
    /**
     * Base URI for Vendor's Web Service
     */
    const WS_ENDPOINT = 'INSERT_SHLIHUT';

    /**
     * @param $order
     * @param $carrier
     * @param bool $checkStatus
     * @return string|void
     * @throws Zend_Http_Client_Exception
     */
    public function insert($order, $carrier = null, $checkStatus = true)
    {
        if ($carrier && $checkStatus) {
            $orderStatus     = $order->getStatus();
            $configStatuses  = Mage::helper('zigzagbase')->getConfig($carrier::ZIGZAG_SHIPPING_ORDER_STATUSES_PATH);
            $allowedStatuses = array();

            if ($configStatuses) {
                $allowedStatuses = explode(',', $configStatuses);
            }

            if (!in_array($orderStatus, $allowedStatuses)) {
                return;
            }
        }

        $shippingType = $carrier ? $carrier::ZIGZAG_SHIPPING_TYPE_CODE : 0;
        $shippingAddress = $order->getShippingAddress();

        $street = implode(' ', $shippingAddress->getStreet());
        preg_match('!\d+!', $street, $matches);
        if(isset($matches[0]) && $matches[0]){
            $houseNumber = $matches[0];
        } else {
            $houseNumber = 0;
        }

        $ownerPhone = Mage::getStoreConfig(
            Mage_Core_Model_Store::XML_PATH_STORE_STORE_PHONE,
            $order->getStoreId()
        );

        $data = [
            'KOD_KIVUN'              => 1,
            'MOSER'                  => '',
            'HEVRA_MOSER'            => '',
            'TEL_MOSER'              => $ownerPhone ? preg_replace('/[^0-9]/', '', $ownerPhone) : '',
            'EZOR_MOSER'             => 0,
            'SHM_EIR_MOSER'          => '',
            'REHOV_MOSER'            => '',
            'MISPAR_BAIT_MOSER'      => 0,
            'koma_MOSER'             => '',
            'MEKABEL'                => $shippingAddress->getName(),
            'HEVRA_MEKABEL'          => $shippingAddress->getCompany() ? $shippingAddress->getCompany() : '',
            'TEL_MEKABEL'            => preg_replace('/[^0-9]/', '', $shippingAddress->getTelephone()),
            'EZOR_MEKABEL'           => 0,
            'SHM_EIR_MEKABEL'        => $shippingAddress->getCity(),
            'REHOV_MEKABEL'          => $street,
            'MISPAR_BAIT_MEKABEL'    => '',
            'koma_MEKABEL'           => '',
            'SUG_SHLIHUT'            => $shippingType,
            'HEAROT'                 => '',
            'SHEM_MAZMIN'            => '',
            'MICROSOFT_ORDER_NUMBER' => '',
            'HEAROT_LKTOVET_MKOR'    => '',
            'HEAROT_LKTOVET_YAAD'    => '',
            'SHEM_CHEVRA'            => '',
            'TEOR_TKALA'             => '',
            'KARTONIM'               => '',
        ];

        $response = $this->doRequest($data);
        if (!$response) {
            Mage::getSingleton('core/session')->addError('An Error Occurred. Please check zigzag.log and other log files');
            return;
        }
        return $this->parseResponse($response, $order, $data);
    }

    /**
     * @param Zend_Http_Response $response
     * @param $order
     * @param $data
     * @return string
     */
    protected function parseResponse($response, $order, $data)
    {
        $tracking = '';
        $code     = $response->getStatus();
        if ($code == 200) {
            try {
                $xml   = str_replace('xmlns="Zigzag"', '', $response->getBody());
                $sxe   = new SimpleXMLElement($xml, LIBXML_NOWARNING);
                $value = (string)$sxe;
                if (strlen($value) > 4) {
                    $tracking = $value;
                    Mage::dispatchEvent('zigzag_shipment_created', ['order' => $order, 'tracking_number' => $tracking]);
                } else {
                    $code = print_r($value, true);
                    $msg  = "Error Code Response for Insert Shipping to ZigZag\nResponse From ZigZag: $code";
                    Mage::helper('zigzagbase')->log('error', $msg, null, true);
                }
            } catch (Exception $e) {
                $msg = "Error Parsing Response for Insert Shipping to ZigZag\nError Code: {$e->getCode()}\nError Message: {$e->getMessage()}\nOrder Number: {$order->getIncrementId()}\nData Sent:\n" . print_r($data, true);
                Mage::helper('zigzagbase')->log('error', $msg, null, true);
            }
        } else {
            $reason = $response->getMessage();
            $msg    = "Error Getting Response for Insert Shipping to ZigZag\nError Code: $code\nReason: $reason\nOrder Number: {$order->getIncrementId()}\nData Sent:\n" . print_r($data, true);
            $body =  $response->getBody();
            if ($body) {
                $msg .= "\nResponse Body: $body";
            }
            Mage::helper('zigzagbase')->log('error', $msg, null, true);
        }

        return $tracking;
    }
}