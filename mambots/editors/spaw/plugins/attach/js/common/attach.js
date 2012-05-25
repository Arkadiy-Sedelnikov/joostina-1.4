function SpawPGattach()
{
}

SpawPGattach.attachClick = function(editor, tbi, sender)
{
  if (tbi.is_enabled)
  {
    SpawEngine.openDialog('spawfm', 'spawfm', editor, '', 'type=images', 'SpawPGattach.attachClickCallback', tbi, sender);  
  }
}

SpawPGattach.attachClickCallback = function(editor, result, tbi, sender)
{
  if (result)
  {
    var newa = result;
	var fNm = /([^\/]+)$/.exec(newa);
    var pdoc = editor.getActivePageDoc();
    var aProps = pdoc.createElement("A");
    aProps.href = newa;
	aProps.innerHTML = '['+fNm[1]+']';
    editor.insertNodeAtSelection(aProps);
  }
  editor.updateToolbar();
  editor.focus();
}

SpawPGattach.isAttachEnabled = function(editor, tbi)
{
  return editor.isInDesignMode();
}
