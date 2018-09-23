<% if $IncludeFormTag %>
<form {$addErrorClass('was-validated').AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
<% if $AlreadyRequested %>
    <% if $Confirmed %>
    <div class="alert alert-success">
        <h4><span class="fa fa-envelope-o"></span> {$fieldLabel('NotifyByEmail')}</h4>
        <span class="fa fa-check"></span> {$fieldLabel('NotificationRequestConfirmed')}
    </div>
    <% else %>
    <div class="alert alert-warning">
        <h4><span class="fa fa-envelope-o"></span> {$fieldLabel('NotifyByEmail')}</h4>
        <span class="fa fa-exclamation-circle"></span> {$fieldLabel('NotificationRequestNotConfirmed')}
        <% loop $Actions %>
            <button class="btn btn-sm btn-link" type="submit" id="{$ID}" title="{$Title}"><span class="fa fa-refresh"></span> {$Title}</button> 
        <% end_loop %>
    </div>
    <% end_if %>
<% else %>
    <div class="alert alert-info">
        <h4><span class="fa fa-envelope-o"></span> {$fieldLabel('NotifyByEmail')}</h4>
        {$BeforeFormContent}
        <div class="input-group mb-2">
            {$Fields.dataFieldByName(Email).Field}
            <div class="input-group-append">
            <% loop $Actions %>
                <button class="btn btn-primary" type="submit" id="{$ID}" title="{$Title}"><span class="fa fa-check"></span> {$Title}</button> 
            <% end_loop %>
            </div>
        </div>
        {$CustomFormSpecialFields}
        <p><span class="fa fa-info-circle"></span> {$fieldLabel('NotifyByEmailDesc')}</p>
        {$AfterFormContent}
    </div>
<% end_if %>
<% if $IncludeFormTag %>
</form>
<% end_if %>