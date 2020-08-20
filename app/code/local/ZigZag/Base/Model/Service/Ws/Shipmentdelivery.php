<?php

class ZigZag_Base_Model_Service_Ws_Shipmentdelivery extends ZigZag_Base_Model_Service_Base
{
    /**
     * Base URI for Vendor's Web Service
     */
    const WS_ENDPOINT = 'UpdateTaom';

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string $trackingNumber
     * @return bool|SimpleXMLElement[]
     * @throws Zend_Http_Client_Exception
     */
    public function set($order, $trackingNumber = '')
    {

        $date = date('Y-m-d', strtotime($order->getZigzagDeliveryFrom()));
        $from = date('His', strtotime($order->getZigzagDeliveryFrom()));
        $to = date('His', strtotime($order->getZigzagDeliveryTo()));
        $data = [
            'Numerator' => $trackingNumber,
            'TaarichTeum' => $date,
            'FromTimeTeum' => $from,
            'ToTimeTeum' => $to,
        ];

        $response = $this->doRequest($data);
        return $this->parseResponse($response, $order);
    }

    /**
     * @param Zend_Http_Response $response
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    protected function parseResponse($response, $order)
    {
        $result = false;
        $code = $response->getStatus();
        if ($code == 200) {
            try {
                $xml = str_replace('xmlns="Zigzag"', '', $response->getBody());
                $sxe = new SimpleXMLElement($xml, LIBXML_NOWARNING);
                $value = (string)$sxe;
                if(is_numeric($value) && $value == 1) {
                    $result = true;
                } else {
                    $code = print_r($value, true);
                    $msg = "Error Update Shipping Delivery Date Time to ZigZag\nResponse From ZigZag: $code";
                    $this->_helper->log('error', $msg, null, true);
                }
            } catch (Exception $e) {
                $msg = "Error Parsing Response for Shipping Delivery Date Time to ZigZag\nError Code: {$e->getCode()}\nError Message: {$e->getMessage()}\nOrder Number: {$order->getIncrementId()}";
                Mage::helper('zigzagbase')->log('error', $msg, null, true);
            }
        } else {
            $reason = $response->getReasonPhrase();
            $msg    = "Error Getting Response for Shipping Delivery Date Time to ZigZag\nError Code: $code\nReason: $reason\nOrder Number: {$order->getIncrementId()}";
            $body =  $response->getBody();
            if ($body) {
                $msg .= "\nResponse Body: $body";
            }
            Mage::helper('zigzagbase')->log('error', $msg, null, true);
        }

        return $result;
    }
}