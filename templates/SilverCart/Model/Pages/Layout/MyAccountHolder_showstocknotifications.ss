<div class="row">
    <section id="content-main" class="col-12 col-md-9">
        <h2 class="sr-only">{$Title}</h2>
        <% include SilverCart/Model/Pages/BreadCrumbs %>
        <article>
            <header><h1><%t SilverCart\ProductNotification\Model\StockNotification.MyStockNotifications 'My Notifications' %></h1></header>
            <p><%t SilverCart\ProductNotification\Model\StockNotification.MyStockNotificationsDesc 'If you are interested in a product which is not available right now, you can request a notification as soon as it\'s ready for sale again.' %></p>
            <p><%t SilverCart\ProductNotification\Model\StockNotification.MyStockNotificationsDesc2 'Here you can find a list of all products you requested a notification for.' %></p>
        <% if $StockNotifications %>
            <div class="row">
                <% loop $StockNotifications %>
                    <% if $Product %>
                        <% with $Product %>
                            <% include SilverCart\View\GroupView\ProductSmallTile %>
                        <% end_with %>
                    <% end_if %>
                <% end_loop %>
            </div>
        <% else %>
            <div class="alert alert-info"><span class="fa fa-info-circle"></span> <%t SilverCart\ProductNotification\Model\StockNotification.NoStockNotifications 'You don\'t have any notifications right now.' %></div>
        <% end_if %>
        </article>
        <% include SilverCart/Model/Pages/WidgetSetContent %>
    </section>
    <aside class="col-12 col-md-3">
        {$SubNavigation}
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>




