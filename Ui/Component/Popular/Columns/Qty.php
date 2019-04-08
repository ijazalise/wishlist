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
namespace Innovadeltech\Wishlist\Ui\Component\Popular\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Status
 * @package Innovadeltech\Wishlist\Ui\Component\Management\Columns
 */
class Qty extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $qty = $item['qtyStock'];
                $item['qtyStock']= number_format($qty,0);
            }
        }
//        echo '<pre>';
//        print_r($dataSource);

        return $dataSource;
    }
}