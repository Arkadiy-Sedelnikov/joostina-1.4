// YouTube properties
function SpawYouTubePropDialog() {
}
SpawYouTubePropDialog.init = function() {
    var iProps = spawArguments;
    if (iProps) {
        if (iProps.src && iProps.src.indexOf("youtube")) {
            var fl_url = iProps.src.substring(iProps.src.indexOf("src=") + 4);
            fl_url = fl_url.substring(fl_url.indexOf('v/') + 2);
            document.getElementById('csrc').value = "http://www.youtube.com/watch?v=" + fl_url + "?fs=1&amp;hl=ru_RU";
        }
    }
    SpawDialog.resizeDialogToContent();
}

SpawYouTubePropDialog.validateParams = function() {
    // check width and height
    if (!document.getElementById('csrc').value.match(/(youtube).*(v=)([^&]*)/)) {
        alert(spawErrorMessages['error_wrong_youtube_url']);
        document.getElementById('csrc').focus();
        return false;
    }
    return true;
}

SpawYouTubePropDialog.okClick = function() {
    // validate paramters
    if (SpawYouTubePropDialog.validateParams()) {
        var pdoc = spawEditor.getActivePageDoc();
        var iProps = spawArguments;
        if (iProps == null) {
            iProps = pdoc.createElement("img");
            iProps.style.cssText = "border: 1px solid #000000; background: url(" + SpawEngine.spaw_dir + "img/flash.gif);";
        }

        var fl_url = document.getElementById('csrc').value.match(/(youtube).*(v=)([^&]*)/);
        fl_url = fl_url[3];
        fl_url = "http://www.youtube.com/v/" + fl_url + "?fs=1&amp;hl=ru_RU";
        iProps.src = (document.getElementById('csrc').value) ? (SpawEngine.spaw_dir + 'img/spacer100.gif?imgtype=flash&src=' + fl_url) : '';
        if (!iProps.src || iProps.src == '')
            iProps.removeAttribute("src");

        iProps.setAttribute("width", "480");
        iProps.setAttribute("height", "385");
        iProps.setAttribute("allowscriptaccess", "always");
        iProps.setAttribute("allowfullscreen", "true");

        if (spawArgs.callback) {
            eval('window.opener.' + spawArgs.callback + '(spawEditor, iProps, spawArgs.tbi, spawArgs.sender)');
        }
        window.close();
    }
}

SpawYouTubePropDialog.cancelClick = function() {
    window.close();
}

if (document.attachEvent) {
    // ie
    window.attachEvent("onload", new Function("SpawYouTubePropDialog.init();"));
}
else {
    window.addEventListener("load", new Function("SpawYouTubePropDialog.init();"), false);
}

