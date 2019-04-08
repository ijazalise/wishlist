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
 * @subpackage Model
 * @copyright  Copyright (c) 2018 Innovadeltech Technologies (http://www.innovadeltech.com)
 * @version    ${release.version}
 * @link       http://www.innovadeltech.com/
 * @author     Ijaz Ali <info@innovadeltech.com>
 */
namespace Innovadeltech\Wishlist\Model\ResourceModel;

/**
 * Class Notification
 * @package Innovadeltech\Wishlist\Model\ResourceModel
 */
class Management extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('innovadeltech_wishlist', 'id');
    }

    /**
     * Update attribute values for notifications
     *
     * @param array $entityIds
     * @param array $attrData
     * @return $this
     * @throws \Exception
     */
    public function updateAttributes($entityIds, $attrData)
    {
        $connection = $this->_getConnection('default');
        $connection->beginTransaction();
        try {
            foreach ($entityIds as $id) {
                $connection->update(
                    $this->getMainTable(),
                    $attrData,
                    $connection->quoteInto($this->getIdFieldName() . ' = ?', $id)
                );
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return $this;
    }
}