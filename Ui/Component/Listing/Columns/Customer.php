<?php
/**
 * @author Ijaz Team
 * @copyright Copyright (c) 2018 Ijaz (https://www.innovadeltech.com)
 * @package Ijaz_Wishlist
 */

namespace Innovadeltech\Wishlist\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Thumbnail
 * @package Magenerds\WishlistNotification\Ui\Component\Listing\Columns
 */
class Customer extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item['customer_id']) {
                $customerID = $item['customer_id']; // your customer-id
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $customerObj = $objectManager->create('Magento\Customer\Model\Customer')
                            ->load($customerID);
                $customerName = $customerObj->getData("firstname").' '.$customerObj->getData("lastname");
                $item['customerName'] = $customerName;
                $item['customerEmail'] = $customerObj->getData("email");
                    continue;
                }
            }
        }
//        echo '<pre>';
//        print_r($dataSource);
//        die('dead here.');
        return $dataSource;
    }
}