<?php
/**
 * Google Tag Manager Block
 *
 * @category    KL
 * @package     KL_GoogleTagManager
 * @since       0.1.0.0
 * @author      Erik Eng <erik@karlssonlord.com>
 */
class KL_GoogleTagManager_Block_Gtm extends Mage_Core_Block_Template
{

    protected $_isCheckoutSuccess = false;

    /**
     * Is Active
     *
     * @since     0.1.0.0
     * @return    bool
     */
    private function _isActive()
    {
        return Mage::getStoreConfig('google/tagmanager/active') &&
            Mage::getStoreConfig('google/tagmanager/container');
    }

    /**
     * Get Data Layer
     *
     * @since     0.1.0.0
     * @return    void
     *
     * @see https://support.google.com/tagmanager/answer/3002596
     */
    private function _getDataLayer()
    {
        if($orderId = Mage::getSingleton('checkout/session')->getLastOrderId()) {
            $order = Mage::getModel('sales/order')->load($orderId);
        }

        if(isset($order) && $order->getId()) {
            $data = array(
                'transactionId'          => $order->getIncrementId(),
                'transactionAffiliation' => Mage::app()->getStore()->getName(),
                'transactionTotal'       => number_format($order->getBaseGrandTotal(), 2, '.', ''),
                'transactionShipping'    => number_format($order->getBaseShippingAmount(), 2, '.', ''),
                'transactionTax'         => number_format($order->getBaseTaxAmount(), 2, '.', ''),
                'transactionProducts'    => array());

            foreach($order->getAllItems() as $item) {
                $data['transactionProducts'][] = array(
                    'name'     => $item->getName(),
                    'sku'      => $item->getSku(),
                    'price'    => number_format($item->getBasePrice(), 2, '.', ''),
                    'quantity' => $item->getQtyOrdered());
            }

            $this->setDataLayer($data);
        }
    }

    /**
     * To Html
     *
     * @since     0.1.0.0
     * @return    string
     */
    protected function _toHtml()
    {
        if(!$this->_isActive()) return '';

        $this->setContainerId(Mage::getStoreConfig('google/tagmanager/container'));
        $this->_getDataLayer();

        return parent::_toHtml();
    }
}