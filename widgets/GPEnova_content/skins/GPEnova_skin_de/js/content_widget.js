//btn_next
function content_wgNOVA_next(obj,list_per_page){
    var page = 1;
    if(obj.is('.gpe_wgListADIV')) {
        var list = jQuery('>table>tbody>tr',obj);
    } else if(obj.is('.gpe_wgGalleryADIV, .gpe_wgZineADIV')) {
        var list = jQuery('>ul>li',obj);
    }

    var total_page = parseInt((list.size()-1) / list_per_page,10)+1;
    list.each(function(i){
        if(jQuery(this).css('display') !='none'){
            page = parseInt((i+1)/list_per_page,10)+1;
            return false;
        }
    });
    if(total_page <= page) return;
    list.each(function(i){
        if( (page* list_per_page) <= i && ((page+1) * list_per_page) > i){
            jQuery(this).fadeIn();
        }else{
            jQuery(this).hide();
        }
    });
}
//btn_prev
function content_wgNOVA_prev(obj,list_per_page){
    var page = 1;
    if(obj.is('.gpe_wgListADIV')){
        var list = jQuery('>table>tbody>tr',obj);
    }else if(obj.is('.gpe_wgGalleryADIV, .gpe_wgZineADIV')){
        var list = jQuery('>ul>li',obj);
    }

    var total_page = parseInt((list.size()-1) / list_per_page,10)+1;
    list.each(function(i){
        if(jQuery(this).css('display') !='none'){
            page = parseInt((i+1)/list_per_page,10)+1;
            return false;
        }
    });

    if(page <= 1) return;
    list.each(function(i){
        if( ((page-2)* list_per_page)<= i && ((page-1) * list_per_page) > i){
            jQuery(this).fadeIn();
        }else{
            jQuery(this).hide();
        }
    });
}
//tap_over_action_type1
function content_wgNOVA_tab_show(tab,list,i){
    tab.parents('ul.gpe_wgTabA').children('li.active').removeClass('active');
    tab.parent('li').addClass('active');
    jQuery('>dd',list).each(function(j){
            if(j==i) jQuery(this).addClass('open');
            else jQuery(this).removeClass('open');
            });
}
//tap_over_action_type2
function content_wgNOVA_tabClick_show(tab,list,i){
    tab.parents('ul.gpe_wgTabA').children('li.active_t2').removeClass('active_t2');
    tab.parent('li').addClass('active_t2');
    jQuery('>dd',list).each(function(j){
            if(j==i) jQuery(this).addClass('open');
            else jQuery(this).removeClass('open');
            });
}