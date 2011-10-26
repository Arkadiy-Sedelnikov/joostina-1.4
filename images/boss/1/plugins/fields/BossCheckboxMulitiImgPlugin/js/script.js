//скрипт запуска загрузки файлов
jQuery(function() {
    var btnUpload = jQuery('#upload');
    var status = jQuery('#status');
    var directory = jQuery('input[name="directory"]').val();
    new AjaxUpload(btnUpload, {
        action: url + '/administrator/ajax.index.php?option=com_boss&act=upload_image&directory='+ directory,
        //Name of the file input box
        name: 'uploadfile',
        onSubmit: function(file, ext) {
            if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                // check for valid file extension
                status.text('Only JPG, PNG or GIF files are allowed');
                return false;

            }
            status.text('Uploading...');
        },
        onComplete: function(file, response) {
            //On completion clear the status
            status.text('');
            //Add uploaded file to list
            if (response === "success") {
                jQuery('<div></div>').appendTo('#files').html('<img src="/images/boss/'+directory+'/fields/'+file+'" alt="" /><br />' + file).addClass('success');
                jQuery('.img_select').append('<option value="'+file+'">'+file+'</option>');
            } else {
                jQuery('<div></div>').appendTo('#files').text('Ошибка загрузки '+file).addClass('error');
            }
        }
    });
});