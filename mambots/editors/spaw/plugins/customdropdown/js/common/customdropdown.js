function SpawPGcustomdropdown(){}

SpawPGcustomdropdown.change = function(editor, tbi, sender)
{
  if (tbi.is_enabled) {
    var cls = sender.options[sender.selectedIndex].value;
    if (!sender.selectedIndex||!cls) 
        return;
	var cls2 = editor.getConfigValue(cls);
	cls=cls2?cls2:cls;
    sender.selectedIndex = 0;
	if (editor.isInDesignMode()){
		editor.insertHtmlAtSelection(cls);
		editor.updateToolbar();
		editor.focus();
	} else {
		var pta = editor.getPageInput(editor.getActivePage().name);
		var ss = pta.selectionStart;
		pta.value = pta.value.substring(0,ss)+cls+pta.value.substring(pta.selectionEnd);
		pta.setSelectionRange(ss,ss+cls.length);
		pta.focus();
	}
    return null;
  }
}

SpawPGcustomdropdown.isEnabled = function(editor, tbi){
  return tbi.is_enabled;
}

SpawPGcustomdropdown.statusCheck = function(editor, tbi){
  return null;
}
