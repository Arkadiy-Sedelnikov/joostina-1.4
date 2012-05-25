// image popup plugin
function SpawPGimgpopup()
{
}

SpawPGimgpopup.imagePopupClick = function(editor, tbi, sender)
{
  if (tbi.is_enabled)
  {
    SpawEngine.openDialog('spawfm', 'spawfm', editor, '', 'type=images', 'SpawPGimgpopup.imagePopupClickCallback', null, null);
  }
}

SpawPGimgpopup.imagePopupClickCallback = function(editor, result, tbi, sender)
{
  if (result)
  {
    var pdoc = editor.getActivePageDoc();
    var a = editor.getSelectedElementByTagName("a");
    if (!a)
    {
      a = pdoc.createElement("A");
      a.href = "#";
      a.setAttribute('onclick', "window.open('"+editor.getConfigValue("PG_IMGPOPUP_DIALOG")+editor.getConfigValue("PG_IMGPOPUP_PARAMETER")+"="+result+"','Image','width=500,height=300,scrollbars=no,toolbar=no,location=no,status=no,resizable=yes,screenX=120,screenY=100');return false;");
      // new link
      var sel = editor.getNodeAtSelection();
      if (sel.nodeType == 1 && sel.outerHTML) // workaround for IE
      {
        a.innerHTML = sel.outerHTML;
      }
      else
      {
        a.appendChild(sel);
      }
      if (SpawUtils.trim(a.innerHTML) == '')
      {
        // nothing selected
        a.innerHTML = result;
      }
      editor.insertNodeAtSelection(a);
    }
    else
    {
      // existing link
      a.href = "#";
      a.setAttribute('onclick', "window.open("+editor.getConfigValue("PG_IMGPOPUP_DIALOG")+editor.getConfigValue("PG_IMGPOPUP_PARAMETER")+"="+result+"','Image','width=500,height=300,scrollbars=no,toolbar=no,location=no,status=no,resizable=yes,screenX=120,screenY=100');return false;");
    }
  }
  //editor.updateToolbar();
}


SpawPGimgpopup.isImagePopupEnabled = function(editor, tbi)
{
  return editor.isInDesignMode();
}
