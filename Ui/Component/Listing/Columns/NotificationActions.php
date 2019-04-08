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
namespace Innovadeltech\Wishlist\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class NotificationActions
 * @package Innovadeltech\Wishlist\Ui\Component\Listing\Columns
 */
class NotificationActions extends Column
{
    /** Url path */
    const NOTIFCATION_URL_PATH_EDIT = 'notification/notification/edit';
    const NOTIFICATION_URL_PATH_DELETE = 'notification/notification/delete';
    const NOTIFICATION_URL_PATH_NOTIFY = 'notification/notification/notify';

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;
    

    /**
     * @var string
     */
    private $_editUrl;
    private $_notifyUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::NOTIFCATION_URL_PATH_EDIT,
        $notifyUrl = self::NOTIFICATION_URL_PATH_NOTIFY
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_editUrl = $editUrl;
        $this->_notifyUrl = $notifyUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
//        echo '<pre>';
//        print_r($dataSource);
//        die('dead here.');
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->_urlBuilder->getUrl($this->_editUrl, ['id' => $item['id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['notify'] = [
                        'href' => $this->_urlBuilder->getUrl($this->_notifyUrl, ['id' => $item['id']]),
                        'label' => __('Notify Customer')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->_urlBuilder->getUrl(self::NOTIFICATION_URL_PATH_DELETE, ['id' => $item['id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete #${ $.$data.id }'),
                            'message' => __('Are you sure you wan\'t to delete notification #${ $.$data.id }?')
                        ]
                    ];
                    
                }
            }
        }

        return $dataSource;
    }
}