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

/**
 * Class Edit
 * @package Innovadeltech\Wishlist\Controller\Adminhtml\Management
 */
class Edit extends \Magento\Backend\App\Action
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
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Innovadeltech\Wishlist\Model\ManagementFactory $notificationFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Innovadeltech\Wishlist\Model\ManagementFactory $notificationFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_notificationFactory = $notificationFactory;
        $this->_logger = $logger;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Innovadeltech_Wishlist::edit');
    }

    /**
     * Product edit form
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $notificationId = (int) $this->getRequest()->getParam('id');

        /** @var $notification \Innovadeltech\Wishlist\Model\Management */
        $notification = $this->_notificationFactory->create();

        if ($notificationId) {
            try {
                $notification->load($notificationId);
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }

        if ($notificationId && !$notification->getId()) {
            $this->messageManager->addError(__('This notification no longer exists.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('notification/*/');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Innovadeltech_Wishlist::notification');
        $resultPage->getConfig()->getTitle()->prepend(__('Notification'));
        $resultPage->getConfig()->getTitle()->prepend($notification->getName());

        return $resultPage;
    }
}