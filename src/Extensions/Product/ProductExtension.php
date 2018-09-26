<?php

namespace SilverCart\ProductNotification\Extensions\Product;

use SilverCart\Model\Pages\ProductGroupPageController;
use SilverCart\ProductNotification\Model\StockNotification;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\View\ArrayData;

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
     * Adds a notification info to a product list template if the product is not
     * buyable due to stock management.
     * 
     * @param ArrayList $data Data to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2018
     */
    public function addPluggedInProductListAdditionalData(ArrayList $data)
    {
        if ($this->owner->isBuyableDueToStockManagementSettings()) {
            return;
        }
        $data->push(ArrayData::create([
            'AdditionalData' => $this->owner->renderWith(self::class . "_AdditionalData")
        ]));
    }

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
    
    /**
     * Updates the field labels.
     * 
     * @param array &$labels Labels to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2018
     */
    public function updateFieldLabels(&$labels)
    {
        $labels = array_merge(
                $labels,
                [
                    'StockNotificationTitle'    => _t(self::class . '.StockNotificationTitle', 'Get a notification as soon as this product is available for sale again.'),
                    'StockNotificationLinkText' => _t(self::class . '.StockNotificationLinkText', 'Notify as soon as available'),
                ]
        );
    }
}