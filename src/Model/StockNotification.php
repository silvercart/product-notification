<?php

namespace SilverCart\ProductNotification\Model;

use SilverCart\Dev\Tools;
use SilverCart\Model\Product\Product;
use SilverCart\Model\ShopEmail;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\Security\Member;

/**
 * StockNotification stores email addresses of interested customers (after an
 * opt-in).
 * 
 * @package SilverCart
 * @subpackage ProductNotification_Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class StockNotification extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    /**
     * Determines whether to send the opt-in email on after write or not.
     *
     * @var bool
     */
    protected $sendOptInEmail = false;
    /**
     * Table name.
     *
     * @var string
     */
    private static $table_name = 'SilvercartStockNotification';
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'Email'     => 'Varchar(254)',
        'OptInHash' => 'Varchar(72)',
        'OptInDone' => DBBoolean::class,
    ];
    /**
     * has one relations
     *
     * @var array
     */
    private static $has_one = [
        'Member'  => Member::class,
        'Product' => Product::class,
    ];
    
    /**
     * Returns the plural name.
     * 
     * @return string
     */
    public function plural_name()
    {
        return Tools::plural_name_for($this);
    }
    
    /**
     * Returns the singular name.
     * 
     * @return string
     */
    public function singular_name()
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    public function fieldLabels($includerelations = true)
    {
        $this->beforeUpdateFieldLabels(function(&$labels) {
            $labels = array_merge(
                    $labels,
                    Tools::field_labels_for(self::class),
                    [
                        'EmailConfirmationLinkLabel'       => _t(self::class . '.EmailConfirmationLinkLabel', 'Confirm email address'),
                        'EmailConfirmationLinkIgnore'      => _t(self::class . '.EmailConfirmationLinkIgnore', 'If you haven\'t requested the newsletter registration just ignore this email.'),
                        'EmailConfirmationLinkInfo'        => _t(self::class . '.EmailConfirmationLinkInfo', 'Click on the activation link or copy the link to your browser please.'),
                        'EmailNotificationGoToProduct'     => _t(self::class . '.EmailNotificationGoToProduct', 'Go to product'),
                        'EmailNotificationHelloAnonymous'  => _t(self::class . '.EmailNotificationHelloAnonymous', 'Hello!'),
                        'EmailTitleStockNotification'      => _t(self::class . '.EmailTitleStockNotification', '"{$Product.Title}" is available for delivery now!'),
                        'EmailTitleStockNotificationOptIn' => _t(self::class . '.EmailTitleStockNotificationOptIn', 'Please confirm your email address'),
                        'MyStockNotifications'             => _t(self::class . '.MyStockNotifications', 'My Notifications'),
                        'NoStockNotifications'             => _t(self::class . '.NoStockNotifications', 'You don\'t have any notifications right now.'),
                    ]
            );
        });
        return parent::fieldLabels($includerelations);
    }
    
    /**
     * Creates the opt-in hash before writing a new record to database.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->exists()
         && !empty($this->Email)
         && !$this->OptInDone
         && empty($this->OptInHash)
        ) {
            $this->OptInHash      = md5(uniqid($this->Email)) . sha1(uniqid($this->Email));
            $this->sendOptInEmail = true;
        }
    }
    
    /**
     * Sends the opt-in email after writing a new record to database.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    protected function onAfterWrite()
    {
        parent::onAfterWrite();
        if ($this->sendOptInEmail) {
            $this->sendOptInEmail();
            $this->sendOptInEmail = false;
        }
    }
    
    /**
     * Sends the opt-in email.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    public function sendOptInEmail()
    {
        ShopEmail::send('StockNotificationOptIn', $this->Email, [
                'Notification'     => $this,
                'Product'          => $this->Product(),
                'Email'            => $this->Email,
                'ConfirmationLink' => Director::absoluteURL("sc-product-notification/opt-in/{$this->ID}/{$this->OptInHash}")
            ]
        );
    }
    
    /**
     * Sends the notification email.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    public function sendNotificationEmail()
    {
        if (!$this->OptInDone) {
            return;
        }
        ShopEmail::send('StockNotification', $this->Email, [
                'Notification' => $this,
                'Product'      => $this->Product(),
                'Member'       => $this->Member(),
                'Email'        => $this->Email
            ]
        );
        $this->delete();
    }
}