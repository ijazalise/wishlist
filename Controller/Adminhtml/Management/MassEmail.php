<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
/**
 * @category   Innovadeltech
 * @package    Innovadeltech_Wishlist
 * @subpackage Controller
 * @copyright  Copyright (c) 2018 Innovadeltech Technologies (http://www.innovadeltech.com)
 * @version    ${release.version}
 * @link       http://www.innovadeltech.com/
 * @author     Ijaz Ali <info@innovadeltech.com>
 */

namespace Innovadeltech\Wishlist\Controller\Adminhtml\Management;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 * @package Innovadeltech\Wishlist\Controller\Adminhtml\Management
 */
class MassEmail extends \Magento\Backend\App\Action {

    /**
     * Field id
     */
    const ID_FIELD = 'id';

    /**
     * Redirect url
     */
    const REDIRECT_URL = '*/*/';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $_collection = 'Innovadeltech\Wishlist\Model\ResourceModel\Management\Collection';
    protected $transportBuilder;
    protected $inlineTranslation;
    protected $storeManager;
    protected $_notificationFactory;

    /**
     * Page model
     *
     * @var string
     */
    protected $_model = 'Innovadeltech\Wishlist\Model\Management';
    protected $scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * MassDelete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
            \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Innovadeltech\Wishlist\Model\ManagementFactory $notificationFactory,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->_notificationFactory = $notificationFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_logger = $logger;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute() {
        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');        
        $id = $selected[0];        
        try {
            if (isset($excluded)) {
                if (!empty($excluded) && $excluded !== 'false') {
                    $this->excludedDelete($excluded);
                } else {
                    $this->sendAll();
                }
            } elseif (!empty($selected)) {
                $this->selectedEmail($selected);
            } else {
                $this->messageManager->addError(__('Please select item(s).'));
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            $this->messageManager->addError($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(static::REDIRECT_URL);
    }

    /**
     * Delete all
     *
     * @return void
     * @throws \Exception
     */
    protected function sendAll() {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->_collection);
        $this->setSuccessMessage($this->delete($collection));
    }

    /**
     * Delete all but the not selected
     *
     * @param array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedDelete(array $excluded) {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->_collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->setSuccessMessage($this->delete($collection));
    }

    /**
     * Delete selected items
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedEmail(array $selected) {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->_collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $imageHelper = $objectManager->get('\Magento\Catalog\Helper\Image');
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeEmail = $this->scopeConfig->getValue('trans_email/ident_general/email', $storeScope);
        $storePersonName = $this->scopeConfig->getValue('trans_email/ident_sales/name', $storeScope);

        foreach ($collection as $list) {
            $customerId = $list->getCustomerId();
            $productId = $list->getProductId();
            $status = $list->getStatus();
            $customer = $objectManager->create('Magento\Customer\Model\Customer')
                    ->load($customerId);
            $customerName = $customer->getData("firstname") . ' ' . $customer->getData("lastname");
            $product = $objectManager->get('Magento\Catalog\Model\Product')
                    ->load($productId);

            $productThumbnail = $imageHelper->init($product, 'product_thumbnail_image')->getUrl();
            $emailVariables = array(
                'CustomerName' => $customerName,
                'CustomerEmail' => $customer->getData("email"),
                'ProductId' => $productId,
                'productName' => $product->getName(),
                'productImage' => $productThumbnail,
                'productUrl' => $product->getProductUrl(),
                'productPrice' => round($product->getPrice(), 2)
            );
            try {

                /* Send email to store owner */
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $transport = $this->transportBuilder
                        ->setTemplateIdentifier($this->scopeConfig->getValue('wishlist_notification/email_template/customer', $storeScope))
                        ->setTemplateOptions(
                                [
                                    'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                                    'store' => $this->storeManager->getStore()->getId(),
                                ]
                        )
                        ->setTemplateVars([
                            'CustomerName' => $customerName,
                            'CustomerEmail' => $customer->getData("email"),
                            'ProductId' => $productId,
                            'productName' => $product->getName(),
                            'productImage' => $productThumbnail,
                            'productUrl' => $product->getProductUrl(),
                            'productPrice' => round($product->getPrice(), 2)
                        ])
                        ->setFrom([
                            'name' => $this->scopeConfig->getValue('trans_email/ident_general/name', $storeScope),
                            'email' => $this->scopeConfig->getValue('trans_email/ident_general/email', $storeScope)
                        ])
                        ->addTo($customer->getData("email"))//$customer->getEmail()
                        ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                $this->messageManager->addSuccess(__('Customer Notified successfully.'));
                $notification = $this->_notificationFactory->create();
                $notification->load($list->getId());
                $notification->setStatus('1')->save();
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Error occurred during Notifying Customer.'));
            }
        }
        $this->setSuccessMessage($this->send($collection));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Delete collection items
     *
     * @param AbstractCollection $collection
     * @return int
     */
    protected function delete(AbstractCollection $collection) {
        $count = 0;
        foreach ($collection->getAllIds() as $id) {
            /** @var \Magento\Framework\Model\AbstractModel $model */
            $model = $this->_objectManager->get($this->_model);
            $model->load($id);
            $model->delete();
            ++$count;
        }

        return $count;
    }

    protected function send(AbstractCollection $collection) {
        $count = 0;
        foreach ($collection->getAllIds() as $id) {
            /** @var \Magento\Framework\Model\AbstractModel $model */
            $model = $this->_objectManager->get($this->_model);
            $model->load($id);
            $model->delete();
            ++$count;
        }

        return $count;
    }

    /**
     * Set error messages
     *
     * @param int $count
     * @return void
     */
    protected function setSuccessMessage($count) {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Innovadeltech_Wishlist::delete');
    }

}
