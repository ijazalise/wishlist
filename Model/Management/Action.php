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
namespace Innovadeltech\Wishlist\Model\Management;

/**
 * Class Action
 * @package Innovadeltech\Wishlist\Model\Management
 */
class Action extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Innovadeltech\Wishlist\Model\ResourceModel\Management');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return \Innovadeltech\Wishlist\Model\ResourceModel\Management
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Update attribute values for entity list per store
     *
     * @param array $notificationIds
     * @param array $attrData
     * @return $this
     */
    public function updateAttributes($notificationIds, $attrData)
    {
        $this->_getResource()->updateAttributes($notificationIds, $attrData);

        return $this;
    }
}