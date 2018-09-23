<?php

namespace SilverCart\ProductNotification\Forms;

use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\EmailField;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Product\Product;
use SilverCart\ProductNotification\Model\StockNotification;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Security\Member;

/**
 * 
 * @package SilverCart
 * @subpackage ProductNotification_Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class StockNotificationOptInForm extends CustomForm
{
    const SESSION_KEY       = 'SilverCart.StockNotificationOptInForm';
    const SESSION_KEY_EMAIL = 'SilverCart.StockNotificationOptInForm.Email';
    /**
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = [
        'Email' => [
            'isFilledIn'     => true,
            'isEmailAddress' => true,
        ],
    ];
    /**
     * Product.
     *
     * @var Product 
     */
    protected $product = null;
    /**
     * Existing StockNotification for the product context and logged in customer.
     *
     * @var StockNotification 
     */
    protected $existingStockNotification = null;
    /**
     * Current logged in customer already requested a notification?
     *
     * @var bool
     */
    protected $alreadyRequested = null;
    /**
     * Current logged in customer already confirmed a notification request?
     *
     * @var bool
     */
    protected $confirmed = null;

    /**
     * 
     * @param Product $product
     * @param \SilverStripe\Control\RequestHandler $controller
     * @param type $name
     * @param \SilverStripe\Forms\FieldList $fields
     * @param \SilverStripe\Forms\FieldList $actions
     * @param \SilverStripe\Forms\Validator $validator
     */
    public function __construct(Product $product = null, \SilverStripe\Control\RequestHandler $controller = null, $name = self::DEFAULT_NAME, \SilverStripe\Forms\FieldList $fields = null, \SilverStripe\Forms\FieldList $actions = null, \SilverStripe\Forms\Validator $validator = null)
    {
        if (is_null($product)
         && array_key_exists('ProductID', $_POST)) {
            $product = Product::get()->byID((int) $_POST['ProductID']);
        }
        $this->setProduct($product);
        parent::__construct($controller, $name, $fields, $actions, $validator);
    }
    /**
     * Returns the required fields.
     * 
     * @return array
     */
    public function getRequiredFields()
    {
        $requiredFields = parent::getRequiredFields();
        $email          = $this->getEmail();
        if (!is_null($email)) {
            $requiredFields = [];
        }
        return $requiredFields;
    }

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields()
    {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $productID  = 0;
            $product    = $this->getProduct();
            $member     = Customer::currentRegisteredCustomer();
            $emailField = EmailField::create('Email', Address::singleton()->fieldLabel('Email'))
                    ->setPlaceholder(Address::singleton()->fieldLabel('Email'));
            if ($product instanceof Product
             && $product->exists()
            ) {
                $productID = $product->ID;
            }
            if ($member instanceof Member
             && $member->exists()
            ) {
                $emailField->setValue($member->Email)
                        ->setReadonly(true)
                        ->setDisabled(true);
            }
            $fields[] = $emailField;
            $fields[] = HiddenField::create('ProductID', 'ProductID', $productID);
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions()
    {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $btnTitle = $this->fieldLabel('NotifyByEmailButton');
            if ($this->AlreadyRequested()) {
                $btnTitle = $this->fieldLabel('NotifyByEmailButtonResendOptIn');
            }
            $actions += [
                FormAction::create('submit', $btnTitle)
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }
    
    /**
     * Submits the form.
     * 
     * @param array      $data Submitted data
     * @param CustomForm $form Form
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    public function doSubmit($data, CustomForm $form)
    {
        $email     = $data['Email'];
        $productID = $data['ProductID'];
        $memberID  = 0;
        $member    = Customer::currentRegisteredCustomer();
        $confirmed = false;
        if ($member instanceof Member
         && $member->exists()
        ) {
            $email     = $member->Email;
            $memberID  = $member->ID;
            $confirmed = true;
        } else {
            if (empty($email)) {
                $email = $this->getEmail();
            }
            Tools::Session()->set(self::SESSION_KEY_EMAIL, $email);
            Tools::saveSession();
        }
        $existing = StockNotification::get()->filter([
            'Email'     => $email,
            'ProductID' => $productID,
        ])->first();
        
        if ($existing instanceof StockNotification
         && $existing->exists()
        ) {
            if ($existing->MemberID === 0
             && $existing->MemberID !== $memberID) {
                $existing->MemberID = $memberID;
                $existing->write();
            }
            if (!$existing->OptInDone) {
                $existing->sendOptInEmail();
            }
        } else {
            $notification = StockNotification::create();
            $notification->Email     = $email;
            $notification->MemberID  = $memberID;
            $notification->ProductID = $productID;
            $notification->OptInDone = $confirmed;
            $notification->write();
            if (class_exists("\\SilverCart\\ProductPopularity\\Model\\Product\\ProductPopularity")) {
                $product = Product::get()->byID((int) $productID);
                if ($product instanceof Product
                 && $product->exists()
                ) {
                    $product->addPopularity(\SilverCart\ProductPopularity\Model\Product\ProductPopularity::SCORE_CART);
                }
            }
        }
        
        $successMessage = $this->fieldLabel('OptInEmailSent', ['email' => $email]);
        if ($confirmed) {
            $successMessage = $this->fieldLabel('SavedStockNotification', ['email' => $email]);
        }
        $this->setSuccessMessage($successMessage);
        $this->getController()->redirectBack();
    }

    /**
     * Returns the product
     * 
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Sets the product.
     * 
     * @param Product $product Product
     * 
     * @return void
     */
    public function setProduct(Product $product = null)
    {
        $this->product = $product;
    }

    /**
     * Returns an existing StockNotification for the product and email context 
     * if exists.
     * 
     * @return StockNotification
     */
    public function getExistingStockNotification()
    {
        if (is_null($this->existingStockNotification)) {
            $email = $this->getEmail();
            if (!is_null($email)) {
                $this->existingStockNotification = StockNotification::get()->filter([
                    'Email'     => $email,
                    'ProductID' => $this->getProduct()->ID,
                ])->first();
            }
        }
        return $this->existingStockNotification;
    }
    
    /**
     * Returns the current email context.
     * 
     * @return string
     */
    public function getEmail()
    {
        $customer = Customer::currentRegisteredCustomer();
        if ($customer instanceof Member
         && $customer->exists()) {
            $email = $customer->Email;
        } else {
            $email = Tools::Session()->get(self::SESSION_KEY_EMAIL);
        }
        return $email;
    }
    
    /**
     * Returns whether the notification was already requested by the current 
     * logged in customer.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.09.2018
     */
    public function AlreadyRequested()
    {
        if (is_null($this->alreadyRequested)) {
            $this->alreadyRequested = false;
            $existing               = $this->getExistingStockNotification();
            if ($existing instanceof StockNotification
             && $existing->exists()) {
                $this->alreadyRequested = true;
            }
        }
        return $this->alreadyRequested;
    }
    
    /**
     * Returns wheter an existing notification request was already confirmed.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.09.2018
     */
    public function Confirmed()
    {
        if (is_null($this->confirmed)) {
            $this->confirmed = false;
            $existing        = $this->getExistingStockNotification();
            if ($existing instanceof StockNotification
             && $existing->exists()
             && $existing->OptInDone) {
                $this->confirmed = true;
            }
        }
        return $this->confirmed;
    }
}