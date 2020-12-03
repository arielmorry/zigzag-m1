<?php

/**
 * Class ZigZag_Base_Helper_Data
 */
class ZigZag_Base_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param $config_path
     * @param null $store
     * @return mixed
     */
    public function getConfig($config_path, $store = null)
    {
        return Mage::getStoreConfig($config_path, $store);
    }

    /**
     * @param $config_path
     * @param $value
     */
    public function setConfig($config_path, $value)
    {
        Mage::getModel('core/config')->saveConfig($config_path, $value);
    }

    /**
     * @param null|int $shippingCode
     * @return bool
     */
    public function isShippingTypeEnabledByCarrier($shippingCode = null)
    {
        $shippingTypes = $this->getConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_TYPES_PATH);

        return $shippingTypes ? in_array($shippingCode, explode(',', $shippingTypes)) : false;
    }

    /**
     * @param $level
     * @param string $msg
     * @param null|Exception $e
     * @param bool $sendEmail
     */
    public function log($level, $msg = '', $e = null, $sendEmail = false)
    {
        $levelMap = [
            'error' => Zend_Log::ERR,
            'debug' => Zend_Log::DEBUG,
            'info'  => Zend_Log::INFO,
        ];

        $msg = is_array($msg) ? print_r($msg, true) : $msg;
        if ($e) {
            $msg .= "Error Message:\n" . $e->getMessage();
        }
        Mage::log((string)$msg, isset($levelMap[$level]) ? $levelMap[$level] : Zend_Log::ERR, 'zigzag.log', true);
        if ($sendEmail) {
            $this->sendErrorNotification($msg, $e);
        }
    }

    /**
     * @param string $msg
     * @param null|Exception $e
     */
    protected function sendErrorNotification($msg, $e)
    {
        $from = $this->getConfig('trans_email/ident_general/email');
        $name = $this->getConfig('trans_email/ident_general/name');
        $to = $this->getConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_EMAIL_PATH);

        $body = '<div><p>Message: ' . $msg . '</p>';
        if ($e) {
            $body .= '<p>Exception Details:<br>' . $e->getMessage() . '</p>';
        }
        $body .= '</div>';

        if ($to) {
            try {
                $email = new \Zend_Mail();
                $email->setSubject('Error - ZigZag Shipping Module');
                $email->setBodyHtml(nl2br($body));
                $email->setFrom($from, $name);
                $email->addTo($to);
                $email->send();
            } catch (\Zend_Mail_Exception $e) {
                $this->log('error', 'Error sending email from ZigZag Module', $e);
            }
        }
    }

    /**
     * @param string $code
     * @return string
     */
    public function getShipmentCodeByCarrierCode($code = '')
    {
        $result = '';
        if ($code) {
            $modelName = $code . '/carrier_' . str_replace('zigzag', '', $code);
            $carrierModel = Mage::getModel($modelName);
            if ($carrierModel) {
                $shipmentCode = (int)$carrierModel::ZIGZAG_SHIPPING_TYPE_CODE;
                $shipmentTypes = $this->getConfig(ZigZag_Base_Model_Carrier_Zigzag::ZIGZAG_SHIPPING_TYPES_FULL_PATH);
                foreach (json_decode($shipmentTypes) as $shipmentType) {
                    if ($shipmentCode === $shipmentType->value) {
                        $result = $shipmentType->label;
                        break;
                    }
                }
            }
        }

        return $result;
    }
}