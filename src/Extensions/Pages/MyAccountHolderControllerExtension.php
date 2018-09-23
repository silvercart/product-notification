<?php

namespace SilverCart\ProductNotification\Extensions\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Customer;
use SilverCart\ProductNotification\Model\StockNotification;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Member;
use SilverStripe\View\ArrayData;

/**
 * Extension for the SilverCart MyAccountHolderController.
 * 
 * @package SilverCart
 * @subpackage StockNotification_Extensions_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MyAccountHolderControllerExtension extends Extension
{
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'showstocknotifications',
    ];
    
    /**
     * Updates the sub navigation.
     * 
     * @param array &$elements Elements to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.09.2018
     */
    public function updateSubNavigation(&$elements)
    {
        $originalElements = $elements['SubElements'];
        $newElements      = ArrayList::create();
        $newElements->merge($originalElements);
        $newElements->push(ArrayData::create([
            'Link'        => $this->owner->StockNotificationLink(),
            'LinkingMode' => $this->owner->isStockNotificationView() ? 'current' : 'link',
            'Title'       => StockNotification::singleton()->fieldLabel('MyStockNotifications'),
            'MenuTitle'   => StockNotification::singleton()->fieldLabel('MyStockNotifications'),
        ]));
        $elements['SubElements'] = $newElements;
    }
    
    /**
     * Returns the product history link.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.09.2018
     */
    public function StockNotificationLink()
    {
        return Tools::PageByIdentifierCode('SilvercartMyAccountHolder')->Link('showstocknotifications');
    }
    
    /**
     * Returns whether the current view is a product history view.
     * 
     * @return boolean
     */
    public function isStockNotificationView()
    {
        $isStockNotificationView = false;
        if (in_array($this->owner->getAction(), self::$allowed_actions)) {
            $isStockNotificationView = true;
        }
        return $isStockNotificationView;
    }
    
    /**
     * Renders the product history.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.09.2018
     */
    public function showstocknotifications()
    {
        return $this->owner->render();
    }
    
    /**
     * Returns the StockNotifications.
     * 
     * @return \SilverStripe\ORM\DataList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.09.2018
     */
    public function StockNotifications()
    {
        $customer = Customer::currentRegisteredCustomer();
        if ($customer instanceof Member
         && $customer->exists()
        ) {
            return StockNotification::get()->filter('MemberID', $customer->ID);
        }
    }
}