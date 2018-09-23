<?php

namespace SilverCart\ProductNotification\Extensions\Product;

use SilverCart\Model\Pages\ProductGroupPageController;
use SilverCart\ProductNotification\Model\StockNotification;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataExtension;

/**
 * Extension for SilverCart Product.
 * 
 * @package SilverCart
 * @subpackage ProductNotification_Extensions_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductExtension extends DataExtension
{
    /**
     * Has many relations.
     *
     * @var array
     */
    private static $has_many = [
        'StockNotifications' => StockNotification::class,
    ];

    /**
     * Sends email notifications if the stock changes from 0 to > 0.
     * 
     * @param double $oldStockQuantity Old stock quantity before update
     * @param double $newStockQuantity New stock quantity after update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    public function onAfterUpdateStockQuantity($oldStockQuantity, $newStockQuantity)
    {
        if ((double) $oldStockQuantity == 0
         && (double) $newStockQuantity > 0
        ) {
            foreach ($this->owner->StockNotifications() as $notification) {
                $notification->sendNotificationEmail();
            }
        }
    }
    
    /**
     * Updates the content to render right after a product is out of stock
     * message.
     * 
     * @param string $content Content to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    public function updateAfterOutOfStockNotificationContent(&$content)
    {
        $ctrl = Controller::curr();
        if ($ctrl instanceof ProductGroupPageController) {
            $form = $ctrl->StockNotificationOptInForm();
            /* @var $form \SilverCart\ProductNotification\Forms\StockNotificationOptInForm */
            $content .= $form->forTemplate();
        }
    }
}