<?php

namespace Innovadeltech\Wishlist\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
	protected $_notificationFactory;

	public function __construct(
                \Innovadeltech\Wishlist\Model\ManagementFactory $notificationFactory                 
                )
	{
		$this->_notificationFactory = $notificationFactory;
               
	}

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $sql = "select wishlist_item_id,customer_id,product_id from wishlist_item 
                    INNER JOIN wishlist ON wishlist_item.wishlist_id = wishlist.wishlist_id";
            $result = $connection->fetchAll($sql); 
           // echo '<pre>'; print_r($result); echo '</pre>';  
            //echo count($result);
            for($i=0; $i<=count($result)-1; $i++ ){
                $data = [
                    'id'         => $result[$i]['wishlist_item_id'],
                    'customer_id'         => $result[$i]['customer_id'],
                    'product_id'         => $result[$i]['product_id'],
                    'status'       => 0
                ];
                
                $wishList = $this->_notificationFactory->create();
		        $wishList->addData($data)->save();
            }
		
	}
}