<?php

use SilverCart\Admin\Dev\ExampleData;
use SilverCart\Model\ShopEmail;
use SilverCart\ProductNotification\Model\StockNotification;

ExampleData::register_email_example_data('StockNotification', function() {
    $notification = StockNotification::singleton();
    $product      = ExampleData::get_product();
    $member       = ExampleData::get_member();
    return [
        'Notification' => $notification,
        'Product'      => $product,
        'Member'       => $member,
        'Email'        => 'email@example.com',
    ];
});
ShopEmail::register_email_template('StockNotification',      StockNotification::singleton()->fieldLabel('EmailTitleStockNotification'));
ShopEmail::register_email_template('StockNotificationOptIn', StockNotification::singleton()->fieldLabel('EmailTitleStockNotificationOptIn'));