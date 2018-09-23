<?php

namespace SilverCart\ProductNotification\Control;

use SilverCart\ProductNotification\Model\StockNotification;
use SilverStripe\Control\Controller;

/**
 * Controller to handle the email opt-in.
 * 
 * @package SilverCart
 * @subpackage ProductNotification_Control
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class NotificationController extends Controller
{
    /**
     * Handles the opt-in.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.09.2018
     */
    protected function init()
    {
        parent::init();
        $request          = $this->getRequest();
        $notificationID   = $request->param('OptInID');
        $notificationHash =  $request->param('OptInHash');
        $notification     = StockNotification::get()->filter('OptInHash', $notificationHash)->byID($notificationID);
        if ($notification instanceof StockNotification
         && $notification->exists()
        ) {
            $notification->OptInDone = true;
            $notification->write();
            $this->redirect($notification->Product()->Link());
        }
    }
}