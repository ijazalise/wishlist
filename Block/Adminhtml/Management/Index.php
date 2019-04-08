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
 * @subpackage Block
 * @copyright  Copyright (c) 2018 Innovadeltech Technologies (http://www.innovadeltech.com)
 * @version    ${release.version}
 * @link       http://www.innovadeltech.com/
 * @author     Ijaz Ali <info@innovadeltech.com>
 */
namespace Innovadeltech\Wishlist\Block\Adminhtml\Management;

use Innovadeltech\Wishlist\Model\ManagementFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Address\Mapper;

/**
 * Class Edit
 * @package Innovadeltech\Wishlist\Block\Adminhtml\Management
 */
class Index extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Innovadeltech\Wishlist\Model\Management
     */
    protected $_notification;

    /**
     * @var \Innovadeltech\Wishlist\Model\ManagementFactory
     */
    protected $_notificationFactory;

    /**
     * Account management
     *
     * @var AccountManagementInterface
     */
    protected $_accountManagement;

    /**
     * Address helper
     *
     * @var \Magento\Customer\Helper\Address
     */
    protected $_addressHelper;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistry;

    /**
     * @var \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    protected $_stockItem;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ManagementFactory $notificationFactory,
        CustomerFactory $customerFactory,
        ProductFactory $productFactory,
        AccountManagementInterface $accountManagement,
        \Magento\Customer\Helper\Address $addressHelper,
        Mapper $addressMapper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->_notificationFactory = $notificationFactory;
        $this->_customerFactory = $customerFactory;
        $this->_productFactory = $productFactory;
        $this->_accountManagement = $accountManagement;
        $this->_addressHelper = $addressHelper;
        $this->_addressMapper = $addressMapper;
        $this->_stockRegistry = $stockRegistry;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_jsonEncoder = $jsonEncoder;

        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('notification_edit');
        $this->setUseContainer(true);
        $this->_blockGroup = 'Vendor_Extension';
        $this->_headerText = __('Records');

        if ($id = $this->getRequest()->getParam('id')) {
            /** @var $notification \Innovadeltech\Wishlist\Model\Management */
            $this->_notification = $this->_notificationFactory->create()->load($id);

            $customerId = $this->_notification->getData('customer_id');
            $this->_customer = $this->_customerFactory->create()->load($customerId);

            $productId = $this->_notification->getData('product_id');
            $this->_product = $this->_productFactory->create()->load($productId);

            $this->_stockItem = $this->_stockRegistry->getStockItem(
                $this->getProduct()->getId(),
                $this->getProduct()->getStore()->getWebsiteId()
            );
        }
    }

    /**
     * Add elements in layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('notification/*/save', ['_current' => true, 'back' => null]);
    }

    /**
     * Returns the customer.
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->_customer;
    }

    /**
     * Retrieve billing address html
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getBillingAddressHtml()
    {
        try {
            $address = $this->_accountManagement->getDefaultBillingAddress($this->getCustomer()->getId());
        } catch (NoSuchEntityException $e) {
            return __('The customer does not have default billing address.');
        }

        if ($address === null) {
            return __('The customer does not have default billing address.');
        }

        return $this->_addressHelper->getFormatTypeRenderer(
            'html'
        )->renderArray(
            $this->_addressMapper->toFlatArray($address)
        );
    }

    /**
     * Returns the product.
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Returns the label for stock.
     *
     * @return string
     */
    public function getIsInStockLabel()
    {
        if ($this->_stockItem->getItemId()) {
            if ($this->_stockItem->getIsInStock()) {
                return __('In stock');
            } else {
                return __('Out of stock');
            }
        }
    }

    /**
     * Returns the product's qty.
     *
     * @return float
     */
    public function getQty()
    {
        if ($this->_stockItem->getItemId()) {
            return $this->_stockItem->getQty();
        }
    }

    /**
     * Returns the notification.
     *
     * @return \Innovadeltech\Wishlist\Model\Management
     */
    public function getNotification()
    {
        return $this->_notification;
    }

    /**
     * Returns the notification's status
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->_notification->getData('status');
    }

    /**
     * Returns the customer's wishlist.
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getWishlist()
    {
        $customerId = $this->getCustomer()->getId();
        return $this->_wishlistFactory->create()->loadByCustomerId($customerId);
    }
}