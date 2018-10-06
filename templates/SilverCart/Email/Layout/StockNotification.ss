<h1><%t SilverCart\ProductNotification\Model\StockNotification.EmailTitleStockNotificationForTemplate '"{product}" is available for delivery now!' product=$Product.Title %></h1>
<% if $Member %>
<p><%t SilverCart\ProductNotification\Model\StockNotification.EmailNotificationHelloMember 'Hello {name}!' name=$Member.Name %></p>
<% else %>
<p>{$Notification.fieldLabel('EmailNotificationHelloAnonymous')}</p>
<% end_if %>
<p><%t SilverCart\ProductNotification\Model\StockNotification.EmailNotificationInfo 'We are happy to inform you that the product "<a href="{link}">{product}</a>" is available again.' link=$Product.Link product=$Product.Title %></p>
<p><%t SilverCart\ProductNotification\Model\StockNotification.EmailNotificationInfo2 'Click on the link below or copy the link to your browser to got to the product.' %></p>
<% with $Product %>
<div style="border: 1px solid #ddd; padding: 6px; box-shadow: 0 0px 3px #bbb inset; overflow: hidden;">
    <a style="float: left; margin-right: 6px;" href="{$Link}">{$ListImage.Pad(120,120)}</a>
    <h1><a href="{$Link}">{$Title}</a></h1>
    <% if $ShortDescription %>
    <i>{$ShortDescription}</i>
    <% end_if %>
    <% if $LongDescription %>
    <p>{$LongDescription.LimitWordCount(40)}</p>
    <% end_if %>
    <p><a href="{$Link}">{$AbsoluteLink}</a></p>
    <div style="text-align: right;">
        <a style="
            display: inline-block;
            text-decoration: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 0.82rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            color: #fff;
            background-color: #083358;
            border-color: #083358;
           " href="{$Link}">{$Up.Notification.fieldLabel('EmailNotificationGoToProduct')} &rarr;</a>
    </div>
</div>
<% end_with %>
<p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
<p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
