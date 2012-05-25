// yahooMaps plugin
function SpawPGyahooMaps()
{
}

SpawPGyahooMaps.yahooMapsPropClick = function(editor, tbi, sender)
{
  if (tbi.is_enabled)
  {
    var i = editor.getSelectedElementByTagName("img");
    if (i)
    {
      // editing
      editor.stripAbsoluteUrl(i);
      SpawEngine.openDialog('yahooMaps', 'yahooMaps_prop', editor, i, '', '', tbi, sender);
    }
    else
    {
      // new flash
      SpawEngine.openDialog('yahooMaps', 'yahooMaps_prop', editor, i, '', 'SpawPGyahooMaps.yahooMapsPropClickCallback', tbi, sender);
    }
  }
}
SpawPGyahooMaps.yahooMapsPropClickCallback = function(editor, result, tbi, sender)
{
  if (result)
  {
    editor.insertNodeAtSelection(result);
  }
  editor.updateToolbar();
}
SpawPGyahooMaps.isyahooMapsPropEnabled =  function(editor, tbi)
{
  return editor.isInDesignMode();
}
