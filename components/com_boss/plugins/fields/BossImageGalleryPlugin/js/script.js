//скрипт запуска загрузки файлов
jQuery(function () {
    var btnUpload = jQuery('#upload_image');
    var status = jQuery('#status_image');
    var directory = jQuery('input[name="directory"]').val();
    var numFields = 0;
    var actUrl = '/ajax.index.php?option=com_boss&act=upload_file&folder[0]=contents&folder[1]=gallery&folder[2]=origin&max_filesize=' + boss_max_imgsize + '&directory=' + directory;
    if (boss_isadmin == 1) {
        actUrl = '/administrator' + actUrl;
    }

    // добавляем атрибут «accept», для фильтрации файлов по MIME-типу
    //jQuery('#upload_image').attr('accept', 'image/jpeg,image/jpg,image/pjpeg,image/png,image/gif,image/bmp');
    //jQuery('#upload_image').attr('accept', 'image/*');
    new AjaxUpload(btnUpload,
        {
            action:url + actUrl,
            //Name of the file input box
            name:'uploadfile',
            onSubmit:function (file, ext) {
                var file = file;
                //numFields = jQuery('#boss_plugin_image').find('.boss_img_gallery').length;
                numFields = jQuery('#gallery_images').find('.boss_img_gallery').length;
                //alert(numFields);
                if (numFields >= boss_nb_images) {
                    status.text('Only ' + boss_nb_images + ' files are allowed').addClass('error');
                    return false;
                }

                if (boss_enable_images[0] != 'all') {
                    var extension = ext[0];
                    if (jQuery.inArray(extension, boss_enable_images) == -1) {
                        // check for valid file extension
                        status.text('Only ' + boss_enable_images + ' files are allowed').addClass('error');
                        return false;
                    }
                }
                status.text('Загрузка...');
            },

            onComplete:function (file, response) {
                //On completion clear the status
                status.text('');
                //Add uploaded file to list
                if (response != "error" && response != "error_max_filesize") {
                    //numFields = jQuery('#boss_plugin_image').find('.boss_img_gallery').length;
                    numFields = jQuery('#gallery_images').find('.boss_img_gallery').length;

                    var newFile = '<label>Описание </label><input type="text" size="40" ' +
                        'name="boss_img_gallery[' + numFields + '][signature]" class="inputbox boss_img_gallery" value="" />' +
                        response + '<input type="hidden" name="boss_img_gallery[' + numFields + '][file]" value="' + response + '" />' +
                        '&nbsp;&nbsp;<input type="button" value="X" class="button" onclick="bossDeleteImage(\'' + response + '\', \'file_' + numFields + '\')" />';

                    jQuery('<div id="file_' + numFields + '"></div>').appendTo('#gallery_images').html(newFile).addClass('success');

                } else if (response == "error_max_filesize") {
                    status.text('Размер ' + file + ' превышает допустимый, допустимый размер ' + boss_max_imgsize + ' Байт.').addClass('error');
                }
                else {
                    status.text('Ошибка загрузки ' + file).addClass('error');
                }
            }
        });
});

function bossDeleteImage(file, id) {
    var directory = jQuery('input[name="directory"]').val();
    var ststusDel = '';
    var actUrl = '/ajax.index.php?option=com_boss&act=delete_file&folder[0]=contents&folder[1]=gallery&folder[2]=origin&file=' + file + '&directory=' + directory;

    if (boss_isadmin == 1) {
        actUrl = '/administrator' + actUrl;
    }

    jQuery.ajax(
        {
            type:"POST",
            url:url + actUrl,
            dataType:'text',
            success:function (data) {
                if (data == 'yes') {
                    //jQuery("#" + id).html('');//jQuery("#" + id).fadeOut('slow');
                    jQuery("#" + id).fadeOut('normal', function () {
                        jQuery("#" + id).html('');
                    });
                    ststusDel = ststusDel + 'Оригинал удален; ';
                }
                else {
                    ststusDel = ststusDel + 'Ошибка удаления оригинала; ';
                }

                jQuery.ajax(
                    {
                        type:"POST",
                        url:url + '/administrator/ajax.index.php?option=com_boss&act=delete_file&folder[0]=contents&folder[1]=gallery&folder[2]=full&file=' + file + '&directory=' + directory,
                        dataType:'text',
                        success:function (data) {
                            if (data == 'yes') {
                                jQuery("#" + id).fadeOut('normal', function () {
                                    jQuery("#" + id).html('');
                                });
                                ststusDel = ststusDel + 'Эскиз удален; ';
                            }
                            else {
                                ststusDel = ststusDel + 'Ошибка удаления эскиза; ';
                            }

                            jQuery.ajax(
                                {
                                    type:"POST",
                                    url:url + '/administrator/ajax.index.php?option=com_boss&act=delete_file&folder[0]=contents&folder[1]=gallery&folder[2]=thumb&file=' + file + '&directory=' + directory,
                                    dataType:'text',
                                    success:function (data) {
                                        if (data == 'yes') {
                                            jQuery("#" + id).fadeOut('normal', function () {
                                                jQuery("#" + id).html('');
                                            });
                                            ststusDel = ststusDel + 'Миниатюра удалена; ';
                                        }
                                        else {
                                            ststusDel = ststusDel + 'Ошибка удаления миниатюры; ';
                                        }
                                        jQuery("#status_image").html(ststusDel);
                                    }
                                });
                        }
                    });
            }
        });
}