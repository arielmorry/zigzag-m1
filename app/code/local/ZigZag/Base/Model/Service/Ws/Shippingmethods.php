<?php

class ZigZag_Base_Model_Service_Ws_Shippingmethods extends ZigZag_Base_Model_Service_Base
{
    /**
     * Base URI for Vendor's Web Service
     */
    const WS_ENDPOINT = 'GetSugeyShlihuyot';

    /**
     * @param $params
     * @return array|SimpleXMLElement[]|void
     * @throws Zend_Http_Client_Exception
     */
    public function get($params = [])
    {
        $response = $this->doRequest($params);
        if (!$response) {
            Mage::getSingleton('core/session')->addError('An Error Occurred. Please check zigzag.log and other log files');
            return;
        }
        return $this->parseResponse($response);
    }

    /**
     * @param Zend_Http_Response $response
     * @return array|SimpleXMLElement[]
     */
    protected function parseResponse($response)
    {
        $options = [];
        $code     = $response->getStatus();
        if ($code == 200) {
            try {
                $xml = str_replace('xmlns="Zigzag"', '', $response->getBody());
                $sxe    = new SimpleXMLElement($xml, LIBXML_NOWARNING);
                $sxe->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
                $tables = $sxe->xpath('//Table');
                foreach ($tables as $t) {
                    $options[(int)$t->KOD] = (string)$t->TEUR;
                }
            } catch (Exception $e) {
                $msg = "Error Parsing Response for Shipping Types from ZigZag\nError Code: {$e->getCode()}\nError Message: {$e->getMessage()}";
                Mage::helper('zigzagbase')->log('error', $msg, null, true);
            }
        } else {
            $reason = $response->getMessage();
            $msg    = "Error Getting Response for Shipping Types from ZigZag\nError Code: $code\nReason: $reason";
            $body =  $response->getBody();
            if ($body) {
                $msg .= "\nResponse Body: $body";
            }
            Mage::helper('zigzagbase')->log('error', $msg, null, true);
        }

        return $options;
    }
}