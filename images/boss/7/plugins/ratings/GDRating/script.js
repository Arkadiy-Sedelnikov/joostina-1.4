function gd_rating_plugin(value, content_id, ip, units, width, directory, user_id, path) {
    $.post(path, {
            value:value,
            content_id:content_id,
            ip:ip,
            units:units,
            width:width,
            directory:directory,
            user_id:user_id
        },
        function (data) {
            $("#ratingblock_" + content_id).html(data);
        }
    );
}