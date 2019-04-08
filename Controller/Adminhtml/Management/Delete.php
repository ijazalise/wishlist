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

use Innovadeltech\Wishlist\Model\ManagementFactory;

/**
 * Class Delete
 * @package Innovadeltech\Wishlist\Controller\Adminhtml\Management
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Innovadeltech\Wishlist\Model\ManagementFactory
     */
    protected $_notificationFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param ManagementFactory $notificationFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        ManagementFactory $notificationFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_notificationFactory = $notificationFactory;
        $this->_logger = $logger;
    }

    /**
     * Delete notification action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $notificationId = (int)$this->getRequest()->getParam('id');
        if ($notificationId) {
            try {
                /** @var $notification \Innovadeltech\Wishlist\Model\Management */
                $notification = $this->_notificationFactory->create()->load($notificationId);
                $notification->delete();
                $this->messageManager->addSuccess(__('You deleted the notification.'));
            } catch (\Exception $e) {
                $this->_logger->critical($e);
                $this->messageManager->addError(__('Something went wrong while trying to delete the notification.'));
                return $resultRedirect->setPath('notification/*/', ['_current' => true]);
            }
        }
        return $resultRedirect->setPath('notification/*/');
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Innovadeltech_Wishlist::delete');
    }
}