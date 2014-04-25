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
        $_checkoutSession = Mage::getSingleton('checkout/session');

        $orderId = $_checkoutSession->getLastOrderId();

        if($_checkoutSession->getLastTransaction() === $orderId) return;
        $_checkoutSession->setLastTransaction($orderId);

        if($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
        }

        if(isset($order) && $order->getId()) {
            $data = array(
                'event'                  => 'transaction',
                'transactionId'          => $order->getIncrementId(),
                'transactionAffiliation' => Mage::app()->getStore()->getName(),
                'transactionCurrency'    => $order->getBaseCurrencyCode(),
                'transactionTotal'       => number_format($order->getBaseGrandTotal(), 2, '.', ''),
                'transactionShipping'    => number_format($order->getBaseShippingAmount(), 2, '.', ''),
                'transactionTax'         => number_format($order->getBaseTaxAmount(), 2, '.', ''),
                'transactionProducts'    => array());

            foreach($order->getAllItems() as $item) {
                $data['transactionProducts'][] = array(
                    'name'     => $item->getName(),
                    'sku'      => $item->getSku(),
                    'price'    => number_format($item->getBasePrice(), 2, '.', ''),
                    'quantity' => number_format($item->getQtyOrdered(), 2, '.', ''));
            }

            $customerSession = Mage::getSingleton('customer/session');
            if($customerSession->isLoggedIn()) {
                $data['customerId']  = $customerSession->getCustomer()->getId();
                $data['nthPurchase'] = 1;

                // Get non canceled orders by customer except for this one
                $_ordersCollection = Mage::getModel('sales/order')->getCollection()
                    ->addFieldToFilter('customer_id', $data['customerId'])
                    ->addFieldToFilter('increment_id', array('neq' => $data['transactionId']))
                    ->addFieldToFilter('status', array('nin' => array('canceled', 'pay_aborted')))
                    ->setOrder('created_at');
                if($_ordersCollection && $_ordersCollection->count()) {
                    $data['nthPurchase'] += $_ordersCollection->count();

                    $_lastOrderDate = new \DateTime($_ordersCollection->getFirstItem()->getCreatedAt());
                    $_nowDate       = new \DateTime(Mage::getModel('core/date')->date());
                    $data['daysSinceLastTransaction'] = $_lastOrderDate->diff($_nowDate)->format('%a');
                }
            }
            else {
                $data['nthPurchase'] = 0;
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