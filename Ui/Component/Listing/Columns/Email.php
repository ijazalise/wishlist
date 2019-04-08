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
class Email extends \Magento\Ui\Component\Listing\Columns\Column
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
                if ($item['w']) {
                $customerID = $item['w']; // your customer-id
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $customerObj = $objectManager->create('Magento\Customer\Model\Customer')
                            ->load($customerID);
                $customerEmail = $customerObj->getData("email");
                $item['customerEmail'] = $customerEmail;
                    continue;
                }
            }
        }
//        echo '<pre>';
//        print_r();
        return $dataSource;
    }
}