// Change case plugin
function SpawPGchangecase()
{
}

SpawPGchangecase.toUpperClick = function(editor, tbi, sender)
{
  if (tbi.is_enabled)
  {
    editor.selectionWalk(SpawPGchangecase.toUpperCase);
  }
}
SpawPGchangecase.toLowerClick = function(editor, tbi, sender)
{
  if (tbi.is_enabled)
  {
    editor.selectionWalk(SpawPGchangecase.toLowerCase);
  }
}
SpawPGchangecase.changeCase = function(mode, node, start_offset, end_offset)
{
  if (node.nodeType == 3) // text node
  {
    if (start_offset == null && end_offset == null)
    {
      // node is fully selected
      node.nodeValue = (mode == "upper")?node.nodeValue.toUpperCase():node.nodeValue.toLowerCase(); 
    } 
    else
    {
      // node is partially selected
      var val = node.nodeValue;
      if (start_offset == null)
        start_offset = 0;
      if (end_offset == null)
        end_offset = val.length

      var start_str = val.substr(0, start_offset);
      var end_str = val.substr(end_offset, val.length-end_offset);
      var sel_str = val.substr(start_offset, end_offset-start_offset);
      
      node.nodeValue = start_str + ((mode == "upper")?sel_str.toUpperCase():sel_str.toLowerCase()) + end_str;
    }
  }
}
SpawPGchangecase.toUpperCase = function(node, start_offset, end_offset)
{
  SpawPGchangecase.changeCase("upper", node, start_offset, end_offset);
}
SpawPGchangecase.toLowerCase = function(node, start_offset, end_offset)
{
  SpawPGchangecase.changeCase("lower", node, start_offset, end_offset);
}

SpawPGchangecase.isChangeCaseEnabled = function(editor, tbi)
{
  return editor.isInDesignMode();
}
