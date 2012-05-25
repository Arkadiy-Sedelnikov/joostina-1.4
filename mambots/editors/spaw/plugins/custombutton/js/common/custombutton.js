function SpawPGcustombutton() 
{ 
} 

SpawPGcustombutton.myButtonClick = function(editor, tbi, sender) 
{ 
  if (tbi.is_enabled) 
  { 
    var pdoc = editor.getActivePageDoc(); 
    var cur_node = editor.getNodeAtSelection();
    var tmp_node = pdoc.createElement("SPAN");
    tmp_node.appendChild(cur_node);
    var template = editor.getConfigValue('PG_CUSTOMBUTTON_TEMPLATE');
    tmp_node.innerHTML = template.replace('%%CURRENT_CONTENT%%', tmp_node.innerHTML);
    editor.insertNodeAtSelection(tmp_node); 
  } 
} 

SpawPGcustombutton.isMyButtonEnabled = function(editor, tbi) 
{ 
  return editor.isInDesignMode(); 
} 
