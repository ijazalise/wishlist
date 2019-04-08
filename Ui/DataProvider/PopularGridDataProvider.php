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

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class NotificationGridDataProvider
 * @package Innovadeltech\Wishlist\Ui\DataProvider
 */
class PopularGridDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    
    protected $_collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->_collection = $collectionFactory->create();
        $this->modifyCollection();
    }

    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }

        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => count($items),
            'items' => array_values($items),
        ];
    }

    public function getCollection()
    {
        return $this->_collection;
    }

    private function modifyCollection()
    {
        $collection = $this->getCollection();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('thumbnail');
        $collection->joinField(
            'qtyStock',
            $collection->getTable('cataloginventory_stock_item'),
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );
        $collection->joinField(
            'wi',
            $collection->getTable('wishlist_item'),
            'product_id',
            'product_id=entity_id'
        );
//        echo $collection->getSelect()->__ToString();
//        $collection->addFieldToSort('count Desc');
        $select = $collection->getSelect();
        $select->columns('count(*) as count');
//        $select->order('count DESC');
        $select->group('at_wi.product_id');
        
//        echo $select->__ToString();
//        die('dead.');        
    }
}
