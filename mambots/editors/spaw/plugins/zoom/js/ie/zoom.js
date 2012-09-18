// zoom plugin
function SpawPGzoom()
{
}

SpawPGzoom.zoomChange = function(editor, tbi, sender)
{
  if (tbi.is_enabled)
  {
    var pdoc = editor.getPageDoc(editor.getActivePage().name);
    pdoc.body.style.zoom = sender.options[sender.selectedIndex].value;
    sender.selectedIndex = 0;
    editor.getPageIframeObject(editor.getActivePage().name).focus();
    pdoc.designMode = 'on';
    editor.updateToolbar();
  }
}

SpawPGzoom.isZoomEnabled = function(editor, tbi)
{
  return editor.isInDesignMode();
}

SpawPGzoom.zoomStatusCheck = function(editor, tbi)
{
  if (tbi.is_enabled)
  {
    var pdoc = editor.getPageDoc(editor.getActivePage().name);
    return pdoc.body.style.zoom;
    editor.updateToolbar();
  }
  else
   return null;
}
