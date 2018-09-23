<h1><%t SilverCart\ProductNotification\Model\StockNotification.EmailTitleStockNotificationForTemplate '"{product}" is available for delivery now!' product=$Product.Title %></h1>
<% if $Member %>
<p><%t SilverCart\ProductNotification\Model\StockNotification.EmailNotificationHelloMember 'Hello {name}!' name=$Member.Name %></p>
<% else %>
<p>{$Notification.fieldLabel('EmailNotificationHelloAnonymous')}</p>
<% end_if %>
<p><%t SilverCart\ProductNotification\Model\StockNotification.EmailNotificationInfo 'We are happy to inform you that the product "<a href="{link}">{product}</a>" is available again.' link=$Product.Link product=$Product.Title %></p>
<p><%t SilverCart\ProductNotification\Model\StockNotification.EmailNotificationInfo2 'Click on the link below or copy the link to your browser to got to the product.' %></p>
<p><a href="{$Product.Link}">{$Notification.fieldLabel('EmailNotificationGoToProduct')} &rarr;</a></p>
<p><a href="{$Product.Link}">{$Product.AbsoluteLink}</a></p>
<p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
<p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
