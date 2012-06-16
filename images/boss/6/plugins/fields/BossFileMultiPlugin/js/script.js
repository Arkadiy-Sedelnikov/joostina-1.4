//скрипт запуска загрузки файлов
jQuery(function () {
    var btnUpload = jQuery('#upload');
    var status = jQuery('#status');
    var directory = jQuery('input[name="directory"]').val();
    var numFields = 0;
    var actUrl = '/ajax.index.php?option=com_boss&act=upload_file&folder=files&directory=' + directory;
    if (boss_isadmin == 1) {
        actUrl = '/administrator' + actUrl;
    }

    new AjaxUpload(btnUpload, {
        action:url + actUrl,
        //Name of the file input box
        name:'uploadfile',
        onSubmit:function (file, ext) {
            numFields = jQuery('#boss_plugin_file').find('.boss_file').length;
            if (numFields >= boss_nb_files) {
                status.text('Only ' + boss_nb_files + ' files are allowed').addClass('error');
                return false;
            }

            if (boss_enable_files[0] != 'all') {
                var extension = ext[0];
                if (jQuery.inArray(extension, boss_enable_files) == -1) {
                    // check for valid file extension
                    status.text('Only ' + boss_enable_files + ' files are allowed');
                    return false;

                }
            }

            status.text('Uploading...');
        },
        onComplete:function (file, response) {
            //On completion clear the status
            status.text('');
            //Add uploaded file to list
            if (response != "error" && response != "error_max_filesize") {

                numFields = jQuery('#boss_plugin_file').find('.boss_file').length;
                var newFile = '<label>Описание </label><input type="text" size="40" ' +
                    'name="boss_file[' + numFields + '][signature]" class="inputbox boss_file" value="" />' +
                    response + '<input type="hidden" name="boss_file[' + numFields + '][file]" value="' + response + '" />' +
                    '&nbsp;&nbsp;<input type="button" value="X" class="button" onclick="bossDeleteFile(\'' + response + '\', \'file_' + numFields + '\')" />';

                jQuery('<div id="file_' + numFields + '"></div>').appendTo('#files').html(newFile).addClass('success');
            } else {
                status.text('Ошибка загрузки ' + file).addClass('error');
            }
        }
    });
});

function bossDeleteFile(file, id) {
    var actUrl = '/ajax.index.php?option=com_boss&act=delete_file&folder=files&file=' + file + '&directory=' + directory;
    if (boss_isadmin == 1) {
        actUrl = '/administrator' + actUrl;
    }

    var directory = jQuery('input[name="directory"]').val();
    jQuery.ajax({
        type:"POST",
        url:url + actUrl,
        dataType:'text',
        success:function (data) {
            if (data == 'yes') {
                jQuery("#" + id).html('');
                jQuery("#status").html('<div>Deleted</div>');
            }

            else {
                jQuery("#status").html('<div>Error</div>').addClass('error');
            }
        }
    });
}