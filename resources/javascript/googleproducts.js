function categories_selected()
{
    return $('listOOGoogleFeed_Categories_index_list_body').getElements('tr td.checkbox input').some(function(element){return element.checked});
}




function enable_selected()
{
    if (!categories_selected())
    {
        alert('Please select products to enable or disable.');
        return false;
    }
    
    $('listformOOGoogleFeed_Categories_index_list').getForm().sendPhpr('index_onEnableSelected',
        {
            confirm: 'Do you really want to Disable/Enable selected product(s)?',
            loadIndicator: {show: false},
            onBeforePost: LightLoadingIndicator.show.pass('Loading...'),
            onComplete: LightLoadingIndicator.hide,
            onFailure: popupAjaxError,
            update: 'index_content',
            onAfterUpdate: update_scrollable_toolbars

        }
    );
    return false;

}

function disable_selected()
{
    if (!categories_selected())
    {
        alert('Please select products to enable or disable.');
        return false;
    }
    
    $('listformOOGoogleFeed_Categories_index_list').getForm().sendPhpr('index_onDisableSelected',
        {
            confirm: 'Do you really want to Disable/Enable selected product(s)?',
            loadIndicator: {show: false},
            onBeforePost: LightLoadingIndicator.show.pass('Loading...'),
            onComplete: LightLoadingIndicator.hide,
            onFailure: popupAjaxError,
            update: 'index_content',
            onAfterUpdate: update_scrollable_toolbars

        }
    );
    return false;

}
