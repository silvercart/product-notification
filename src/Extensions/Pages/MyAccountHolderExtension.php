<?php

namespace SilverCart\ProductNotification\Extensions\Pages;

use SilverCart\ProductNotification\Model\StockNotification;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\View\ArrayData;

/**
 * Extension for MyAccountHolder.
 * 
 * @package SilverCart
 * @subpackage StockNotification_Extensions_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 23.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MyAccountHolderExtension extends DataExtension
{
    /**
     * Updates the breadcrumb items.
     * 
     * @param \SilverStripe\ORM\ArrayList $breadcrumbItems Items to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.09.2018
     */
    public function updateBreadcrumbItems($breadcrumbItems)
    {
        $ctrl = Controller::curr();
        if ($ctrl->hasMethod('isStockNotificationView')
         && $ctrl->isStockNotificationView()) {
            $title = DBText::create();
            $title->setValue(StockNotification::singleton()->fieldLabel('MyStockNotifications'));
            $breadcrumbItems->push(ArrayData::create([
                'MenuTitle' => $title,
                'Title'     => $title,
                'Link'      => $ctrl->StockNotificationLink(),
            ]));
        }
    }
}