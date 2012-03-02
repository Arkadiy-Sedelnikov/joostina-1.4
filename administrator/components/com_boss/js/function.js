// смена статуса публикации, elID - идентификатор объекта у которого меняется статус публикации
var url = 'http://'+location.hostname;

function boss_publ(elID,uri){
	jQuery('#'+elID).attr('src','/administrator/images/aload.gif');
	jQuery.get( url+'/administrator/ajax.index.php?option=com_boss&'+uri, function(data) {
        jQuery('#'+elID).attr('src','/administrator/images/'+data);
	});
	return false;
}

function delete_pack(directory,id){

    jQuery.ajax({
        type: "POST",
        url: url+'/administrator/ajax.index.php?option=com_boss&act=delete_pack&pack='+id+'&directory='+directory,
        dataType: 'text',
        success: function (data){
            if(data == 'yes')
                jQuery("#"+id).html('<div>Deleted</div>');
			else
				jQuery("#"+id).html('<div>Error</div>');
        }
    });
}

//function showHide(id) {
//    jQuery('#'+id ).toggle();
//}

function showHide(id){
        jQuery(".jwts_slidewrapper").slideUp('slow');
		jQuery('#'+id ).slideDown('slow');
	}


jQuery(document).ready(function(){
    
	jQuery(".check_it").bind('click', function () {
            var id = jQuery(this).attr("id").replace('check_', '');
		jQuery("input.urights_box_"+id).each(function () {
			jQuery(this).attr("checked","checked");
		});
		jQuery(".checker").toggleClass("active");
		return false;
	});
    
	jQuery(".uncheck_it").bind('click', function () {
            var id = jQuery(this).attr("id").replace('uncheck_', '');
		jQuery("input.urights_box_"+id).each(function () {
			jQuery(this).removeAttr("checked");
		});
		jQuery(".checker").toggleClass("active");
		return false;
	});

        jQuery(".checker_group").bind('click', function () {
               
                var idArray = this.id.split("_");
                var objekt = idArray[1];
                var id = idArray[2];
                var hiddenId = 'hidden_'+objekt+'_'+id;

                if (jQuery("#"+hiddenId).val()==1) {
                      jQuery("input.urights_box_"+objekt).each(function () {
                          if (jQuery(this).val()==id) jQuery(this).removeAttr('checked');
                       });
                       jQuery("#"+hiddenId).attr('value', 0);
                     }
                     else {
                      jQuery("input.urights_box_"+objekt).each(function () {
                          if (jQuery(this).val()==id) jQuery(this).attr("checked","checked");
                       });
                       jQuery("#"+hiddenId).attr('value', 1);
                     }

		return false;
	});
    
});