<?php

namespace SilverCart\ProductNotification\Extensions\Pages;

use SilverCart\ProductNotification\Forms\StockNotificationOptInForm;
use SilverStripe\Core\Extension;

/**
 * Extension for SilverCart ProductGroupPageController.
 * 
 * @package SilverCart
 * @subpackage ProductNotification_Extensions_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupPageControllerExtension extends Extension
{
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'StockNotificationOptInForm',
    ];
    
    /**
     * Returns the StockNotificationOptInForm.
     * 
     * @return StockNotificationOptInForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    public function StockNotificationOptInForm()
    {
        return StockNotificationOptInForm::create($this->owner->getProduct(), $this->owner, 'StockNotificationOptInForm')->setFormAction($this->owner->OriginalLink('StockNotificationOptInForm'));
    }
}