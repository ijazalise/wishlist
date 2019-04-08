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

/**
 * Class Save
 * @package Innovadeltech\Wishlist\Controller\Adminhtml\Management
 */
class Save extends \Magento\Backend\App\Action
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
     * Save constructor.
     * @param Action\Context $context
     * @param \Innovadeltech\Wishlist\Model\ManagementFactory $notificationFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Innovadeltech\Wishlist\Model\ManagementFactory $notificationFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_notificationFactory = $notificationFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Save notification action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $notificationId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        try {
            $notification = $this->_notificationFactory->create();
            $notification->load($notificationId);

            if (array_key_exists('notification', $data) && array_key_exists('status', $data['notification'])) {
                $notification->setData('status', $data['notification']['status']);
            }

            $notification->save();

            $this->messageManager->addSuccess(__('You saved the notification.'));
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            $this->messageManager->addError($e->getMessage());
        }

        $resultRedirect->setPath('notification/*/index');

        return $resultRedirect;
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