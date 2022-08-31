function avvFilterClear(idElement, idForm) {
    if (
        (typeof(idElement) === 'undefined') || (typeof(idElement) != 'string') ||
        (idElement =='')
    )
    {
        idElement = 'filter_search';
    }
    if (
        (typeof(idForm) === 'undefined') || (typeof(idForm) != 'string') ||
        (idForm =='')
    )
    {
        idForm = 'adminForm';
    }
    //DOM ==================
    //e = document.getElementById(idElement);
    //e.value="";
    //document.getElementById(idForm).submit();
    //jQuery ===============
    //jQuery('#'+idElement).attr('value', '');
    //jQuery('#'+idForm).submit();
    //Mootools =============
    $$('#'+idElement).setProperty('value', '');
    $$('#'+idForm)[0].submit();
}

function showHideFilterPanel() {
    el = jQuery('#btn-showhide-panel');
    el.toggleClass('icon-arrow-down').toggleClass('icon-arrow-up');
    fb = jQuery("#filter-buttons");
    if (el.hasClass('icon-arrow-down')) {
        // панель скрыта
        console.log("панель скрыта");
        fb.addClass("hide-cc");
        tooltipStr = "Показать панель фильтров";
        sfp = 0;
        el.parent().removeClass('shown');
    }
    if (el.hasClass('icon-arrow-up')) {
        // панель показана
        console.log("панель показана");
        fb.removeClass("hide-cc");
        tooltipStr = "Скрыть панель фильтров";
        sfp = 1;
        el.parent().addClass('shown');
    }
    jQuery('#button-showhide-filterpanel').attr('data-original-title', tooltipStr);
    jQuery('#show_filter_panel').attr('value', sfp);
}