<?php

use SilverCart\Model\ShopEmail;
use SilverCart\ProductNotification\Model\StockNotification;

ShopEmail::register_email_template('StockNotification',      StockNotification::singleton()->fieldLabel('EmailTitleStockNotification'));
ShopEmail::register_email_template('StockNotificationOptIn', StockNotification::singleton()->fieldLabel('EmailTitleStockNotificationOptIn'));