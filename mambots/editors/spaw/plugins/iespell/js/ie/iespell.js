// ieSpell plugin
function SpawPGiespell()
{
}

SpawPGiespell.ieSpellClick = function(editor, tbi, sender)
{
  if (tbi.is_enabled)
  {
    try
    {
      var iespell = new ActiveXObject("ieSpell.ieSpellExtension");
      var pdoc = editor.getActivePageDoc();
      iespell.CheckDocumentNode(pdoc.body);
    }
    catch(excp) {}
  }
}

SpawPGiespell.isIeSpellEnabled = function(editor, tbi)
{
  var result = false;
  if (editor.isInDesignMode())
  {
    try
    {
      var iespell = new ActiveXObject("ieSpell.ieSpellExtension");
      result = true;
    }
    catch(excp) {}
  }
  return result;
}
