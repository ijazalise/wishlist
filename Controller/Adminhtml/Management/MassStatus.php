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
 * @subpackage Controller
 * @copyright  Copyright (c) 2018 Innovadeltech Technologies (http://www.innovadeltech.com)
 * @version    ${release.version}
 * @link       http://www.innovadeltech.com/
 * @author     Ijaz Ali <info@innovadeltech.com>
 */
namespace Innovadeltech\Wishlist\Controller\Adminhtml\Management;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassStatus
 * @package Innovadeltech\Wishlist\Controller\Adminhtml\Management
 */
class MassStatus extends \Magento\Backend\App\Action
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Innovadeltech\Wishlist\Model\Management\Action
     */
    protected $_action;

    /**
     * MassStatus constructor.
     * @param Action\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Innovadeltech\Wishlist\Model\Management\Action $action
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Innovadeltech\Wishlist\Model\Management\Action $action
    ) {
        parent::__construct($context);
        $this->_logger = $logger;
        $this->_action = $action;
    }

    /**
     * Update notification status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $notificationIds = (array) $this->getRequest()->getParam('selected');
        $status = (int) $this->getRequest()->getParam('status');

        try {
            $this->_action->updateAttributes($notificationIds, ['status' => $status]);
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', count($notificationIds)));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            $this->_getSession()->addException($e, __('Something went wrong while updating the notification(s) status.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('notification/*/', ['_current' => true]);
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Innovadeltech_Wishlist::save');
    }
}