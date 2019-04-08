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

/**
 * Class Edit
 * @package Innovadeltech\Wishlist\Controller\Adminhtml\Management
 */
class Notify extends \Magento\Backend\App\Action
{
    /**
     * @var \Innovadeltech\Wishlist\Model\ManagementFactory
     */
    protected $transportBuilder;    
    protected $inlineTranslation;
    protected $_notificationFactory;
    protected $scopeConfig;
    protected $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Innovadeltech\Wishlist\Model\ManagementFactory $notificationFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Innovadeltech\Wishlist\Model\ManagementFactory $notificationFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_notificationFactory = $notificationFactory;
        $this->_logger = $logger;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Innovadeltech_Wishlist::edit');
    }

    /**
     * Product edit form
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $notificationId = (int) $this->getRequest()->getParam('id');
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        /** @var $notification \Innovadeltech\Wishlist\Model\Management */
        $notification = $this->_notificationFactory->create();
        $notification->load($notificationId);
        $customerData = array();
        $customerData = $notification->getData();
        $customerId =  $customerData['customer_id'];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->create('Magento\Customer\Model\Customer')
                       ->load($customerId);
        $customerName = $customer->getData("firstname").' '.$customer->getData("lastname");
        $productId = $customerData['product_id'];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
        $imageHelper  = $objectManager->get('\Magento\Catalog\Helper\Image');
        $productThumbnail = $imageHelper->init($product, 'product_thumbnail_image')->getUrl();
        $emailVariables = array(
            'CustomerName' => $customerName,
            'CustomerEmail' => $customer->getData("email"),
            'ProductId' => $customerData['product_id'],
            'productName' => $product->getName(),
            'productImage' => $productThumbnail,
            'productUrl' => $product->getProductUrl(),
            'productPrice' => round($product->getPrice(),2)            
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
                        'ProductId' => $customerData['product_id'],
                        'productName' => $product->getName(),
                        'productImage' => $productThumbnail,
                        'productUrl' => $product->getProductUrl(),
                        'productPrice' => round($product->getPrice(),2)            
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
            $notification->setStatus('1')->save();
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
            
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Error occurred during Notifying Customer.'));
        }

    }
}