// YouTube plugin
function SpawPGyoutube()
{
}

SpawPGyoutube.youTubePropClick = function(editor, tbi, sender)
{
  if (tbi.is_enabled)
  {
    var i = editor.getSelectedElementByTagName("img");
    if (i)
    {
      // editing
      editor.stripAbsoluteUrl(i);
      SpawEngine.openDialog('youtube', 'youtube_prop', editor, i, '', '', tbi, sender);
    }
    else
    {
      // new flash
      SpawEngine.openDialog('youtube', 'youtube_prop', editor, i, '', 'SpawPGyoutube.youTubePropClickCallback', tbi, sender);
    }
  }
}
SpawPGyoutube.youTubePropClickCallback = function(editor, result, tbi, sender)
{
  if (result)
  {
    editor.insertNodeAtSelection(result);
  }
  editor.updateToolbar();
}
SpawPGyoutube.isYouTubePropEnabled =  function(editor, tbi)
{
  return editor.isInDesignMode();
}
