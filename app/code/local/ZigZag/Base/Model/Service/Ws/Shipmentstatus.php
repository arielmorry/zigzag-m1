<?php

class ZigZag_Base_Model_Service_Ws_Shipmentstatus extends ZigZag_Base_Model_Service_Base
{
    /**
     * Base URI for Vendor's Web Service
     */
    const WS_ENDPOINT = 'getStatusShlihutYomiAndHistoriaByNum';

    /**
     * @param string $trackingNumber
     * @return array|SimpleXMLElement[]|void
     * @throws Zend_Http_Client_Exception
     */
    public function get($trackingNumber = '')
    {
        $data = [
            'NUMERATOR_ZIGZAG' => $trackingNumber
        ];

        $response = $this->doRequest($data);
        if (!$response) {
            Mage::getSingleton('core/session')->addError('An Error Occurred. Please check zigzag.log and other log files');
            return;
        }
        return $this->parseResponse($response, $trackingNumber);
    }

    /**
     * @param Zend_Http_Response $response
     * @param string $trackingNumber
     * @return array|SimpleXMLElement[]
     */
    protected function parseResponse($response, $trackingNumber)
    {
        $tables = [];
        $code = $response->getStatus();
        if ($code == 200) {
            try {
                $xml = str_replace('xmlns="Zigzag"', '', $response->getBody());
                $sxe = new SimpleXMLElement($xml, LIBXML_NOWARNING);
                $sxe->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
                $tables = $sxe->xpath('//Table');
                if (!$tables) {
                    $msg = "Error Empty Shipping Status from ZigZag\nTracking Number: $trackingNumber";
                    Mage::helper('zigzagbase')->log('error', $msg, null, true);
                }
            } catch (Exception $e) {
                $msg = "Error Parsing Response for Shipping Status from ZigZag\nError Code: {$e->getCode()}\nError Message: {$e->getMessage()}\nTracking Number: $trackingNumber";
                Mage::helper('zigzagbase')->log('error', $msg, null, true);
            }
        } else {
            $reason = $response->getReasonPhrase();
            $msg = "Error Getting Response for Shipping Status from ZigZag\nError Code: $code\nReason: $reason\nTracking Number: $trackingNumber";
            $body = $response->getBody();
            if ($body) {
                $msg .= "\nResponse Body: $body";
            }
            Mage::helper('zigzagbase')->log('error', $msg, null, true);
        }

        return $tables;
    }
}