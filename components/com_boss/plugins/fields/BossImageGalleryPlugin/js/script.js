//скрипт запуска загрузки файлов
jQuery(function() {
    var btnUpload = jQuery('#upload');
    var status = jQuery('#status');
    var directory = jQuery('input[name="directory"]').val();
    var numFields = 0;
    new AjaxUpload(btnUpload, {
        action: url + '/administrator/ajax.index.php?option=com_boss&act=upload_file&folder[0]=contents&folder[1]=gallery&folder[2]=origin&directory='+ directory,
        //Name of the file input box
        name: 'uploadfile',
        onSubmit: function(file, ext) {
            numFields = jQuery('#boss_plugin_image').find('.boss_img_gallery').length;
            if(numFields >= boss_nb_images){
                status.text('Only '+boss_nb_images+' files are allowed').addClass('error');
                return false;
            }

            if(boss_enable_images[0] != 'all'){
                var extension = ext[0];
                if (jQuery.inArray(extension, boss_enable_images) == -1) {
                    // check for valid file extension
                    status.text('Only '+boss_enable_images+' files are allowed');
                    return false;

                }
            }

            status.text('Uploading...');
        },
        onComplete: function(file, response) {
            //On completion clear the status
            status.text('');
            //Add uploaded file to list
            if (response === "success") {
                numFields = jQuery('#boss_plugin_image').find('.boss_img_gallery').length;
                var newFile =  '<label>Описание </label><input type="text" size="40" ' +
                    'name="boss_img_gallery['+numFields+'][signature]" class="inputbox boss_img_gallery" value="" />' +
                    file + '<input type="hidden" name="boss_img_gallery['+numFields+'][file]" value="'+ file+'" />' +
                    '&nbsp;&nbsp;<input type="button" value="X" class="button" onclick="bossDeleteImage(\''+ file+'\', \'file_'+numFields+'\')" />';
                
                jQuery('<div id="file_'+numFields+'"></div>').appendTo('#files').html(newFile).addClass('success');
            } else {
                status.text('Ошибка загрузки '+file).addClass('error');
            }
        }
    });
});

function bossDeleteImage(file, id){
    var directory = jQuery('input[name="directory"]').val();
    var ststusDel = '';
    
        jQuery.ajax({
        type: "POST",
        url: url+'/administrator/ajax.index.php?option=com_boss&act=delete_file&folder[0]=contents&folder[1]=gallery&folder[2]=origin&file='+file+'&directory='+directory,
        dataType: 'text',
        success: function (data){
            if(data == 'yes'){
                jQuery("#"+id).html('');
                ststusDel = ststusDel+ 'Origin Deleted; ';
            }
			else{
                ststusDel = ststusDel+ 'Origin Delete Error; ';
            }

            jQuery.ajax({
                type: "POST",
                url: url+'/administrator/ajax.index.php?option=com_boss&act=delete_file&folder[0]=contents&folder[1]=gallery&folder[2]=full&file='+file+'&directory='+directory,
                dataType: 'text',
                success: function (data){
                    if(data == 'yes'){
                        jQuery("#"+id).html('');
                        ststusDel = ststusDel+ 'Full Deleted; ';
                    }
	        		else{
                        ststusDel = ststusDel+ 'Full Delete Error; ';
                    }

                    jQuery.ajax({
                        type: "POST",
                        url: url+'/administrator/ajax.index.php?option=com_boss&act=delete_file&folder[0]=contents&folder[1]=gallery&folder[2]=thumb&file='+file+'&directory='+directory,
                        dataType: 'text',
                        success: function (data){
                            if(data == 'yes'){
                                jQuery("#"+id).html('');
                                ststusDel = ststusDel+ 'Thumb Deleted; ';
                            }
	                		else{
                                ststusDel = ststusDel+ 'Thumb Delete Error; ';
                            }
                            jQuery("#status").html(ststusDel);
                        }
                    });
                }
            });
        }
    });



}