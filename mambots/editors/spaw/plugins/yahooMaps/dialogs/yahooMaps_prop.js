// yahooMaps properties
function SpawyahooMapsPropDialog()
{
}
SpawyahooMapsPropDialog.init = function() {
  var iProps = spawArguments;
  if (iProps) {
    if (iProps.src && iProps.src.indexOf("yahooMaps")) {
      var fl_url = iProps.src.substring(iProps.src.indexOf("src=")+4);
      fl_url = fl_url.substring(fl_url.indexOf('v/')+2);
      document.getElementById('csrc').value = "http://www.yahooMaps.com/watch?v=" + fl_url;
    }
  }
  SpawDialog.resizeDialogToContent();
}

SpawyahooMapsPropDialog.validateParams = function() {
  // check width and height
  if (!document.getElementById('csrc').value) {
    alert(spawErrorMessages['error_wrong_yahooMaps_address']);
    document.getElementById('csrc').focus();
    return false;
  }
  if (!document.getElementById('cdesc').value) {
    alert(spawErrorMessages['error_wrong_yahooMaps_desc']);
    document.getElementById('cdesc').focus();
    return false;
  }
  return true;
}

SpawyahooMapsPropDialog.okClick = function() {
  // validate paramters
  if (SpawyahooMapsPropDialog.validateParams()) {
    
    var pdoc = spawEditor.getActivePageDoc();
    var iProps = spawArguments;
    if (iProps == null) {
      iProps = pdoc.createElement("div");
  	  iProps.setAttribute("id", "mapContainer");
      iProps.setAttribute("class", "map_container");  
    }    
    iProps.setAttribute("about",document.getElementById('cdesc').value);
    iProps.setAttribute("address",document.getElementById('csrc').value);
    iProps.setAttribute("zoom","3");

    if (spawArgs.callback) {
      eval('window.opener.'+spawArgs.callback + '(spawEditor, iProps, spawArgs.tbi, spawArgs.sender)');
    }
    window.close();
  }
}

SpawyahooMapsPropDialog.cancelClick = function() {
  window.close();
}

if (document.attachEvent) {
  // ie
  window.attachEvent("onload", new Function("SpawyahooMapsPropDialog.init();"));
} else {
  window.addEventListener("load", new Function("SpawyahooMapsPropDialog.init();"), false);
}
