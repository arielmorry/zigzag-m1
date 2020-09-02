<?php
require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Sales' . DS . 'OrderController.php';

class ZigZag_Base_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'orders.csv';
        /** @var Mage_Adminhtml_Block_Sales_Order_Grid $grid */
        $grid = $this->getLayout()->createBlock('adminhtml/sales_order_grid');

        $grid->addColumnAfter('track', array(
            'header' => 'ZigZag Tracking Number',
            'index' => 'track',
            'filter_index' => 'track',
            'type' => 'text',
        ), 'shipping_name');

        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_order_grid');

        $grid->addColumnAfter('track', array(
            'header' => 'ZigZag Tracking Number',
            'index' => 'track',
            'filter_index' => 'track',
            'type' => 'text',
        ), 'shipping_name');

        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}