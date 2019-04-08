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
namespace Innovadeltech\Wishlist\Ui\DataProvider;

use Innovadeltech\Wishlist\Model\ResourceModel\Management\CollectionFactory;
//use Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory;

/**
 * Class NotificationGridDataProvider
 * @package Innovadeltech\Wishlist\Ui\DataProvider
 */
class ManagementGridDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Notification collection
     *
     * @var \Innovadeltech\Wishlist\Model\ResourceModel\Management\Collection
     */
    protected $_collection;
    protected $_wishlist;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishlist,    
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->_collection = $collectionFactory->create();
        $this->_wishlist = $wishlist->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->addFieldToSelect('*')->load();
        }
        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items['items']),
        ];
    }

    /**
     * @return \Innovadeltech\Wishlist\Model\ResourceModel\Management\Collection
     */
    public function getCollection()
    {
        
//        die('dead.');
//        echo $this->_collection->getSelect()->__ToString();
        return $this->_collection;
    }
}