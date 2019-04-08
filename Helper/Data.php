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
 * @subpackage Helper
 * @copyright  Copyright (c) 2018 Innovadeltech Technologies (http://www.innovadeltech.com)
 * @version    ${release.version}
 * @link       http://www.innovadeltech.com/
 * @author     Ijaz Ali <info@innovadeltech.com>
 */
namespace Innovadeltech\Wishlist\Helper;

use Magento\Framework\UrlInterface;
use Magento\Framework\Registry;

/**
 * Class Data
 * @package Innovadeltech\Wishlist\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;
    protected $_wishlist;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\ObjectManagerInterface
     */
    public function __construct(
        Registry $registry,
        UrlInterface $urlBuilder,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        \Magento\Catalog\Helper\Image $imageHelper
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_registry = $registry;
        $this->_wishlist = $wishlist;
        $this->_imageHelper = $imageHelper;
    }

    /**
     * Returns the customer edit link for the backend.
     *
     * @return string
     */
    public function getCustomerEditLink($customerId)
    {
        return $this->_urlBuilder->getUrl('customer/index/edit', ['id' => $customerId]);
    }
    
    public function getCustomerNotifyLink($itemId)
    {
        return $this->_urlBuilder->getUrl('wishlist/management/notify', ['id' => $itemId]);
    }

    /**
     * Returns the product edit link for the backend.
     * @return string
     */
    public function getProductEditLink($productId)
    {
        return $this->_urlBuilder->getUrl('catalog/product/edit', ['id' => $productId]);
    }

    /**
     * Registers objects to the registry.
     *
     * @param $key
     * @param $value
     */
    public function register($key, $value)
    {
        $this->_registry->unregister($key);
        $this->_registry->register($key, $value);
    }

    /**
     * Returns the product's image url.
     *
     * @return string
     */
    public function getProductImageSrc($product)
    {
        return $this->_imageHelper->init($product, 'product_listing_thumbnail')
            ->getUrl();
    }
    public function getCreatedAt($productId,$customerId)
    {
        $wish = $this->_wishlist->loadByCustomerId($customerId);
        $items = $wish->getItemCollection();
        foreach ($items as $item) 
        {
            if ($item->getProductId() == $productId) 
            {
                $addedAt = $item->getAddedAt();
            }
        }
        
        return $addedAt;
    }
    public function getProductQty($productId){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productStockObj = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface')->getStockItem($productId);
        return $productStockObj->getQty();
//        print_r($productStockObj->getData());
    }
    public function sendNotificationEmail(){
        
        
    }
}