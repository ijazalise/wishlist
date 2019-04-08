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
 * @subpackage Ui
 * @copyright  Copyright (c) 2018 Innovadeltech Technologies (http://www.innovadeltech.com)
 * @version    ${release.version}
 * @link       http://www.innovadeltech.com/
 * @author     Ijaz Ali <info@innovadeltech.com>
 */
namespace Innovadeltech\Wishlist\Ui\Component\Management\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Thumbnail
 * @package Innovadeltech\Wishlist\Ui\Component\Management\Columns
 */
class Sku extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
                $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $productId = $item['product_id'];
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $productObj = $objectManager->get('Magento\Catalog\Model\Product')
                                ->load($productId);
                $imageHelper  = $objectManager->get('\Magento\Catalog\Helper\Image');
                $productUrl = $imageHelper->init($productObj, 'product_thumbnail_image')->getUrl();
                $item[$fieldName . '_src'] = $productUrl;
                $item[$fieldName . '_alt'] = 'Product image';
                $item[$fieldName . '_orig_src'] = $productUrl;
                $stockManager = \Magento\Framework\App\ObjectManager::getInstance();
                $StockState = $stockManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
                $qtyStock = $StockState->getStockQty($productId, $productObj->getStore()->getWebsiteId());
                $item['qtyStock'] = $qtyStock;
                $item['name'] = $productObj->getName();
                $item['price'] = $productObj->getPrice();
                $item['special_price'] = $productObj->getSpecialPrice();
//                $item['sku'] = $productObj->getSku();
//                $item[$fieldName . '_src'] = $item['image_url'];
                
                
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'catalog/product/edit',
                    ['id' => $item['product_id'], 'store' => $this->context->getRequestParam('store')]
                );
            }
        }
//            echo '<pre>';
//            print_r($dataSource);
//            die('dead here.');

        return $dataSource;
    }
}