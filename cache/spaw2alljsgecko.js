//###647467513###

function SpawColor()
{}
SpawColor.prototype.hue=359;SpawColor.prototype.saturation=100;SpawColor.prototype.brightness=100;SpawColor.prototype.red=255;SpawColor.prototype.green=255;SpawColor.prototype.blue=255;SpawColor.prototype.setHSB=function(h,s,b)
{if(h!=null)
this.hue=h;if(s!=null)
this.saturation=s;if(b!=null)
this.brightness=b;this.updateRGBFromHSB();}
SpawColor.prototype.setRGB=function(r,g,b)
{if(r!=null)
this.red=r;if(g!=null)
this.green=g;if(b!=null)
this.blue=b;this.updateHSBFromRGB();}
SpawColor.prototype.setRGBFromHTML=function(val)
{var sval=val;if((sval.length!=4&&sval.length!=7)||sval.charAt(0)!='#')
{sval=this.getHTMLColorFromKeyword(sval);}
if(sval!=null&&(sval.length==4||sval.length==7)&&sval.charAt(0)=='#')
{if(sval.length==4)
{this.red=this.hex2dec(sval.charAt(1)+sval.charAt(1));this.green=this.hex2dec(sval.charAt(2)+sval.charAt(2));this.blue=this.hex2dec(sval.charAt(3)+sval.charAt(3));}
else
{this.red=this.hex2dec(sval.substring(1,3));this.green=this.hex2dec(sval.substring(3,5));this.blue=this.hex2dec(sval.substring(5,7));}
this.updateHSBFromRGB();}}
SpawColor.prototype.getHTMLColorFromKeyword=function(kwd)
{var named_colors=new Array();named_colors['aliceblue']='#f0f8ff';named_colors['antiquewhite']='#faebd7';named_colors['aqua']='#00ffff';named_colors['aquamarine']='#7fffd4';named_colors['azure']='#f0ffff';named_colors['beige']='#f5f5dc';named_colors['bisque']='#ffe4c4';named_colors['black']='#000000';named_colors['blanchedalmond']='#ffebcd';named_colors['blue']='#0000ff';named_colors['blueviolet']='#8a2be2';named_colors['brown']='#a52a2a';named_colors['burlywood']='#deb887';named_colors['cadetblue']='#5f9ea0';named_colors['chartreuse']='#7fff00';named_colors['chocolate']='#d2691e';named_colors['coral']='#ff7f50';named_colors['cornflowerblue']='#6495ed';named_colors['cornsilk']='#fff8dc';named_colors['crimson']='#dc143c';named_colors['cyan']='#00ffff';named_colors['darkblue']='#00008b';named_colors['darkcyan']='#008b8b';named_colors['darkgoldenrod']='#b8860b';named_colors['darkgray']='#a9a9a9';named_colors['darkgreen']='#006400';named_colors['darkkhaki']='#bdb76b';named_colors['darkmagenta']='#8b008b';named_colors['darkolivegreen']='#556b2f';named_colors['darkorange']='#ff8c00';named_colors['darkorchid']='#9932cc';named_colors['darkred']='#8b0000';named_colors['darksalmon']='#e9967a';named_colors['darkseagreen']='#8fbc8f';named_colors['darkslateblue']='#483d8b';named_colors['darkslategray']='#2f4f4f';named_colors['darkturquoise']='#00ced1';named_colors['darkviolet']='#9400d3';named_colors['deeppink']='#ff1493';named_colors['deepskyblue']='#00bfff';named_colors['dimgray']='#696969';named_colors['dodgerblue']='#1e90ff';named_colors['firebrick']='#b22222';named_colors['floralwhite']='#fffaf0';named_colors['forestgreen']='#228b22';named_colors['fuchsia']='#ff00ff';named_colors['gainsboro']='#dcdcdc';named_colors['ghostwhite']='#f8f8ff';named_colors['gold']='#ffd700';named_colors['goldenrod']='#daa520';named_colors['gray']='#808080';named_colors['green']='#008000';named_colors['greenyellow']='#adff2f';named_colors['honeydew']='#f0fff0';named_colors['hotpink']='#ff69b4';named_colors['indianred']='#cd5c5c';named_colors['indigo']='#4b0082';named_colors['ivory']='#fffff0';named_colors['khaki']='#f0e68c';named_colors['lavender']='#e6e6fa';named_colors['lavenderblush']='#fff0f5';named_colors['lawngreen']='#7cfc00';named_colors['lemonchiffon']='#fffacd';named_colors['lightblue']='#add8e6';named_colors['lightcoral']='#f08080';named_colors['lightcyan']='#e0ffff';named_colors['lightgoldenrodyellow']='#fafad2';named_colors['lightgreen']='#90ee90';named_colors['lightgrey']='#d3d3d3';named_colors['lightpink']='#ffb6c1';named_colors['lightsalmon']='#ffa07a';named_colors['lightseagreen']='#20b2aa';named_colors['lightskyblue']='#87cefa';named_colors['lightslategray']='#778899';named_colors['lightsteelblue']='#b0c4de';named_colors['lightyellow']='#ffffe0';named_colors['lime']='#00ff00';named_colors['limegreen']='#32cd32';named_colors['linen']='#faf0e6';named_colors['magenta']='#ff00ff';named_colors['maroon']='#800000';named_colors['mediumaquamarine']='#66cdaa';named_colors['mediumblue']='#0000cd';named_colors['mediumorchid']='#ba55d3';named_colors['mediumpurple']='#9370db';named_colors['mediumseagreen']='#3cb371';named_colors['mediumslateblue']='#7b68ee';named_colors['mediumspringgreen']='#00fa9a';named_colors['mediumturquoise']='#48d1cc';named_colors['mediumvioletred']='#c71585';named_colors['midnightblue']='#191970';named_colors['mintcream']='#f5fffa';named_colors['mistyrose']='#ffe4e1';named_colors['moccasin']='#ffe4b5';named_colors['navajowhite']='#ffdead';named_colors['navy']='#000080';named_colors['oldlace']='#fdf5e6';named_colors['olive']='#808000';named_colors['olivedrab']='#6b8e23';named_colors['orange']='#ffa500';named_colors['orangered']='#ff4500';named_colors['orchid']='#da70d6';named_colors['palegoldenrod']='#eee8aa';named_colors['palegreen']='#98fb98';named_colors['paleturquoise']='#afeeee';named_colors['palevioletred']='#db7093';named_colors['papayawhip']='#ffefd5';named_colors['peachpuff']='#ffdab9';named_colors['peru']='#cd853f';named_colors['pink']='#ffc0cb';named_colors['plum']='#dda0dd';named_colors['powderblue']='#b0e0e6';named_colors['purple']='#800080';named_colors['red']='#ff0000';named_colors['rosybrown']='#bc8f8f';named_colors['royalblue']='#4169e1';named_colors['saddlebrown']='#8b4513';named_colors['salmon']='#fa8072';named_colors['sandybrown']='#f4a460';named_colors['seagreen']='#2e8b57';named_colors['seashell']='#fff5ee';named_colors['sienna']='#a0522d';named_colors['silver']='#c0c0c0';named_colors['skyblue']='#87ceeb';named_colors['slateblue']='#6a5acd';named_colors['slategray']='#708090';named_colors['snow']='#fffafa';named_colors['springgreen']='#00ff7f';named_colors['steelblue']='#4682b4';named_colors['tan']='#d2b48c';named_colors['teal']='#008080';named_colors['thistle']='#d8bfd8';named_colors['tomato']='#ff6347';named_colors['turquoise']='#40e0d0';named_colors['violet']='#ee82ee';named_colors['wheat']='#f5deb3';named_colors['white']='#ffffff';named_colors['whitesmoke']='#f5f5f5';named_colors['yellow']='#ffff00';named_colors['yellowgreen']=['#9acd32'];if(named_colors[kwd.toLowerCase()]!=null)
return named_colors[kwd.toLowerCase()];else
return null;}
SpawColor.parseRGB=function(rgbstr)
{var nc=new SpawColor();var r,g,b;if(!isNaN(parseInt(rgbstr)))
{var n=parseInt(rgbstr);r=n%256;n=Math.floor(n/256);g=n%256;n=Math.floor(n/256);b=n%256;nc.setRGB(r,g,b);}
else if(rgbstr.toLowerCase().indexOf('rgb(')==0)
{r=parseInt(rgbstr.substring(4,rgbstr.indexOf(',')));g=parseInt(rgbstr.substring(rgbstr.indexOf(',')+1,rgbstr.lastIndexOf(',')));b=parseInt(rgbstr.substring(rgbstr.lastIndexOf(',')+1,rgbstr.indexOf(')')));nc.setRGB(r,g,b);}
else
{nc.setRGBFromHTML(rgbstr);}
return nc;}
SpawColor.prototype.updateRGBFromHSB=function()
{var r,g,b;var fS=this.saturation/100;var fB=this.brightness/100;var hi=Math.floor(this.hue/60)%6;var f=this.hue/60-hi;var p=fB*(1-fS);var q=fB*(1-f*fS);var t=fB*(1-(1-f)*fS);switch(hi)
{case 0:r=Math.round(fB*255);g=Math.round(t*255);b=Math.round(p*255);break;case 1:r=Math.round(q*255);g=Math.round(fB*255);b=Math.round(p*255);break;case 2:r=Math.round(p*255);g=Math.round(fB*255);b=Math.round(t*255);break;case 3:r=Math.round(p*255);g=Math.round(q*255);b=Math.round(fB*255);break;case 4:r=Math.round(t*255);g=Math.round(p*255);b=Math.round(fB*255);break;case 5:r=Math.round(fB*255);g=Math.round(p*255);b=Math.round(q*255);break;}
this.red=r;this.green=g;this.blue=b;}
SpawColor.prototype.updateHSBFromRGB=function()
{var h,s;var r=this.red/255;var g=this.green/255;var b=this.blue/255;var mx=Math.max(this.red,this.green,this.blue)/255;var mn=Math.min(this.red,this.green,this.blue)/255;if(mx==mn)
{h=0;}
else if(mx==r&&g>=b)
{h=60*(g-b)/(mx-mn);}
else if(mx==r&&g<b)
{h=60*(g-b)/(mx-mn)+360;}
else if(mx==g)
{h=60*(b-r)/(mx-mn)+120;}
else if(mx==b)
{h=60*(r-g)/(mx-mn)+240;}
if(mx==0)
s=0;else
s=Math.round((1-mn/mx));this.hue=Math.round(h);this.saturation=Math.round(s*100);this.brightness=Math.round(mx*100);}
SpawColor.prototype.dec2hex=function(dec)
{var result='';var num=dec;while(num/16>=1||num%16>0)
{var ch=num%16;if(ch>=10)
{switch(ch)
{case 10:ch='a';break;case 11:ch='b';break;case 12:ch='c';break;case 13:ch='d';break;case 14:ch='e';break;case 15:ch='f';break;}}
result=''+ch+result;num=Math.floor(num/16);}
return result;}
SpawColor.prototype.hex2dec=function(hex)
{var l=hex.length;var p=0;var result=0;for(var i=l-1;i>=0;i--)
{p=l-i-1;var c=hex.charAt(i);if(!isNaN(parseInt(c)))
{result+=parseInt(c)*Math.pow(16,p);}
else
{switch(c.toLowerCase())
{case'a':result+=10*Math.pow(16,p);break;case'b':result+=11*Math.pow(16,p);break;case'c':result+=12*Math.pow(16,p);break;case'd':result+=13*Math.pow(16,p);break;case'e':result+=14*Math.pow(16,p);break;case'f':result+=15*Math.pow(16,p);break;}}}
return result;}
SpawColor.prototype.addZeroes=function(source,num)
{var result=source;while(result.length<num)
{result='0'+result;}
return result;}
SpawColor.prototype.getHtmlColor=function()
{return result='#'+this.addZeroes(this.dec2hex(this.red),2)+this.addZeroes(this.dec2hex(this.green),2)+this.addZeroes(this.dec2hex(this.blue),2);}
function SpawContextMenu(editor)
{this.editor=editor;}
SpawContextMenu.prototype.editor;SpawContextMenu.prototype.enclosure;SpawContextMenu.prototype.show=function(event)
{var last_tbn='';this.enclosure=document.createElement("div");this.enclosure.className=this.editor.theme.prefix+'contextmenu';this.enclosure.style.position="absolute";this.enclosure.style.left=this.editor.getPageOffsetLeft()+event.clientX+"px";this.enclosure.style.top=this.editor.getPageOffsetTop()+event.clientY+"px";this.enclosure.style.zIndex=15000;var ed=this.editor.controlled_by;for(var i=0;i<ed.toolbar_items.length;i++)
{if(ed.toolbar_items[i].on_enabled_check&&ed.toolbar_items[i].on_enabled_check!='')
{if(ed.toolbar_items[i].on_click&&ed.toolbar_items[i].show_in_context_menu)
{if(eval("SpawPG"+ed.toolbar_items[i].module
+'.'+ed.toolbar_items[i].on_enabled_check+'(this.editor, ed.toolbar_items[i])'))
{if(last_tbn!=''&&ed.toolbar_items[i].toolbar_name!=last_tbn)
{var sep=document.createElement("div");sep.className=this.editor.theme.prefix+'contextmenuseparator';this.enclosure.appendChild(sep);}
last_tbn=ed.toolbar_items[i].toolbar_name;var mitem=document.createElement("div");var checkmark=document.createElement("img");checkmark.src=SpawEngine.getSpawDir()+'plugins/core/lib/theme/'+this.editor.theme.prefix+'/img/checkmark.gif';checkmark.style.visibility='hidden';checkmark.className=this.editor.theme.prefix+'checkmark';if(ed.toolbar_items[i].on_pushed_check&&ed.toolbar_items[i].on_pushed_check!='')
{if(eval("SpawPG"+ed.toolbar_items[i].module
+'.'+ed.toolbar_items[i].on_pushed_check+'(this.editor, ed.toolbar_items[i])'))
{checkmark.style.visibility='visible';}}
mitem.appendChild(checkmark);mitem.appendChild(document.createTextNode(document.getElementById(ed.toolbar_items[i].id).title));mitem.style.cursor="default";mitem.style.whiteSpace="nowrap";mitem.setAttribute("unselectable","on");mitem.className=this.editor.theme.prefix+"contextmenuitem";mitem.onmouseover=new Function("this.className = '"+this.editor.theme.prefix+"contextmenuitemover'");mitem.onmouseout=new Function("this.className = '"+this.editor.theme.prefix+"contextmenuitem'");if(mitem.attachEvent)
{mitem.attachEvent("onclick",new Function("SpawEngine.active_context_menu.hide(); SpawEngine.active_context_menu = null;"
+"SpawPG"+ed.toolbar_items[i].module+'.'+ed.toolbar_items[i].on_click+'('+this.editor.name+'_obj, '+ed.name+'_obj.toolbar_items['+i+'], null)'));}
else if(mitem.addEventListener)
{mitem.addEventListener("click",new Function("SpawEngine.active_context_menu.hide(); SpawEngine.active_context_menu = null;"
+"SpawPG"+ed.toolbar_items[i].module+'.'+ed.toolbar_items[i].on_click+'('+this.editor.name+'_obj, '+ed.name+'_obj.toolbar_items['+i+'], null)'),false);}
this.enclosure.appendChild(mitem);}}}}
if(this.enclosure.innerHTML!='')
{document.body.appendChild(this.enclosure);return true;}
else
{return false;}}
SpawContextMenu.prototype.hide=function()
{if(this.enclosure!=null)
{document.body.removeChild(this.enclosure);}}
function SpawEditor(name)
{this.name=name;this.toolbar_items=new Array();this.pages=new Array();this.tabs=new Array();this.controlled_editors=new Array();this.config=new Array();this.document=window.document;}
SpawEditor.prototype.name;SpawEditor.prototype.scid;SpawEditor.prototype.stylesheet;SpawEditor.prototype.config;SpawEditor.prototype.getConfigValue=function(name)
{return this.config[name];}
SpawEditor.prototype.setConfigValue=function(name,value)
{this.config[name]=value;}
SpawEditor.prototype.getRequestUriConfigValue=function()
{return this.getConfigValue("__request_uri");}
SpawEditor.prototype.isInitialized=function()
{var result=true;for(var i=0;i<this.pages.length;i++)
{if(!this.pages[i].initialized)
{result=false;break;}}
return result;}
SpawEditor.prototype.toolbar_items;SpawEditor.prototype.addToolbarItem=function(tbi,toolbar_name)
{if(tbi.base_image_url)
this.theme.preloadImages(tbi);tbi.editor=this;tbi.toolbar_name=toolbar_name;this.toolbar_items.push(tbi);}
SpawEditor.prototype.getToolbarItem=function(id)
{for(var i=0;i<this.toolbar_items.length;i++)
{if(this.toolbar_items[i].id==id)
{return this.toolbar_items[i];}}
return null;}
SpawEditor.prototype.enableEditingMode=function(tbi)
{if(tbi&&tbi.editor.getActivePage().editing_mode_tbi&&tbi.editor.getActivePage().editing_mode_tbi!=null)
{var mbt=this.document.getElementById(tbi.editor.getActivePage().editing_mode_tbi.id);mbt.disabled=false;tbi.editor.theme.buttonOut(tbi.editor.getActivePage().editing_mode_tbi,mbt);}}
SpawEditor.prototype.disableEditingMode=function(tbi)
{if(tbi&&tbi.editor.getActivePage().editing_mode_tbi&&tbi.editor.getActivePage().editing_mode_tbi!=null)
{var mbt=this.document.getElementById(tbi.editor.getActivePage().editing_mode_tbi.id);mbt.disabled=true;tbi.editor.theme.buttonOff(tbi.editor.getActivePage().editing_mode_tbi,mbt);}}
SpawEditor.prototype.pages;SpawEditor.prototype.addPage=function(page)
{this.pages.push(page);this.addTab(page);}
SpawEditor.prototype.getPage=function(id)
{for(var i=0;i<this.pages.length;i++)
{if(this.pages[i].name==id)
return this.pages[i];}
return null;}
SpawEditor.prototype.active_page;SpawEditor.prototype.setActivePage=function(id)
{if(this.active_page&&(this.active_page.name!=id||SpawEngine.active_editor!=this))
{SpawEngine.handleEvent("spawbeforepageswitch",null,null,this.name);this.getTab(this.active_page.name).setInactive();this.hidePage(this.active_page);this.enableEditingMode(this.active_page.editing_mode_tbi);this.active_page=this.getPage(id);this.getTab(this.active_page.name).setActive();this.showPage(this.active_page);this.disableEditingMode(this.active_page.editing_mode_tbi);SpawEngine.setActiveEditor(this);SpawEngine.handleEvent("spawpageswitch",null,null,this.name);setTimeout(this.name+'_obj.updateToolbar();',10);}}
SpawEditor.prototype.getActivePage=function()
{return this.active_page;}
SpawEditor.prototype.pageOffsetParent;SpawEditor.prototype.currentTextAreaWidth='100%';SpawEditor.prototype.hidePage=function(page)
{var pta=this.getPageInput(page.name);var pif=this.getPageIframeObject(page.name);if(page.editing_mode=='design')
this.currentTextAreaWidth=pif.offsetWidth+'px';else
this.currentTextAreaWidth=pta.offsetWidth+'px';pta.style.display='none';pif.style.display='none';}
SpawEditor.prototype.showPage=function(page)
{var pta=this.getPageInput(page.name);var pif=this.getPageIframeObject(page.name);var pdoc=this.getPageDoc(page.name);if(page.editing_mode=='design')
{pta.style.display='none';pif.style.display='inline';if(this.Enabled)
pdoc.designMode='on';}
else
{pta.style.width=this.currentTextAreaWidth;pta.style.display='inline';pif.style.display='none';}
this.focus();}
SpawEditor.prototype.tabs;SpawEditor.prototype.addTab=function(page)
{this.tabs.push(new SpawTab(page));}
SpawEditor.prototype.getTab=function(page_name)
{for(var i=0;i<this.tabs.length;i++)
{if(this.tabs[i].page.name==page_name)
return this.tabs[i];}
return null;}
SpawEditor.prototype.floating_mode=false;SpawEditor.prototype.controlled_editors;SpawEditor.prototype.addControlledEditor=function(editor)
{this.controlled_editors.push(editor);}
SpawEditor.prototype.isControlledEditor=function(name)
{for(var i=0;i<this.controlled_editors.length;i++)
{if(this.controlled_editors[i].name==name)
return true;}
return false;}
SpawEditor.prototype.controlled_by;SpawEditor.prototype.getCurrentEditor=function()
{return SpawEngine.getActiveEditor();}
SpawEditor.prototype.getTargetEditor=function()
{if(this.controlled_by==this&&this.controlled_editors.length<=1)
return this;else
return SpawEngine.getActiveEditor();}
SpawEditor.prototype.theme;SpawEditor.prototype.setTheme=function(theme)
{this.theme=theme;}
SpawEditor.prototype.getTheme=function()
{return this.theme;}
SpawEditor.prototype.lang;SpawEditor.prototype.setLang=function(lang)
{this.lang=lang;}
SpawEditor.prototype.getLang=function()
{return this.lang;}
SpawEditor.prototype.output_charset;SpawEditor.prototype.setOutputCharset=function(output_charset)
{this.output_charset=output_charset;}
SpawEditor.prototype.getOutputCharset=function()
{return this.output_charset;}
SpawEditor.prototype.onLoadHookup=function()
{var spaw_tmpstr="";if(window.onload!=null)
{var rgld=/\{([^}]+)/;var xx=rgld.exec(window.onload.toString());spaw_tmpstr=xx[1];}
window.onload=new Function(this.name+'_obj.initialize();'+spaw_tmpstr);}
SpawEditor.prototype.getPageInput=function(page_name)
{return this.document.getElementById(page_name);}
SpawEditor.prototype.getPageIframeObject=function(page_name)
{return this.document.getElementById(page_name+'_rEdit');}
SpawEditor.prototype.getActivePageDoc=function()
{return this.getPageDoc(this.active_page.name);}
SpawEditor.prototype.currentToolbarX;SpawEditor.prototype.currentToolbarY;SpawEditor.prototype.lastMousePosX;SpawEditor.prototype.lastMousePosY;SpawEditor.prototype.isToolbarMoving=false;SpawEditor.prototype.floatingMouseDown=function(event)
{this.currentToolbarX=this.document.getElementById(this.name+'_toolbox').offsetLeft;this.currentToolbarY=this.document.getElementById(this.name+'_toolbox').offsetTop;this.lastMousePosX=event.clientX;this.lastMousePosY=event.clientY;this.isToolbarMoving=true;SpawEngine.movingToolbar=this;}
SpawEditor.prototype.floatingToolbarX=100;SpawEditor.prototype.floatingToolbarY=10;SpawEditor.prototype.positionFloatingToolbar=function()
{this.document.getElementById(this.controlled_by.name+'_toolbox').style.left=this.getPageOffsetLeft()+this.floatingToolbarX+"px";this.document.getElementById(this.controlled_by.name+'_toolbox').style.top=this.getPageOffsetTop()+this.floatingToolbarY+"px";}
SpawEditor.prototype.saveFloatingToolbarPosition=function(x,y)
{this.floatingToolbarX=x-this.getPageOffsetLeft();this.floatingToolbarY=y-this.getPageOffsetTop();}
SpawEditor.prototype.getPageOffsetLeft=function()
{var elm=this.getPageIframeObject(this.active_page?this.active_page.name:this.name);var res=elm.offsetLeft;while((elm=elm.offsetParent)!=null)
{res+=elm.offsetLeft;}
return res;}
SpawEditor.prototype.getPageOffsetTop=function()
{var elm=this.getPageIframeObject(this.active_page?this.active_page.name:this.name);var res=elm.offsetTop;while((elm=elm.offsetParent)!=null)
{res+=elm.offsetTop;}
return res;}
SpawEditor.prototype.isResizing=false;SpawEditor.prototype.isVerticalResizingAllowed=function()
{var res=this.getConfigValue("resizing_directions");res=res?res.toLowerCase():res;if(res=='vertical'||res=='both')
return true;else
return false;}
SpawEditor.prototype.isHorizontalResizingAllowed=function()
{var res=this.getConfigValue("resizing_directions");res=res?res.toLowerCase():res;if(res=='horizontal'||res=='both')
return true;else
return false;}
SpawEditor.prototype.resizingGripMouseDown=function(event)
{this.lastMousePosX=event.clientX;this.lastMousePosY=event.clientY;this.isResizing=true;SpawEngine.resizingEditor=this;if(event.preventDefault)
event.preventDefault();}
SpawEditor.prototype.finalizeResizing=function()
{var resobj=this.isInDesignMode()?this.getPageIframeObject(this.getActivePage().name):this.getPageInput(this.getActivePage().name);for(var i=0;i<this.pages.length;i++)
{var pif=this.getPageIframeObject(this.pages[i].name);var pta=this.getPageInput(this.pages[i].name);pif.style.height=resobj.offsetHeight+'px';pta.style.height=resobj.offsetHeight+'px';pta.style.width=resobj.offsetWidth+'px';}}
SpawEditor.prototype.updateToolbar=function()
{if(this.controlled_by!=this)
this.updateEditorToolbar(this.controlled_by);this.updateEditorToolbar(this);}
SpawEditor.prototype.updateEditorToolbar=function(ed)
{for(var i=0;i<ed.toolbar_items.length;i++)
{if(ed.toolbar_items[i].on_enabled_check&&ed.toolbar_items[i].on_enabled_check!='')
{if(eval("SpawPG"+ed.toolbar_items[i].module
+'.'+ed.toolbar_items[i].on_enabled_check+'(this, ed.toolbar_items[i])'))
{this.document.getElementById(ed.toolbar_items[i].id).disabled=false;ed.toolbar_items[i].is_enabled=true;if(ed.toolbar_items[i].on_click)
{ed.theme.buttonOut(ed.toolbar_items[i],this.document.getElementById(ed.toolbar_items[i].id));}
else
{ed.theme.dropdownOut(ed.toolbar_items[i],this.document.getElementById(ed.toolbar_items[i].id));}
if(ed.toolbar_items[i].on_pushed_check&&ed.toolbar_items[i].on_pushed_check!='')
{if(eval("SpawPG"+ed.toolbar_items[i].module
+'.'+ed.toolbar_items[i].on_pushed_check+'(this, ed.toolbar_items[i])'))
{ed.toolbar_items[i].is_pushed=true;ed.theme.buttonDown(ed.toolbar_items[i],this.document.getElementById(ed.toolbar_items[i].id));}
else
{ed.toolbar_items[i].is_pushed=false;ed.theme.buttonOut(ed.toolbar_items[i],this.document.getElementById(ed.toolbar_items[i].id));}}
if(ed.toolbar_items[i].on_status_check&&ed.toolbar_items[i].on_status_check!='')
{var val=eval("SpawPG"+ed.toolbar_items[i].module
+'.'+ed.toolbar_items[i].on_status_check+'(this, ed.toolbar_items[i])');var dd=this.document.getElementById(ed.toolbar_items[i].id);if(val)
{for(var oi=0;oi<dd.options.length;oi++)
{if(dd.options[oi].value==val)
dd.options[oi].selected=true;}}
else
{dd.selectedIndex=0;}}}
else
{this.document.getElementById(ed.toolbar_items[i].id).disabled=true;ed.toolbar_items[i].is_enabled=false;if(ed.toolbar_items[i].on_click)
{ed.theme.buttonOff(ed.toolbar_items[i],this.document.getElementById(ed.toolbar_items[i].id));}
else
{ed.theme.dropdownOff(ed.toolbar_items[i],this.document.getElementById(ed.toolbar_items[i].id));}}}}}
SpawEditor.prototype.updatePageInput=function(page)
{var pdoc=this.getPageDoc(page.name);var pta=this.getPageInput(page.name);pta.value=this.getPageHtml(page);}
SpawEditor.prototype.updatePageDoc=function(page)
{var pdoc=this.getPageDoc(page.name);var pta=this.getPageInput(page.name);var htmlValue=pta.value;if(document.attachEvent)
{htmlValue='<span id="spaw2_script_workaround">.</span>'+htmlValue;}
pdoc.body.innerHTML=htmlValue;if(document.attachEvent)
{var tmpSpan=pdoc.getElementById("spaw2_script_workaround");tmpSpan.parentNode.removeChild(tmpSpan);}
this.flash2img();}
SpawEditor.prototype.getPageHtml=function(page)
{SpawEngine.handleEvent("spawgethtml",null,"page_doc",this.name);var pdoc=this.getPageDoc(page.name);var pta=this.getPageInput(page.name);var result;if(page.editing_mode=="html")
{result=pta.value;}
else if(page.editing_mode=="design")
{this.removeGlyphs(pdoc.body);this.img2flash();this.stripAbsoluteUrls();if(this.getConfigValue("rendering_mode")=="builtin")
{result=pdoc.body.innerHTML;}
else
{result=this.dom2xml(pdoc.body,'');pta.value=result;this.updatePageDoc(page);}}
if(this.getConfigValue('convert_html_entities'))
result=this.convertToEntities(result);return result;}
SpawEditor.updateFields=function(editor,event)
{editor.updateFields();}
SpawEditor.prototype.updateFields=function()
{for(var i=0;i<this.pages.length;i++)
{this.updatePageInput(this.pages[i]);}}
SpawEditor.prototype.spawSubmit=function()
{SpawEngine.updateFields();var frm=this.getPageInput(this.pages[0].name).form;document.forms[0].setAttribute("__spawsubmiting","true");frm.formSubmit();}
SpawEditor.prototype.dom2xml=function(node,indent,inParagraph)
{var xbuf='';var f_indent='';var e_indent='';var f_crlf='';var e_crlf='';for(var i=0;i<node.childNodes.length;i++)
{var chnode=node.childNodes[i];if(chnode.nodeType==3)
{if(SpawUtils.trim(chnode.nodeValue).length>0)
xbuf+=SpawUtils.trimLineBreaks(SpawUtils.htmlEncode(chnode.nodeValue));else if(chnode.nodeValue.length>0)
xbuf+=" ";}
else if(chnode.nodeType==8)
{xbuf+="<!--"+chnode.nodeValue+"-->";}
else if(chnode.nodeType==1)
{if(chnode.getAttribute("__spawprocessed")==null)
{chnode.setAttribute("__spawprocessed",true);var attr_str='';for(var j=0;j<chnode.attributes.length;j++)
{var attr=chnode.attributes[j];if(attr.nodeValue!=null&&(chnode.getAttribute(attr.nodeName,2)!=null||chnode.getAttribute(attr.nodeName,0)!=null)&&attr.specified&&attr.nodeName.toLowerCase()!="__spawprocessed"&&attr.nodeName.toLowerCase().indexOf("_moz")!=0)
{var attrval=chnode.getAttribute(attr.nodeName,2);var attrnm=attr.nodeName.toLowerCase();var tgnm=chnode.tagName.toLowerCase();if(attrval==null)
attrval=chnode.getAttribute(attr.nodeName,0);if(tgnm!='font')
{if(attrnm!='class'&&attrnm!='style'&&attrnm!='href'&&attrnm!='src'&&attrnm!='shape'&&attrnm!='coords'&&attrnm!='type'&&attrnm!='value'&&attrnm!='enctype')
attr_str+=' '+attrnm+'="'+attrval+'"';}
else
{if(attrnm=='face')
chnode.style.fontFamily=attrval;else if(attrnm=='size')
{switch(attrval)
{case'1':attrval='xx-small';break;case'2':attrval='x-small';break;case'3':attrval='small';break;case'4':attrval='medium';break;case'5':attrval='large';break;case'6':attrval='x-large';break;case'7':attrval='xx-large';break;default:attrval='medium';break;}
chnode.style.fontSize=attrval;}
else if(attrnm=='color')
chnode.style.color=attrval;attr_str='';}}}
if(chnode.style.cssText&&chnode.style.cssText!='')
attr_str+=' style="'+chnode.style.cssText+'"';if(chnode.className&&chnode.className!='')
attr_str+=' class="'+chnode.className+'"';if(chnode.type&&chnode.type!='')
attr_str+=' type="'+chnode.type+'"';if(chnode.value&&chnode.value!=''&&!(chnode.tagName.toLowerCase()=='li'&&chnode.value=='-1'))
attr_str+=' value="'+chnode.value+'"';if(chnode.enctype&&chnode.enctype!=''&&chnode.enctype!='application/x-www-form-urlencoded')
attr_str+=' enctype="'+chnode.enctype+'"';if(chnode.href&&chnode.href!=''&&chnode.tagName.toLowerCase()!='img')
attr_str+=' href="'+this.getStrippedAbsoluteUrl(chnode.href,false).replace(/&amp;/g,"&").replace(/&/g,"&amp;")+'"';if(chnode.src&&chnode.src!='')
attr_str+=' src="'+this.getStrippedAbsoluteUrl(chnode.src,true).replace(/&amp;/g,"&").replace(/&/g,"&amp;")+'"';if(chnode.coords&&chnode.coords!='')
attr_str+=' coords="'+chnode.coords+'"';if(chnode.shape&&chnode.shape!='')
attr_str+=' shape="'+chnode.shape+'"';if(this.getConfigValue("beautify_xhtml_output"))
{switch(chnode.tagName.toLowerCase())
{case"p":case"td":case"th":case"label":case"li":f_indent=indent;f_crlf='\n';e_indent='';e_crlf='';break;case"table":case"thead":case"tfoot":case"tbody":case"tr":case"div":case"ul":case"ol":case"script":case"style":f_indent=indent;f_crlf='\n';e_indent=indent;e_crlf='\n';break;default:f_indent='';f_crlf='';e_indent='';e_crlf='';}}
if(chnode.tagName.toLowerCase()!="script"&&chnode.tagName.toLowerCase()!="style")
{var tag_name=(chnode.tagName.toLowerCase()!='font')?chnode.tagName.toLowerCase():'span';var re_bold=/^\W*style\W+font-weight\W+bold\W+$/i;var re_italic=/^\W*style\W+font-style\W+italic\W+$/i;if(tag_name=='span'){if(re_bold.test(attr_str)){tag_name='strong';attr_str='';}else if(re_italic.test(attr_str)){tag_name='em';attr_str='';}}
var pInParagraph=false;var closingP='';if(document.attachEvent)
{if(inParagraph==true&&tag_name=='p')
{closingP='</p>';pInParagraph=false;inParagraph=false;}
else if(inParagraph==true||tag_name=='p')
pInParagraph=true;}
if(chnode.childNodes.length>0)
{var innercode=this.dom2xml(chnode,indent+((f_indent!="tmp")?"  ":""),pInParagraph);if(SpawUtils.trim(innercode)=='')
innercode='&nbsp;';var closingTag="</"+tag_name+">";if(document.attachEvent)
{if(tag_name=='p'&&innercode.indexOf("</p>")!=-1)
closingTag="";}
xbuf+=closingP+f_crlf+f_indent+"<"+SpawUtils.trim(tag_name+attr_str)+">"+innercode+e_crlf+e_indent+closingTag;}
else if(chnode.tagName.indexOf("/")==-1)
{if(tag_name=="img"||tag_name=="br"||tag_name=="wbr"||tag_name=="hr"||tag_name=="input")
{xbuf+=f_crlf+f_indent+"<"+SpawUtils.trim(tag_name+attr_str)+" />"+e_crlf+e_indent;}
else
{if(tag_name!="b"&&tag_name!="i"&&tag_name!="u"&&tag_name!="strike"&&tag_name!="strong"&&tag_name!="em"&&tag_name!="i"&&tag_name!="span")
{var innercode='';if(tag_name=='p')
innercode='&nbsp;';xbuf+=f_crlf+f_indent+"<"+SpawUtils.trim(tag_name+attr_str)+">"+innercode+"</"+tag_name+">";}}}}
else
{xbuf+=f_crlf+f_indent+"<"+SpawUtils.trim(chnode.tagName.toLowerCase()+attr_str)+">"+SpawUtils.trim(chnode.innerHTML)+e_crlf+e_indent+"</"+chnode.tagName.toLowerCase()+">";}}}}
return xbuf;}
SpawEditor.prototype.getCleanCode=function(node,clean_type)
{var xbuf='';for(var i=0;i<node.childNodes.length;i++)
{var chnode=node.childNodes[i];if(chnode.nodeType==3)
{if(SpawUtils.trim(chnode.nodeValue).length>0)
xbuf+=chnode.nodeValue.replace(/\u00A0/g,"&nbsp;");}
else if(chnode.nodeType==8)
{xbuf+="<!--"+chnode.nodeValue+"-->";}
else if(chnode.nodeType==1)
{if(chnode.getAttribute("__spawprocessed")==null)
{chnode.setAttribute("__spawprocessed",true);var attr_str='';for(var j=0;j<chnode.attributes.length;j++)
{var attr=chnode.attributes[j];if(attr.nodeValue!=null&&chnode.getAttribute(attr.nodeName,2)!=null&&attr.specified&&attr.nodeName.toLowerCase()!="__spawprocessed"&&attr.nodeName.toLowerCase().indexOf("_moz")!=0)
{var attrval=chnode.getAttribute(attr.nodeName,2);if(attr.nodeName.toLowerCase()!='class'&&attr.nodeName.toLowerCase()!='style'&&attr.nodeName.toLowerCase()!='type'&&attr.nodeName.toLowerCase()!='value'&&attr.nodeName.toLowerCase()!='enctype')
attr_str+=' '+attr.nodeName.toLowerCase()+'="'+attrval+'"';}}
if(chnode.type&&chnode.type!='')
attr_str+=' type="'+chnode.type+'"';if(chnode.value&&chnode.value!='')
attr_str+=' value="'+chnode.value+'"';if(chnode.enctype&&chnode.enctype!=''&&chnode.enctype!='application/x-www-form-urlencoded')
attr_str+=' enctype="'+chnode.enctype+'"';if(chnode.tagName.toLowerCase()!="script")
{if(chnode.childNodes.length>0)
{if(chnode.tagName.indexOf(":")==-1&&chnode.tagName.toLowerCase()!='font'&&chnode.tagName.toLowerCase()!='div'&&chnode.tagName.toLowerCase()!='span')
xbuf+="<"+SpawUtils.trim(chnode.tagName.toLowerCase()+attr_str)+">"+this.getCleanCode(chnode,clean_type)+"</"+chnode.tagName.toLowerCase()+">";else
xbuf+=this.getCleanCode(chnode,clean_type);}
else if(chnode.tagName.indexOf("/")==-1)
{if(chnode.tagName.indexOf(":")==-1&&chnode.tagName.toLowerCase()!='font'&&chnode.tagName.toLowerCase()!='div'&&chnode.tagName.toLowerCase()!='span')
{if(chnode.tagName.toLowerCase()=="img"||chnode.tagName.toLowerCase()=="br"||chnode.tagName.toLowerCase()=="wbr"||chnode.tagName.toLowerCase()=="hr"||chnode.tagName.toLowerCase()=="input")
{xbuf+="<"+SpawUtils.trim(chnode.tagName.toLowerCase()+attr_str)+" />";}
else
{xbuf+="<"+SpawUtils.trim(chnode.tagName.toLowerCase()+attr_str)+"></"+chnode.tagName.toLowerCase()+">";}}}}
else
{xbuf+="<"+SpawUtils.trim(chnode.tagName.toLowerCase()+attr_str)+">"+chnode.innerHTML+"</"+chnode.tagName.toLowerCase()+">";}}}}
return xbuf;}
SpawEditor.prototype.cleanPageCode=function(clean_type)
{var pname=this.getActivePage().name;var pdoc=this.getActivePageDoc();var pta=this.getPageInput(pname);pta.value=this.getCleanCode(pdoc.body,clean_type);this.updatePageDoc(this.getActivePage());}
SpawEditor.prototype.showStatus=function(message)
{var sb=this.document.getElementById(this.name+'_status');if(sb&&!document.attachEvent)
{sb.innerHTML=message;}}
SpawEditor.rightClick=function(editor,event)
{editor.rightClick(editor,event);}
SpawEditor.prototype.rightClick=function(editor,event)
{if(SpawEngine.active_context_menu!=null)
SpawEngine.active_context_menu.hide();var cm=new SpawContextMenu(this);if(cm.show(event))
{SpawEngine.active_context_menu=cm;if(event.preventDefault)
event.preventDefault();else
event.returnValue=false;}}
SpawEditor.hideContextMenu=function(editor,event)
{editor.hideContextMenu(editor,event);}
SpawEditor.prototype.hideContextMenu=function(editor,event)
{if(SpawEngine.active_context_menu)
{SpawEngine.active_context_menu.hide();SpawEngine.active_context_menu=null;}}
SpawEditor.prototype.getSelectedElementByTagName=function(tagName)
{var result=null;var elm=this.getSelectionParent();while(elm&&elm.tagName&&elm.tagName.toLowerCase()!=tagName.toLowerCase()&&elm.tagName.toLowerCase()!='body')
elm=elm.parentNode;if(elm&&elm.tagName&&elm.tagName.toLowerCase()!='body')
result=elm;return result;}
SpawEditor.prototype.getAnchors=function()
{var anchors=new Array();var pdoc=this.getActivePageDoc();var links=pdoc.getElementsByTagName("a");for(var i=0;i<links.length;i++)
{if(links[i].name&&links[i].name!='')
anchors.push(links[i]);}
return anchors;}
SpawEditor.prototype.show_glyphs=true;SpawEditor.prototype.isInDesignMode=function()
{return(this.getActivePage().editing_mode=="design");}
SpawEditor.prototype.getStrippedAbsoluteUrl=function(url,host_only)
{if(this.getConfigValue('strip_absolute_urls'))
{var pdoc=this.getActivePageDoc();var curl=pdoc.location.href;var di=curl.lastIndexOf('/',curl.lastIndexOf('?')!=-1?curl.lastIndexOf('?'):curl.length);var cdir=curl;if(di!=-1)
cdir=curl.substr(0,di+1);var chost=curl;var hi=curl.indexOf('/',curl.indexOf('://')!=-1?(curl.indexOf('://')+3):curl.length);if(hi!=-1)
chost=curl.substr(0,hi);if(url.toLowerCase().indexOf(curl.toLowerCase())==0&&!host_only)
{url=url.substr(curl.length);}
else if(url.toLowerCase().indexOf(cdir.toLowerCase())==0&&!host_only)
{url=url.substr(cdir.length);}
else if(url.toLowerCase().indexOf(chost.toLowerCase())==0)
{url=url.substr(chost.length);}}
return url;}
SpawEditor.prototype.stripAbsoluteUrls=function()
{if(this.getConfigValue('strip_absolute_urls'))
{var pdoc=this.getActivePageDoc();var links=pdoc.getElementsByTagName("a");for(var i=0;i<links.length;i++)
{if(links[i].href&&links[i].href!='')
links[i].href=this.getStrippedAbsoluteUrl(links[i].href,false);}
var imgs=pdoc.getElementsByTagName("img");for(var i=0;i<imgs.length;i++)
{if(imgs[i].src&&imgs[i].src!='')
imgs[i].src=this.getStrippedAbsoluteUrl(imgs[i].src,true);}}}
SpawEditor.prototype.stripAbsoluteUrl=function(elm)
{if(this.getConfigValue('strip_absolute_urls')&&elm&&elm.nodeType==1)
{if(elm.tagName.toLowerCase()=='a'&&elm.href&&elm.href!="")
{elm.href=this.getStrippedAbsoluteUrl(elm.href,false);}
else if(elm.tagName.toLowerCase()=='img'&&elm.src&&elm.src!="")
{elm.src=this.getStrippedAbsoluteUrl(elm.src,true);}}}
SpawEditor.prototype.flash2img=function()
{var pdoc=this.getActivePageDoc();var flashs_elm=pdoc.getElementsByTagName("EMBED");var flashs=new Array();for(var i=0;i<flashs_elm.length;i++)
{flashs[i]=flashs_elm[i];}
for(var i=0;i<flashs.length;i++)
{if(flashs[i].attributes.getNamedItem('src')!=null)
{var flash=pdoc.createElement("IMG");flash.setAttribute('src',SpawEngine.spaw_dir+'img/spacer100.gif?imgtype=flash&src='+flashs[i].getAttribute("src"));if(flashs[i].style.cssText!='')
{flash.setAttribute("__spaw_style",flashs[i].style.cssText);if(flashs[i].style.width!='')
flash.setAttribute('width',flashs[i].style.width);if(flashs[i].style.height!='')
flash.setAttribute('height',flashs[i].style.height);}
for(var j=0;j<flashs[i].attributes.length;j++)
{var attr=flashs[i].attributes[j];if(attr.nodeValue!=null&&(flashs[i].getAttribute(attr.nodeName,2)!=null||flashs[i].getAttribute(attr.nodeName,0)!=null)&&attr.specified&&attr.nodeName.toLowerCase().indexOf("_moz")!=0&&attr.nodeName.toLowerCase()!="src")
{var attrval=flashs[i].getAttribute(attr.nodeName,2);if(attrval==null)
attrval=flashs[i].getAttribute(attr.nodeName,0);flash.setAttribute(attr.nodeName.toLowerCase(),attrval);}}
flash.style.cssText="border: 1px solid #000000; background: url("+SpawEngine.spaw_dir+"img/flash.gif);";flashs[i].parentNode.replaceChild(flash,flashs[i]);}}}
SpawEditor.prototype.img2flash=function()
{var pdoc=this.getActivePageDoc();var imgs_elm=pdoc.getElementsByTagName("IMG");var imgs=new Array();for(var i=0;i<imgs_elm.length;i++)
{imgs[i]=imgs_elm[i];}
for(var i=0;i<imgs.length;i++)
{if(imgs[i].src.indexOf("spacer100.gif?imgtype=flash")!=-1)
{var flash=pdoc.createElement('EMBED');flash.setAttribute('type','application/x-shockwave-flash');flash.setAttribute('src',imgs[i].src.substring(imgs[i].src.indexOf("src=")+4));for(var j=0;j<imgs[i].attributes.length;j++)
{var attr=imgs[i].attributes[j];if(attr.nodeValue!=null&&(imgs[i].getAttribute(attr.nodeName,2)!=null||imgs[i].getAttribute(attr.nodeName,0)!=null)&&attr.specified&&attr.nodeName.toLowerCase().indexOf("_moz")!=0&&attr.nodeName.toLowerCase()!="src"&&attr.nodeName.toLowerCase()!="type"&&attr.nodeName.toLowerCase()!="style")
{var attrval=imgs[i].getAttribute(attr.nodeName,2);if(attrval==null)
attrval=imgs[i].getAttribute(attr.nodeName,0);flash.setAttribute(attr.nodeName.toLowerCase(),attrval);}}
if(imgs[i].getAttribute("__spaw_style",2)!=null)
{flash.style.cssText=imgs[i].getAttribute("__spaw_style",2);flash.removeAttribute("__spaw_style");}
imgs[i].parentNode.replaceChild(flash,imgs[i]);}}}
SpawEditor.prototype.current_context;SpawEditor.checkContext=function(editor,event)
{editor.checkContext(editor,event);}
SpawEditor.prototype.checkContext=function(editor,event)
{if(SpawEngine.getActiveEditor()!=this)
SpawEngine.setActiveEditor(this);var sp=this.getSelectionParent();if(this.current_context!=sp)
{this.updateToolbar();this.current_context=sp;}}
SpawEditor.prototype.focus=function()
{var obj=(this.isInDesignMode())?this.getPageIframe(this.getActivePage().name):this.getPageInput(this.getActivePage().name);if(obj.contentWindow)
obj.contentWindow.focus();else
obj.focus();}
function SpawEditorPage(name,caption,direction)
{this.name=name;this.caption=caption;if(direction!=null)
this.direction=direction;else
this.direction="ltr";}
SpawEditorPage.prototype.name;SpawEditorPage.prototype.caption;SpawEditorPage.prototype.direction;SpawEditorPage.prototype.value;SpawEditorPage.prototype.is_initialized=false;SpawEditorPage.prototype.document;SpawEditorPage.prototype.editing_mode="design";SpawEditorPage.prototype.editing_mode_tbi;
function SpawEngine()
{}
SpawEngine.spaw_dir;SpawEngine.setSpawDir=function(spaw_dir)
{SpawEngine.spaw_dir=spaw_dir;}
SpawEngine.getSpawDir=function()
{return(SpawEngine.spaw_dir);}
SpawEngine.platform;SpawEngine.setPlatform=function(platform)
{SpawEngine.platform=platform;}
SpawEngine.getPlatform=function()
{return(SpawEngine.platform);}
SpawEngine.addBrowserEventHandler=function(obj,evt,func)
{if(document.attachEvent)
{obj.attachEvent("on"+evt,func);}
else
{obj.addEventListener(evt,func,false);}}
SpawEngine.plugins=new Array();SpawEngine.registerPlugin=function(plugin_object)
{SpawEngine.plugins.push(plugin_object);}
SpawEngine.getPlugin=function(name)
{for(var i=0;i<SpawEngine.plugins.length;i++)
{if(SpawEngine.plugins[i].name==name)
return SpawEngine.plugins[i];}
return null;}
SpawEngine.editors=new Array();SpawEngine.registerEditor=function(editor)
{SpawEngine.editors.push(editor);}
SpawEngine.isEditorRegistered=function(name)
{for(var i=0;i<SpawEngine.editors.length;i++)
{if(SpawEngine.editors[i].name==name)
return true;}
return false;}
SpawEngine.getEditor=function(name)
{for(var i=0;i<SpawEngine.editors.length;i++)
{if(SpawEngine.editors[i].name==name)
return SpawEngine.editors[i];}
return null;}
SpawEngine.isInitialized=function()
{var result=true;for(var i=0;i<SpawEngine.editors.length;i++)
{if(!SpawEngine.editors[i].isInitialized())
{result=false;break;}}
return result;}
SpawEngine.updateFields=function()
{if(!document.forms[0].getAttribute("__spawsubmiting"))
{for(var i=0;i<SpawEngine.editors.length;i++)
{SpawEngine.editors[i].updateFields();}}}
SpawEngine.onSubmit=function()
{SpawEngine.handleEvent("spawbeforesubmit",null,null,null);SpawEngine.updateFields();}
SpawEngine.active_editor;SpawEngine.setActiveEditor=function(editor)
{if(SpawEngine.active_editor!=editor)
{SpawEngine.active_editor=editor;if(editor.floating_mode)
editor.positionFloatingToolbar();}}
SpawEngine.getActiveEditor=function()
{return SpawEngine.active_editor;}
SpawEngine.mouseMove=function(event)
{if(event==null&&window.event!=null)
{event=window.event;if(event.button!=undefined&&event.button!=1)
{if(SpawEngine.resizingEditor!=null)
{SpawEngine.resizingEditor.isResizing=false;SpawEngine.resizingEditor.finalizeResizing();SpawEngine.resizingEditor=null;}
if(SpawEngine.movingToolbar!=null)
{SpawEngine.movingToolbar.isMouseMoving=false;SpawEngine.movingToolbar=null;}}}
if(SpawEngine.movingToolbar!=null&&SpawEngine.movingToolbar.isToolbarMoving)
{document.getElementById(SpawEngine.movingToolbar.name+'_toolbox').style.left=SpawEngine.movingToolbar.currentToolbarX+event.clientX-SpawEngine.movingToolbar.lastMousePosX+"px";document.getElementById(SpawEngine.movingToolbar.name+'_toolbox').style.top=SpawEngine.movingToolbar.currentToolbarY+event.clientY-SpawEngine.movingToolbar.lastMousePosY+"px";SpawEngine.movingToolbar.currentToolbarX=document.getElementById(SpawEngine.movingToolbar.name+'_toolbox').offsetLeft;SpawEngine.movingToolbar.currentToolbarY=document.getElementById(SpawEngine.movingToolbar.name+'_toolbox').offsetTop;SpawEngine.movingToolbar.lastMousePosX=event.clientX;SpawEngine.movingToolbar.lastMousePosY=event.clientY;SpawEngine.getActiveEditor().saveFloatingToolbarPosition(SpawEngine.movingToolbar.currentToolbarX,SpawEngine.movingToolbar.currentToolbarY);}
if(SpawEngine.resizingEditor!=null&&SpawEngine.resizingEditor.isResizing)
{if(SpawEngine.resizingEditor.isHorizontalResizingAllowed()&&!event.ctrlKey)
{var encwidth=document.getElementById(SpawEngine.resizingEditor.name+"_enclosure").offsetWidth;var w_delta=event.clientX-SpawEngine.resizingEditor.lastMousePosX;var resobj=SpawEngine.resizingEditor.getPageInput(SpawEngine.resizingEditor.getActivePage().name);if(!SpawEngine.resizingEditor.isInDesignMode())
{resobj.style.width=resobj.offsetWidth+w_delta+"px";}
document.getElementById(SpawEngine.resizingEditor.name+"_enclosure").style.width=encwidth+w_delta+"px";if(!SpawEngine.resizingEditor.isInDesignMode()&&(document.getElementById(SpawEngine.resizingEditor.name+"_enclosure").offsetWidth-w_delta)>(encwidth))
{}
SpawEngine.resizingEditor.lastMousePosX=event.clientX;}
if(SpawEngine.resizingEditor.isVerticalResizingAllowed()&&!event.shiftKey)
{var resobj;if(SpawEngine.resizingEditor.isInDesignMode())
resobj=SpawEngine.resizingEditor.getPageIframeObject(SpawEngine.resizingEditor.getActivePage().name);else
resobj=SpawEngine.resizingEditor.getPageInput(SpawEngine.resizingEditor.getActivePage().name);resobj.style.height=resobj.offsetHeight+event.clientY-SpawEngine.resizingEditor.lastMousePosY+"px";document.getElementById(SpawEngine.resizingEditor.name+'_enclosure').style.height=resobj.style.height;SpawEngine.resizingEditor.lastMousePosY=event.clientY;}}}
SpawEngine.addBrowserEventHandler(document,"mousemove",SpawEngine.mouseMove);SpawEngine.mouseUp=function(event)
{if(SpawEngine.resizingEditor!=null)
{SpawEngine.resizingEditor.isResizing=false;SpawEngine.resizingEditor.finalizeResizing();SpawEngine.resizingEditor=null;}
if(SpawEngine.movingToolbar!=null)
{SpawEngine.movingToolbar.isMouseMoving=false;SpawEngine.movingToolbar=null;}}
SpawEngine.addBrowserEventHandler(document,"mouseup",SpawEngine.mouseUp);SpawEngine.resizingEditor;SpawEngine.movingToolbar;SpawEngine.active_context_menu;SpawEngine.openDialog=function(module,dialog,editor,arguments,querystring,callback,tbi,sender)
{var posX=screen.availWidth/2-275;var posY=screen.availHeight/2-250;var durl=SpawEngine.spaw_dir+'dialogs/dialog.php?module='+module+'&dialog='+dialog
+'&theme='+editor.theme.prefix+'&lang='+editor.getLang()
+'&charset='+editor.getOutputCharset()
+'&scid='+editor.scid+'&sess='+editor.sess
+"&"+querystring+editor.getRequestUriConfigValue();var args=new Object();args.editor=editor;args.arguments=arguments;args.callback=callback;args.tbi=tbi;args.sender=sender;var wnd=window.open(durl,module+'_'+dialog,'status=no,resizable=yes,width=350,height=250,left='+posX+',top='+posY);window.dialogArguments=args;wnd.focus();return wnd;}
SpawEditor.prototype.convertToEntities=function(src_string)
{var result=src_string;var entities={"¡":"&iexcl;","¢":"&cent;","£":"&pound;","¤":"&curren;","¥":"&yen;","¦":"&brvbar;","§":"&sect;","¨":"&uml;","©":"&copy;","ª":"&ordf;","«":"&laquo;","¬":"&not;","­":"&shy;","®":"&reg;","¯":"&macr;","°":"&deg;","±":"&plusmn;","²":"&sup2;","³":"&sup3;","´":"&acute;","µ":"&micro;","¶":"&para;","·":"&middot;","¸":"&cedil;","¹":"&sup1;","º":"&ordm;","»":"&raquo;","¼":"&frac14;","½":"&frac12;","¾":"&frac34;","¿":"&iquest;","À":"&Agrave;","Á":"&Aacute;","Â":"&Acirc;","Ã":"&Atilde;","Ä":"&Auml;","Å":"&Aring;","Æ":"&AElig;","Ç":"&Ccedil;","È":"&Egrave;","É":"&Eacute;","Ê":"&Ecirc;","Ë":"&Euml;","Ì":"&Igrave;","Í":"&Iacute;","Î":"&Icirc;","Ï":"&Iuml;","Ð":"&ETH;","Ñ":"&Ntilde;","Ò":"&Ograve;","Ó":"&Oacute;","Ô":"&Ocirc;","Õ":"&Otilde;","Ö":"&Ouml;","×":"&times;","Ø":"&Oslash;","Ù":"&Ugrave;","Ú":"&Uacute;","Û":"&Ucirc;","Ü":"&Uuml;","Ý":"&Yacute;","Þ":"&THORN;","ß":"&szlig;","à":"&agrave;","á":"&aacute;","â":"&acirc;","ã":"&atilde;","ä":"&auml;","å":"&aring;","æ":"&aelig;","ç":"&ccedil;","è":"&egrave;","é":"&eacute;","ê":"&ecirc;","ë":"&euml;","ì":"&igrave;","í":"&iacute;","î":"&icirc;","ï":"&iuml;","ð":"&eth;","ñ":"&ntilde;","ò":"&ograve;","ó":"&oacute;","ô":"&ocirc;","õ":"&otilde;","ö":"&ouml;","÷":"&divide;","ø":"&oslash;","ù":"&ugrave;","ú":"&uacute;","û":"&ucirc;","ü":"&uuml;","ý":"&yacute;","þ":"&thorn;","ÿ":"&yuml;","ƒ":"&fnof;","Α":"&Alpha;","Β":"&Beta;","γ":"&Gamma;","Δ":"&Delta;","Ε":"&Epsilon;","Ζ":"&Zeta;","Η":"&Eta;","Θ":"&Theta;","Ι":"&Iota;","Κ":"&Kappa;","Λ":"&Lambda;","Μ":"&Mu;","Ν":"&Nu;","Ξ":"&Xi;","Ο":"&Omicron;","Π":"&Pi;","Ρ":"&Rho;","Σ":"&Sigma;","Τ":"&Tau;","Υ":"&Upsilon;","Φ":"&Phi;","Χ":"&Chi;","Ψ":"&Psi;","Ω":"&Omega;","α":"&alpha;","β":"&beta;","γ":"&gamma;","δ":"&delta;","ε":"&epsilon;","ζ":"&zeta;","η":"&eta;","θ":"&theta;","ι":"&iota;","κ":"&kappa;","λ":"&lambda;","μ":"&mu;","ν":"&nu;","ξ":"&xi;","ο":"&omicron;","π":"&pi;","ρ":"&rho;","ς":"&sigmaf;","σ":"&sigma;","τ":"&tau;","υ":"&upsilon;","φ":"&phi;","χ":"&chi;","ψ":"&psi;","ω":"&omega;","•":"&bull;","…":"&hellip;","′":"&prime;","″":"&Prime;","‾":"&oline;","⁄":"&frasl;","℘":"&weierp;","ℑ":"&image;","ℜ":"&real;","™":"&trade;","ℵ":"&alefsym;","←":"&larr;","↑":"&uarr;","→":"&rarr;","↓":"&darr;","↔":"&harr;","↵":"&crarr;","⇐":"&lArr;","⇑":"&uArr;","⇒":"&rArr;","⇔":"&hArr;","∀":"&forall;","∂":"&part;","∃":"&exist;","∅":"&empty;","∇":"&nabla;","∈":"&isin;","∉":"&notin;","∋":"&ni;","∏":"&prod;","∑":"&sum;","−":"&minus;","∗":"&lowast;","√":"&radic;","∝":"&prop;","∞":"&infin;","∧":"&and;","∨":"&or;","∩":"&cap;","∪":"&cup;","∫":"&int;","≅":"&cong;","≈":"&asymp;","≠":"&ne;","≡":"&equiv;","≤":"&le;","≥":"&ge;","⊂":"&sub;","⊃":"&sup;","⊄":"&nsub;","⊆":"&sube;","⊇":"&supe;","⊕":"&oplus;","⊗":"&otimes;","⊥":"&perp;","⋅":"&sdot;","⌈":"&lceil;","⌉":"&rceil;","⌊":"&lfloor;","⌋":"&rfloor;","〈":"&lang;","〉":"&rang;","◊":"&loz;","♠":"&spades;","♣":"&clubs;","♥":"&hearts;","♦":"&diams;","Œ":"&OElig;","œ":"&oelig;","Š":"&Scaron;","š":"&scaron;","Ÿ":"&Yuml;","ˆ":"&circ;","˜":"&tilde;"," ":"&ensp;"," ":"&emsp;"," ":"&thinsp;","‌":"&zwnj;","‍":"&zwj;","‎":"&lrm;","‏":"&rlm;","–":"&ndash;","—":"&mdash;","‘":"&lsquo;","’":"&rsquo;","‚":"&sbquo;","„":"&bdquo;","†":"&dagger;","‡":"&Dagger;","‰":"&permil;","‹":"&lsaquo;","›":"&rsaquo;","€":"&euro;","“":"&ldquo;","”":"&rdquo;"}
var entities_str="¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿƒΑΒγΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩαβγδεζηθικλμνξοπρςστυφχψω•…′″‾⁄℘ℑℜ™ℵ←↑→↓↔↵⇐⇑⇒⇔∀∂∃∅∇∈∉∋∏∑−∗√∝∞∧∨∩∪∫≅≈≠≡≤≥⊂⊃⊄⊆⊇⊕⊗⊥⋅⌈⌉⌊⌋〈〉◊♠♣♥♦ŒœŠšŸˆ˜   ‌‍‎‏–—‘’‚“”„†‡‰‹›€";var rgx=new RegExp("["+entities_str+"]","gm");var matches=result.match(rgx);if(matches!=null)
{var processed=new Array();for(var i=0;i<matches.length;i++)
{if(processed[matches[i]]==null&&entities[matches[i]]!=null&&entities[matches[i]]!=undefined)
{processed[matches[i]]=entities[matches[i]];var replace_rgx=new RegExp(matches[i],"gm");result=result.replace(replace_rgx,entities[matches[i]]);}}}
return result;}
SpawEngine.event_handlers=new Array();SpawEngine.addEventHandler=function(evt_type,handler_fn,evt_target)
{var trg=(evt_target==null)?"page_doc":evt_target.toLowerCase();if(!SpawEngine.event_handlers[trg])
SpawEngine.event_handlers[trg]=new Array();if(SpawEngine.event_handlers[trg][evt_type])
{SpawEngine.event_handlers[trg][evt_type].push(handler_fn);}
else
{if(evt_type.toLowerCase().substring(0,4)!="spaw")
{var ev_obj;if(trg.substring(0,4)!="page"&&trg!="form")
{ev_obj=SpawEngine.getEventTargetObject(trg,null);if(ev_obj.attachEvent)
{ev_obj.attachEvent("on"+evt_type,new Function("event",'SpawEngine.handleEvent("'+evt_type+'", event, "'+trg+'", null);'));}
else
{ev_obj.addEventListener(evt_type,new Function("event",'SpawEngine.handleEvent("'+evt_type+'", event, "'+trg+'", null);'),false);}}
else
{var old_ev_obj;for(var si=0;si<SpawEngine.editors.length;si++)
{if(trg=="form")
{ev_obj=SpawEngine.getEventTargetObject(trg,null,SpawEngine.editors[si]);if(ev_obj!=old_ev_obj)
{if(ev_obj.attachEvent)
{ev_obj.attachEvent("on"+evt_type,new Function("event",'SpawEngine.handleEvent("'+evt_type+'", event, "'+trg+'","'+SpawEngine.editors[si].name+'");'));}
else
{ev_obj.addEventListener(evt_type,new Function("event",'SpawEngine.handleEvent("'+evt_type+'", event, "'+trg+'","'+SpawEngine.editors[si].name+'");'),false);}
old_ev_obj=ev_obj;}}
else
{for(var i=0;i<SpawEngine.editors[si].pages.length;i++)
{ev_obj=SpawEngine.getEventTargetObject(trg,SpawEngine.editors[si].pages[i],SpawEngine.editors[si]);if(ev_obj.attachEvent)
{ev_obj.attachEvent("on"+evt_type,new Function("event",'SpawEngine.handleEvent("'+evt_type+'", event, "'+trg+'","'+SpawEngine.editors[si].name+'");'));}
else
{ev_obj.addEventListener(evt_type,new Function("event",'SpawEngine.handleEvent("'+evt_type+'", event, "'+trg+'","'+SpawEngine.editors[si].name+'");'),false);}}}}}}
SpawEngine.event_handlers[trg][evt_type]=new Array();SpawEngine.event_handlers[trg][evt_type].push(handler_fn);}}
SpawEngine.handleEvent=function(evt_type,evt,evt_target,editor_name)
{var trg=(evt_target==null)?"page_doc":evt_target.toLowerCase();var ed=editor_name?SpawEngine.getEditor(editor_name):SpawEngine.getActiveEditor();if(SpawEngine.event_handlers[trg]&&SpawEngine.event_handlers[trg][evt_type])
{for(var i=0;i<SpawEngine.event_handlers[trg][evt_type].length;i++)
{eval(SpawEngine.event_handlers[trg][evt_type][i]+'(ed, evt)');}}}
SpawEngine.getEventTargetObject=function(evt_target,page,editor)
{var trg=(evt_target==null)?"page_doc":evt_target.toLowerCase();var ev_obj;switch(trg)
{case"page_iframe":ev_obj=editor.getPageIframeObject(page.name);break;case"page_doc":ev_obj=editor.getPageDoc(page.name);break;case"page_body":ev_obj=editor.getPageDoc(page.name).body;break;case"form":ev_obj=editor.getPageInput(editor.getActivePage().name).form;break;case"window":ev_obj=window;break;case"document":ev_obj=document;break;default:ev_obj=editor.getActivePageDoc();break;}
return ev_obj;}
function SpawTab(page)
{this.page=page;}
SpawTab.prototype.page;SpawTab.prototype.template;SpawTab.prototype.active_template;SpawTab.prototype.setInactive=function()
{var tab=document.getElementById(this.page.name+'_tab');if(tab)
{tab.innerHTML=this.template;}}
SpawTab.prototype.setActive=function()
{var tab=document.getElementById(this.page.name+'_tab');if(tab)
{tab.innerHTML=this.active_template;}}
function SpawTbItem(module,name,id)
{this.module=module;this.name=name;this.id=id;this.is_enabled=true;}
SpawTbItem.prototype.module;SpawTbItem.prototype.name;SpawTbItem.prototype.id;SpawTbItem.prototype.editor;SpawTbItem.prototype.toolbar_name;SpawTbItem.prototype.is_enabled;function SpawTbImage(module,name,id)
{this.constructor(module,name,id);}
SpawTbImage.prototype=new SpawTbItem;function SpawTbButton(module,name,id,on_enabled_check,on_pushed_check,on_click,base_image_url,show_in_context_menu)
{this.constructor(module,name,id);this.on_enabled_check=on_enabled_check;this.on_pushed_check=on_pushed_check;this.on_click=on_click;this.base_image_url=base_image_url;if(show_in_context_menu)
this.show_in_context_menu=show_in_context_menu;else
this.show_in_context_menu=false;}
SpawTbButton.prototype=new SpawTbItem;SpawTbButton.prototype.on_enabled_check;SpawTbButton.prototype.on_pushed_check;SpawTbButton.prototype.on_click;SpawTbButton.prototype.is_pushed=false;SpawTbButton.prototype.show_in_context_menu=false;SpawTbButton.prototype.base_image_url;SpawTbButton.prototype.image;SpawTbButton.prototype.image_over;SpawTbButton.prototype.image_down;SpawTbButton.prototype.image_off;function SpawTbDropdown(module,name,id,on_enabled_check,on_status_check,on_change)
{this.constructor(module,name,id);this.on_enabled_check=on_enabled_check;this.on_status_check=on_status_check;this.on_change=on_change;}
SpawTbDropdown.prototype=new SpawTbItem;SpawTbDropdown.prototype.on_enabled_check;SpawTbDropdown.prototype.on_status_check;SpawTbDropdown.prototype.on_change;
function SpawUtils()
{}
SpawUtils.ltrim=function(txt)
{var spacers=" \t\r\n ";while(txt.length>0&&spacers.indexOf(txt.charAt(0))!=-1)
{txt=txt.substr(1);}
return(txt);}
SpawUtils.rtrim=function(txt)
{var spacers=" \t\r\n ";while(txt.length>0&&spacers.indexOf(txt.charAt(txt.length-1))!=-1)
{txt=txt.substr(0,txt.length-1);}
return(txt);}
SpawUtils.trim=function(txt)
{return txt.replace(/^\s+|\s+$/g,'');}
SpawUtils.trimLineBreaks=function(txt){return txt.replace(/\s+/g,' ');}
SpawUtils.htmlEncode=function(txt)
{return txt.replace('&','&amp;').replace('<','&lt;').replace('>','&gt;').replace(/\u00A0/g,"&nbsp;");}
SpawUtils.getPageOffsetLeft=function(obj)
{var elm=obj;x=obj.offsetLeft;while((elm=elm.offsetParent)!=null)
{x+=elm.offsetLeft;}
return x;}
SpawUtils.getPageOffsetTop=function(obj)
{var elm=obj;y=obj.offsetTop;while((elm=elm.offsetParent)!=null)
{y+=elm.offsetTop;}
return y;}
SpawEditor.prototype.initialize=function()
{this.document=document;if(!this.document)
{setTimeout(this.name+'_obj.initialize();',50);return;}
for(var i=0;i<this.pages.length;i++)
{if(i>0)
{this.hidePage(this.pages[i-1]);this.showPage(this.pages[i]);}
if(!this.pages[i].initialized)
{var pta=this.getPageInput(this.pages[i].name);var pdoc=this.getPageDoc(this.pages[i].name);try
{if(pdoc.designMode!='on'&&eval(this.name+'_obj.enabled'))
{pdoc.designMode='on';pdoc.designMode='off';pdoc.designMode='on';}}
catch(e)
{setTimeout(this.name+'_obj.initialize();',50);return;}
var c_set=pdoc.createElement("meta");c_set.setAttribute("http-equiv","Content-Type");c_set.setAttribute("content","text/html; charset=utf-8");var s_sheet=pdoc.createElement("link");s_sheet.setAttribute("rel","stylesheet");s_sheet.setAttribute("type","text/css");if(window.location.href.toLowerCase().indexOf("https")==0&&this.stylesheet.indexOf("/")==0)
{this.stylesheet=window.location.href.substring(0,window.location.href.indexOf("/",9))+this.stylesheet;}
s_sheet.setAttribute("href",this.stylesheet);var head=pdoc.getElementsByTagName("head");if(!head||head.length==0)
{head=pdoc.createElement("head");pdoc.childNodes[0].insertBefore(head,pdoc.body);}
else
{head=head[0];}
head.appendChild(c_set);head.appendChild(s_sheet);pdoc.body.dir=this.pages[i].direction;this.pages[i].initialized=true;SpawEngine.setActiveEditor(this);if(pta.value!=null&&pta.value!="\n"&&pta.value!='')
this.updatePageDoc(this.pages[i]);}
if(this.pages.length>1)
this.hidePage(this.pages[i]);else if(window.location.href.toLowerCase().indexOf("https")==0)
{this.pages[0].editing_mode="html";this.showPage(this.pages[0]);this.pages[0].editing_mode="design";this.showPage(this.pages[0]);}}
if(this.pages.length>1)
this.showPage(this.pages[0]);SpawEngine.handleEvent("spawinit",null,null,this.name);if(SpawEngine.isInitialized())
{SpawEngine.addEventHandler("contextmenu",'SpawEditor.rightClick');SpawEngine.addEventHandler("keypress",'SpawEditor.hideContextMenu');SpawEngine.addEventHandler("click",'SpawEditor.hideContextMenu');SpawEngine.addEventHandler("click",'SpawEditor.hideContextMenu',"document");SpawEngine.addEventHandler("keyup",'SpawEditor.checkContext');SpawEngine.addEventHandler("mouseup",'SpawEditor.checkContext');SpawEngine.addEventHandler("submit",'SpawEngine.onSubmit',"form");SpawEngine.handleEvent("spawallinit",null,null,null);}
var frm=this.getPageInput(this.pages[0].name).form;if(!frm.formSubmit)
{frm.formSubmit=frm.submit;frm.submit=new Function(this.name+'_obj.spawSubmit();');}
setTimeout(this.name+'_obj.updateToolbar();',10);}
SpawEditor.prototype.getPageIframe=function(page_name)
{return this.getPageIframeObject(page_name);}
SpawEditor.prototype.getPageDoc=function(page_name)
{if(this.getPageIframe(page_name))
return this.getPageIframe(page_name).contentDocument;}
SpawEditor.prototype.insertNodeAtSelection=function(newNode)
{var pif=this.getPageIframe(this.getActivePage().name);var pdoc=this.getPageDoc(this.getActivePage().name);var sel=pif.contentWindow.getSelection();var rng=sel.getRangeAt(0);rng.deleteContents();var container=rng.startContainer;var startpos=rng.startOffset;rng=pdoc.createRange();switch(container.nodeType)
{case 3:var txt=container.nodeValue;var afterTxt=txt.substring(startpos);container.nodeValue=txt.substring(0,startpos);if(container.nextSibling==null)
{container.parentNode.appendChild(newNode);container.parentNode.appendChild(pdoc.createTextNode(afterTxt));}
else
{var afterNode=pdoc.createTextNode(afterTxt);container.parentNode.insertBefore(afterNode,container.nextSibling);container.parentNode.insertBefore(newNode,afterNode);}
rng.setStart(container.parentNode.childNodes[1],0);rng.setEnd(container.parentNode.childNodes[2],0);break;default:container.insertBefore(newNode,container.childNodes[startpos]);rng.setEnd(container.childNodes[startpos],0);rng.setStart(container.childNodes[startpos],0);break;}
sel.removeAllRanges();this.addGlyphs(pdoc.body);}
SpawEditor.prototype.getNodeAtSelection=function()
{var pif=this.getPageIframe(this.getActivePage().name);var sel=pif.contentWindow.getSelection();if(sel&&sel.rangeCount>0)
{var rng=sel.getRangeAt(0);return rng.cloneContents();}
else
{return null;}}
SpawEditor.prototype.getSelectionParent=function()
{var result;var pif=this.getPageIframe(this.getActivePage().name);var pdoc=this.getActivePageDoc();var sel=pif.contentWindow.getSelection();if(sel&&sel.rangeCount>0)
{var rng=sel.getRangeAt(0);var container=rng.commonAncestorContainer;result=container;if(container.nodeType==3)
{result=container.parentNode;}
else if(rng.startContainer.nodeType==1&&rng.startContainer==rng.endContainer&&(rng.endOffset-rng.startOffset)<=1)
{result=rng.startContainer.childNodes[rng.startOffset];}}
else
{result=pdoc.body;}
return result;}
SpawEditor.prototype.addGlyphs=function(root)
{if(this.show_glyphs)
{if(root.nodeType==1)
{if(root.tagName.toLowerCase()=='table'&&(!root.border||root.border=="0"||root.border=="")&&(!root.style.borderWidth||root.style.borderWidth=="0"||root.style.borderWidth=="")&&(!root.getAttribute("__spawglyphed")))
{root.style.border="1px dashed #aaaaaa";root.setAttribute("__spawglyphed",true);var cls=root.getElementsByTagName("td");for(var i=0;i<cls.length;i++)
{cls[i].style.border="1px dashed #aaaaaa";cls[i].setAttribute("__spawglyphed",true);}
cls=root.getElementsByTagName("th");for(var i=0;i<cls.length;i++)
{cls[i].style.border="1px dashed #aaaaaa";cls[i].setAttribute("__spawglyphed",true);}}}
if(root.hasChildNodes())
{for(var i=0;i<root.childNodes.length;i++)
this.addGlyphs(root.childNodes[i]);}}}
SpawEditor.prototype.removeGlyphs=function(root)
{if(root.nodeType==1&&root.getAttribute("__spawglyphed"))
{root.style.border="";root.removeAttribute("__spawglyphed");}
if(root.hasChildNodes())
{for(var i=0;i<root.childNodes.length;i++)
this.removeGlyphs(root.childNodes[i]);}}
SpawEditor.prototype.selectionWalk=function(func)
{var pif=this.getPageIframe(this.getActivePage().name);var sel=pif.contentWindow.getSelection();if(sel&&sel.rangeCount>0)
{var rng=sel.getRangeAt(0);var ancestor=rng.commonAncestorContainer;this.selectionNodeWalk(ancestor,rng,func);}}
SpawEditor.prototype.selectionNodeWalk=function(node,rng,func)
{if(rng.isPointInRange(node,0)||rng.startContainer==node||rng.endContainer==node)
{func(node,(rng.startContainer==node)?rng.startOffset:null,(rng.endContainer==node)?rng.endOffset:null);}
if(node.childNodes&&node.childNodes.length>0)
{for(var i=0;i<node.childNodes.length;i++)
{var cnode=node.childNodes[i];this.selectionNodeWalk(cnode,rng,func);}}}
SpawEditor.prototype.insertHtmlAtSelection=function(source)
{var pdoc=this.getPageDoc(this.getActivePage().name);var elm=pdoc.createElement("span");var frag=pdoc.createDocumentFragment();elm.innerHTML=source;while(elm.hasChildNodes())
frag.appendChild(elm.childNodes[0]);this.insertNodeAtSelection(frag);}
SpawEditor.prototype.applyStyleToSelection=function(cssClass,styleName,styleValue)
{var sel=this.getNodeAtSelection();var pnode=this.getSelectionParent();if(sel)
{if(sel.nodeType==1)
{if(cssClass!='')
sel.className=cssClass;if(styleName!='')
sel.style[styleName]=styleValue;this.insertNodeAtSelection(sel);}
else
{var pdoc=this.getActivePageDoc();var spn=pdoc.createElement("SPAN");if(cssClass!='')
spn.className=cssClass;if(styleName!='')
spn.style[styleName]=styleValue;spn.appendChild(sel);if(spn.innerHTML.length>0)
{if(spn.innerHTML!=pnode.innerHTML||pnode.tagName.toLowerCase()=="body")
this.insertNodeAtSelection(spn);else
{if(cssClass!='')
pnode.className=cssClass;if(styleName!='')
pnode.style[styleName]=styleValue;}}
else
{if(pnode.tagName.toLowerCase()!="body")
{if(cssClass!='')
pnode.className=cssClass;if(styleName!='')
pnode.style[styleName]=styleValue;}
else
{spn.innerHTML=pnode.innerHTML;pnode.innerHTML='';pnode.appendChild(spn);}}}}}
SpawEditor.prototype.removeStyleFromSelection=function(cssClass,styleName)
{this.focus();var pnode=this.getSelectionParent();if(cssClass)
{while(pnode&&pnode.tagName.toLowerCase()!="body"&&(!pnode.className||pnode.className==""))
{pnode=pnode.parentNode;}
if(pnode&&pnode.tagName.toLowerCase()!="body")
{pnode.removeAttribute("class");pnode.removeAttribute("className");}}
if(styleName)
{while(pnode&&pnode.tagName.toLowerCase()!="body"&&!pnode.style[styleName])
{pnode=pnode.parentNode;}
if(pnode&&pnode.tagName.toLowerCase()!="body")
{pnode.style[styleName]='';}}}
function SpawPGattach()
{}
SpawPGattach.attachClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{SpawEngine.openDialog('spawfm','spawfm',editor,'','type=images','SpawPGattach.attachClickCallback',tbi,sender);}}
SpawPGattach.attachClickCallback=function(editor,result,tbi,sender)
{if(result)
{var newa=result;var fNm=/([^\/]+)$/.exec(newa);var pdoc=editor.getActivePageDoc();var aProps=pdoc.createElement("A");aProps.href=newa;aProps.innerHTML='['+fNm[1]+']';editor.insertNodeAtSelection(aProps);}
editor.updateToolbar();editor.focus();}
SpawPGattach.isAttachEnabled=function(editor,tbi)
{return editor.isInDesignMode();}
function SpawPGchangecase()
{}
SpawPGchangecase.toUpperClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{editor.selectionWalk(SpawPGchangecase.toUpperCase);}}
SpawPGchangecase.toLowerClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{editor.selectionWalk(SpawPGchangecase.toLowerCase);}}
SpawPGchangecase.changeCase=function(mode,node,start_offset,end_offset)
{if(node.nodeType==3)
{if(start_offset==null&&end_offset==null)
{node.nodeValue=(mode=="upper")?node.nodeValue.toUpperCase():node.nodeValue.toLowerCase();}
else
{var val=node.nodeValue;if(start_offset==null)
start_offset=0;if(end_offset==null)
end_offset=val.length
var start_str=val.substr(0,start_offset);var end_str=val.substr(end_offset,val.length-end_offset);var sel_str=val.substr(start_offset,end_offset-start_offset);node.nodeValue=start_str+((mode=="upper")?sel_str.toUpperCase():sel_str.toLowerCase())+end_str;}}}
SpawPGchangecase.toUpperCase=function(node,start_offset,end_offset)
{SpawPGchangecase.changeCase("upper",node,start_offset,end_offset);}
SpawPGchangecase.toLowerCase=function(node,start_offset,end_offset)
{SpawPGchangecase.changeCase("lower",node,start_offset,end_offset);}
SpawPGchangecase.isChangeCaseEnabled=function(editor,tbi)
{return editor.isInDesignMode();}
function SpawPGcore()
{}
SpawPGcore.standardFunctionClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var pdoc=editor.getPageDoc(editor.active_page.name);try
{pdoc.execCommand(tbi.name,false,null);}
catch(e)
{}
editor.updateToolbar();editor.focus();}}
SpawPGcore.standardFunctionChange=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var pdoc=editor.getPageDoc(editor.active_page.name);var val=sender.options[sender.selectedIndex].value;try
{pdoc.execCommand(tbi.name,false,val);}
catch(e)
{}
sender.selectedIndex=0;editor.updateToolbar();editor.focus();}}
SpawPGcore.isStandardFunctionEnabled=function(editor,tbi)
{if(editor.getActivePage().editing_mode=="design")
{if(SpawPGcore.isStandardFunctionPushed(editor,tbi))
return true;else
return editor.getPageDoc(editor.getActivePage().name).queryCommandEnabled(tbi.name);}
else
{return false;}}
SpawPGcore.isStandardFunctionPushed=function(editor,tbi)
{if(editor.getActivePage().editing_mode=="design")
{try
{return editor.getPageDoc(editor.getActivePage().name).queryCommandState(tbi.name);}
catch(e)
{return false;}}
else
{return false;}}
SpawPGcore.standardFunctionStatusCheck=function(editor,tbi)
{var pdoc=editor.getActivePageDoc();try
{return pdoc.queryCommandValue(tbi.name);}
catch(e)
{return'';}}
SpawPGcore.foreColorClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var cl=editor.getPageDoc(editor.getActivePage().name).queryCommandValue("forecolor");if(cl==null)
cl='#000000';SpawEngine.openDialog('core','colorpicker',editor,SpawColor.parseRGB(cl),'','SpawPGcore.foreColorClickCallback',tbi,sender);}}
SpawPGcore.foreColorClickCallback=function(editor,result,tbi,sender)
{var pdoc=editor.getPageDoc(editor.active_page.name);try
{pdoc.execCommand('forecolor',false,result);}
catch(e)
{}}
SpawPGcore.isForeColorEnabled=function(editor,tbi)
{if(editor.isInDesignMode())
{try
{return editor.getActivePageDoc().queryCommandEnabled("forecolor");}
catch(e)
{return false;}}
else
{return false;}}
SpawPGcore.styleChange=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var cls=sender.options[sender.selectedIndex].value;if(cls!='')
{editor.applyStyleToSelection(cls,'','');}
else
{editor.removeStyleFromSelection(true,'');}
sender.selectedIndex=0;editor.updateToolbar();editor.focus();}}
SpawPGcore.isStyleEnabled=function(editor,tbi)
{return editor.isInDesignMode();}
SpawPGcore.styleStatusCheck=function(editor,tbi)
{var pnode=editor.getSelectionParent();while(pnode&&pnode.tagName&&pnode.tagName.toLowerCase()!="body"&&(!pnode.className||pnode.className==""))
{pnode=pnode.parentNode;}
if(pnode&&pnode.tagName&&pnode.tagName.toLowerCase()!="body")
{return pnode.className;}
else
{return null;}}
SpawPGcore.fontFamilyChange=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var fontName=sender.options[sender.selectedIndex].value;if(fontName!='')
{editor.applyStyleToSelection('','fontFamily',fontName);}
else
{editor.removeStyleFromSelection('','fontFamily');}
sender.selectedIndex=0;editor.updateToolbar();editor.focus();}}
SpawPGcore.fontSizeChange=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var fontSize=sender.options[sender.selectedIndex].value;if(fontSize!='')
{switch(fontSize)
{case"1":fontSize="xx-small";break;case"2":fontSize="x-small";break;case"3":fontSize="small";break;case"4":fontSize="medium";break;case"5":fontSize="large";break;case"6":fontSize="x-large";break;case"7":fontSize="xx-large";break;default:break;}
editor.applyStyleToSelection('','fontSize',fontSize);}
else
{editor.removeStyleFromSelection('','fontSize');}
sender.selectedIndex=0;editor.updateToolbar();editor.focus();}}
SpawPGcore.isInDesignMode=function(editor,tbi)
{return editor.isInDesignMode();}
SpawPGcore.isInHtmlMode=function(editor,tbi)
{return!editor.isInDesignMode();}
SpawPGcore.hyperlinkClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var a=editor.getSelectedElementByTagName("a");editor.stripAbsoluteUrl(a);SpawEngine.openDialog('core','hyperlink',editor,a,'','SpawPGcore.hyperlinkClickCallback',tbi,sender);}}
SpawPGcore.hyperlinkClickCallback=function(editor,result,tbi,sender)
{if(result)
{var newa=result;var pdoc=editor.getActivePageDoc();var a=editor.getSelectedElementByTagName("a");if(!a)
{var sel=editor.getNodeAtSelection();if(sel.nodeType==1&&sel.tagName.toLowerCase()=='span')
{newa.innerHTML=sel.innerHTML;}
else
{newa.appendChild(sel);}
if(SpawUtils.trim(newa.innerHTML)==''&&SpawUtils.trim(newa.href)!=''&&newa.href!=pdoc.location.href)
{if(newa.title)
newa.innerHTML=newa.title;else
newa.innerHTML=editor.getStrippedAbsoluteUrl(newa.href,false);}
if(newa.href==pdoc.location.href)
newa.removeAttribute("href");editor.insertNodeAtSelection(newa);}}
editor.updateToolbar();editor.focus();}
SpawPGcore.isHyperlinkEnabled=function(editor,tbi)
{if(editor.isInDesignMode())
{return editor.getActivePageDoc().queryCommandEnabled("createlink");}
else
{return false;}}
SpawPGcore.imageClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{SpawEngine.openDialog('spawfm','spawfm',editor,'','type=images','SpawPGcore.imageClickCallback',null,null);}}
SpawPGcore.imageClickCallback=function(editor,result,tbi,sender)
{if(result)
{var img=document.createElement("IMG");img.src=result;img.border=0;img.alt="";editor.insertNodeAtSelection(img);}
editor.updateToolbar();editor.focus();}
SpawPGcore.imagePropClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var i=editor.getSelectedElementByTagName("img");if(i)
{editor.stripAbsoluteUrl(i);SpawEngine.openDialog('core','image_prop',editor,i,'','',tbi,sender);}
else
{SpawEngine.openDialog('core','image_prop',editor,i,'','SpawPGcore.imagePropClickCallback',tbi,sender);}}}
SpawPGcore.imagePropClickCallback=function(editor,result,tbi,sender)
{if(result)
{editor.insertNodeAtSelection(result);}
editor.updateToolbar();editor.focus();}
SpawPGcore.flashPropClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var i=editor.getSelectedElementByTagName("img");if(i)
{editor.stripAbsoluteUrl(i);SpawEngine.openDialog('core','flash_prop',editor,i,'','',tbi,sender);}
else
{SpawEngine.openDialog('core','flash_prop',editor,i,'','SpawPGcore.flashPropClickCallback',tbi,sender);}}}
SpawPGcore.flashPropClickCallback=function(editor,result,tbi,sender)
{if(result)
{editor.insertNodeAtSelection(result);}
editor.updateToolbar();editor.focus();}
SpawPGcore.insertHorizontalRuleClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var pdoc=editor.getActivePageDoc();var hr=pdoc.createElement("HR");editor.insertNodeAtSelection(hr);}}
SpawPGcore.isDesignModeEnabled=function(editor,tbi)
{return(tbi.editor.getActivePage().editing_mode!="design");}
SpawPGcore.isHtmlModeEnabled=function(editor,tbi)
{return(tbi.editor.getActivePage().editing_mode!="html");}
SpawPGcore.designModeClick=function(editor,tbi,sender)
{var ap=tbi.editor.getActivePage();if(ap.editing_mode!="design")
{SpawEngine.handleEvent("spawbeforemodeswitch",null,"page_doc",tbi.editor.name);editor.updatePageDoc(ap)
tbi.editor.enableEditingMode(ap.editing_mode_tbi);ap.editing_mode="design";ap.editing_mode_tbi=tbi;tbi.editor.showPage(ap);SpawEngine.handleEvent("spawmodeswitch",null,"page_doc",tbi.editor.name);setTimeout(tbi.editor.name+'_obj.updateToolbar();',10);tbi.editor.disableEditingMode(ap.editing_mode_tbi);editor.addGlyphs(editor.getActivePageDoc().body);}}
SpawPGcore.htmlModeClick=function(editor,tbi,sender)
{var ap=tbi.editor.getActivePage();if(ap.editing_mode!="html")
{SpawEngine.handleEvent("spawbeforemodeswitch",null,"page_doc",tbi.editor.name);editor.updatePageInput(ap);tbi.editor.enableEditingMode(ap.editing_mode_tbi);ap.editing_mode="html";ap.editing_mode_tbi=tbi;tbi.editor.showPage(ap);SpawEngine.handleEvent("spawmodeswitch",null,"page_doc",tbi.editor.name);setTimeout(tbi.editor.name+'_obj.updateToolbar();',10);tbi.editor.disableEditingMode(ap.editing_mode_tbi);}}
SpawPGcore.tableCreateClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{SpawEngine.openDialog('core','table_prop',editor,null,'','SpawPGcore.tableCreateClickCallback',tbi,sender);}}
SpawPGcore.tableCreateClickCallback=function(editor,result,tbi,sender)
{if(result)
{editor.insertNodeAtSelection(result);}
editor.updateToolbar();editor.focus();}
SpawPGcore.tablePropClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var tbl=editor.getSelectedElementByTagName("table");SpawEngine.openDialog('core','table_prop',editor,tbl,'','SpawPGcore.tablePropClickCallback',tbi,sender);}}
SpawPGcore.tablePropClickCallback=function(editor,result,tbi,sender)
{var tbl=editor.getSelectedElementByTagName("table");if(!tbl&&result)
{editor.insertNodeAtSelection(result);}
editor.updateToolbar();editor.focus();}
SpawPGcore.isTablePropertiesEnabled=function(editor,tbi)
{if(editor.isInDesignMode())
{var tbl=editor.getSelectedElementByTagName("table");return(tbl)?true:false;}
else
{return false;}}
SpawPGcore.tableCellPropClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var tc=editor.getSelectedElementByTagName("td");if(tc==null)
tc=editor.getSelectedElementByTagName("th");SpawEngine.openDialog('core','table_cell_prop',editor,tc,'','',tbi,sender);}}
SpawPGcore.isTableCellPropertiesEnabled=function(editor,tbi)
{if(editor.isInDesignMode())
{var tbl=editor.getSelectedElementByTagName("td");if(!tbl)
tbl=editor.getSelectedElementByTagName("th");return(tbl)?true:false;}
else
{return false;}}
SpawPGcore.tableCellMatrix=function(tbl)
{var tm=new Array();var rows;if(tbl.rows&&tbl.rows.length>0)
{rows=tbl.rows;}
else
{rows=tbl.getElementsByTagName("TR");}
for(var i=0;i<rows.length;i++)
tm[i]=new Array();for(var i=0;i<rows.length;i++)
{jr=0;for(var j=0;j<rows[i].cells.length;j++)
{while(tm[i][jr]!=undefined)
jr++;for(var jh=jr;jh<jr+(rows[i].cells[j].colSpan?rows[i].cells[j].colSpan:1);jh++)
{for(var jv=i;jv<i+(rows[i].cells[j].rowSpan?rows[i].cells[j].rowSpan:1);jv++)
{if(jv==i)
{tm[jv][jh]=rows[i].cells[j].cellIndex;}
else
{tm[jv][jh]=-1;}}}}}
return(tm);}
SpawPGcore.insertTableRowClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var tbl=editor.getSelectedElementByTagName("table");var cr=editor.getSelectedElementByTagName("tr");if(tbl&&cr)
{var newr=tbl.insertRow(cr.rowIndex+1);for(var i=0;i<cr.cells.length;i++)
{if(cr.cells[i].rowSpan>1)
{cr.cells[i].rowSpan++;}
else
{var newc=cr.cells[i].cloneNode(false);newc.innerHTML="&nbsp;";newr.appendChild(newc);}}
for(var i=0;i<cr.rowIndex;i++)
{var tempr;if(tbl.rows&&tbl.rows.length>0)
{tempr=tbl.rows[i];}
else
{tempr=tbl.getElementsByTagName("tr")[i];}
for(var j=0;j<tempr.cells.length;j++)
{if(tempr.cells[j].rowSpan>(cr.rowIndex-i))
tempr.cells[j].rowSpan++;}}}
editor.updateToolbar();editor.focus();}}
SpawPGcore.insertTableColumnClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var ct=editor.getSelectedElementByTagName("table");var cr=editor.getSelectedElementByTagName("tr");var cd=editor.getSelectedElementByTagName("td");if(!cd)
cd=editor.getSelectedElementByTagName("th");if(cd&&cr&&ct)
{var tm=SpawPGcore.tableCellMatrix(ct);var rows;if(ct.rows&&ct.rows.length>0)
{rows=ct.rows;}
else
{rows=ct.getElementsByTagName("TR");}
var rowIndex;if(cr.rowIndex>=0)
{rowIndex=cr.rowIndex;}
else
{for(var ri=0;ri<rows.length;ri++)
{if(rows[ri]==cr)
{rowIndex=ri;break;}}}
var realIndex;for(var j=0;j<tm[rowIndex].length;j++)
{if(tm[rowIndex][j]==cd.cellIndex)
{realIndex=j;break;}}
for(var i=0;i<rows.length;i++)
{if(tm[i][realIndex]!=-1)
{if(rows[i].cells[tm[i][realIndex]].colSpan>1)
{rows[i].cells[tm[i][realIndex]].colSpan++;}
else
{var newc=rows[i].insertCell(tm[i][realIndex]+1);var nc=rows[i].cells[tm[i][realIndex]].cloneNode(false);nc.innerHTML="&nbsp;";rows[i].replaceChild(nc,newc);}}}}
editor.updateToolbar();editor.focus();}}
SpawPGcore.mergeTableCellRightClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var ct=editor.getSelectedElementByTagName("table");var cr=editor.getSelectedElementByTagName("tr");var cd=editor.getSelectedElementByTagName("td");if(!cd)
cd=editor.getSelectedElementByTagName("th");if(cd&&cr&&ct)
{var tm=SpawPGcore.tableCellMatrix(ct);var rows;if(ct.rows&&ct.rows.length>0)
{rows=ct.rows;}
else
{rows=ct.getElementsByTagName("TR");}
var rowIndex;if(cr.rowIndex>=0)
{rowIndex=cr.rowIndex;}
else
{for(var ri=0;ri<rows.length;ri++)
{if(rows[ri]==cr)
{rowIndex=ri;break;}}}
var realIndex;for(var j=0;j<tm[rowIndex].length;j++)
{if(tm[rowIndex][j]==cd.cellIndex)
{realIndex=j;break;}}
if(cd.cellIndex+1<cr.cells.length)
{var ccrs=cd.rowSpan?cd.rowSpan:1;var cccs=cd.colSpan?cd.colSpan:1;var ncrs=cr.cells[cd.cellIndex+1].rowSpan?cr.cells[cd.cellIndex+1].rowSpan:1;var nccs=cr.cells[cd.cellIndex+1].colSpan?cr.cells[cd.cellIndex+1].colSpan:1;var j=realIndex;while(tm[rowIndex][j]==cd.cellIndex)j++;if(tm[rowIndex][j]==cd.cellIndex+1)
{if(ccrs==ncrs)
{cd.colSpan=cccs+nccs;cd.innerHTML+=cr.cells[cd.cellIndex+1].innerHTML;cr.deleteCell(cd.cellIndex+1);}}}}
editor.updateToolbar();editor.focus();}}
SpawPGcore.mergeTableCellDownClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var ct=editor.getSelectedElementByTagName("table");var cr=editor.getSelectedElementByTagName("tr");var cd=editor.getSelectedElementByTagName("td");if(!cd)
cd=editor.getSelectedElementByTagName("th");if(cd&&cr&&ct)
{var tm=SpawPGcore.tableCellMatrix(ct);var rows;if(ct.rows&&ct.rows.length>0)
{rows=ct.rows;}
else
{rows=ct.getElementsByTagName("TR");}
var rowIndex;if(cr.rowIndex>=0)
{rowIndex=cr.rowIndex;}
else
{for(var ri=0;ri<rows.length;ri++)
{if(rows[ri]==cr)
{rowIndex=ri;break;}}}
var crealIndex;for(var j=0;j<tm[rowIndex].length;j++)
{if(tm[rowIndex][j]==cd.cellIndex)
{crealIndex=j;break;}}
var ccrs=cd.rowSpan?cd.rowSpan:1;var cccs=cd.colSpan?cd.colSpan:1;if(rowIndex+ccrs<rows.length)
{var ncellIndex=tm[rowIndex+ccrs][crealIndex];if(ncellIndex!=-1&&(crealIndex==0||(crealIndex>0&&(tm[rowIndex+ccrs][crealIndex-1]!=tm[rowIndex+ccrs][crealIndex]))))
{var ncrs=rows[rowIndex+ccrs].cells[ncellIndex].rowSpan?rows[rowIndex+ccrs].cells[ncellIndex].rowSpan:1;var nccs=rows[rowIndex+ccrs].cells[ncellIndex].colSpan?rows[rowIndex+ccrs].cells[ncellIndex].colSpan:1;if(cccs==nccs)
{cd.innerHTML+=rows[rowIndex+ccrs].cells[ncellIndex].innerHTML;rows[rowIndex+ccrs].deleteCell(ncellIndex);cd.rowSpan=ccrs+ncrs;}}}}
editor.updateToolbar();editor.focus();}}
SpawPGcore.deleteTableRowClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var ct=editor.getSelectedElementByTagName("table");var cr=editor.getSelectedElementByTagName("tr");var cd=editor.getSelectedElementByTagName("td");if(!cd)
cd=editor.getSelectedElementByTagName("th");if(cd&&cr&&ct)
{var tm=SpawPGcore.tableCellMatrix(ct);var rows;if(ct.rows&&ct.rows.length>0)
{rows=ct.rows;}
else
{rows=ct.getElementsByTagName("TR");}
var rowIndex;if(cr.rowIndex>=0)
{rowIndex=cr.rowIndex;}
else
{for(var ri=0;ri<rows.length;ri++)
{if(rows[ri]==cr)
{rowIndex=ri;break;}}}
if(rows.length<=1)
{ct.parentNode.removeChild(ct);}
else
{for(var i=0;i<rowIndex;i++)
{var tempr=rows[i];for(var j=0;j<tempr.cells.length;j++)
{if(tempr.cells[j].rowSpan>(rowIndex-i))
tempr.cells[j].rowSpan--;}}
var curCI=-1;for(var i=0;i<tm[rowIndex].length;i++)
{var prevCI=curCI;curCI=tm[rowIndex][i];if(curCI!=-1&&curCI!=prevCI&&cr.cells[curCI].rowSpan>1&&(rowIndex+1)<rows.length)
{var ni=i;var nrCI=tm[rowIndex+1][ni];while(nrCI==-1)
{ni++;if(ni<rows[rowIndex+1].cells.length)
nrCI=tm[rowIndex+1][ni];else
nrCI=rows[rowIndex+1].cells.length;}
var newc=rows[rowIndex+1].insertCell(nrCI);rows[rowIndex].cells[curCI].rowSpan--;var nc=rows[rowIndex].cells[curCI].cloneNode(false);rows[rowIndex+1].replaceChild(nc,newc);var cs=(cr.cells[curCI].colSpan>1)?cr.cells[curCI].colSpan:1;var nj;for(var j=i;j<(i+cs);j++)
{tm[rowIndex+1][j]=nrCI;nj=j;}
for(var j=nj;j<tm[rowIndex+1].length;j++)
{if(tm[rowIndex+1][j]!=-1)
tm[rowIndex+1][j]++;}}}
if(ct.rows&&ct.rows.length>0)
{ct.deleteRow(rowIndex);}
else
{ct.removeChild(rows[rowIndex]);}}}
editor.updateToolbar();editor.focus();}}
SpawPGcore.deleteTableColumnClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var ct=editor.getSelectedElementByTagName("table");var cr=editor.getSelectedElementByTagName("tr");var cd=editor.getSelectedElementByTagName("td");if(!cd)
cd=editor.getSelectedElementByTagName("th");if(cd&&cr&&ct)
{var tm=SpawPGcore.tableCellMatrix(ct);var rows;if(ct.rows&&ct.rows.length>0)
{rows=ct.rows;}
else
{rows=ct.getElementsByTagName("TR");}
var rowIndex;if(cr.rowIndex>=0)
{rowIndex=cr.rowIndex;}
else
{for(var ri=0;ri<rows.length;ri++)
{if(rows[ri]==cr)
{rowIndex=ri;break;}}}
var realIndex;if(tm[0].length<=1)
{ct.parentNode.removeChild(ct);}
else
{for(var j=0;j<tm[rowIndex].length;j++)
{if(tm[rowIndex][j]==cd.cellIndex)
{realIndex=j;break;}}
for(var i=0;i<rows.length;i++)
{if(tm[i][realIndex]!=-1)
{if(rows[i].cells[tm[i][realIndex]].colSpan>1)
rows[i].cells[tm[i][realIndex]].colSpan--;else
rows[i].deleteCell(tm[i][realIndex]);}}}}
editor.updateToolbar();editor.focus();}}
SpawPGcore.splitTableCellVerticallyClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var ct=editor.getSelectedElementByTagName("table");var cr=editor.getSelectedElementByTagName("tr");var cd=editor.getSelectedElementByTagName("td");if(!cd)
cd=editor.getSelectedElementByTagName("th");if(cd&&cr&&ct)
{var tm=SpawPGcore.tableCellMatrix(ct);var rows;if(ct.rows&&ct.rows.length>0)
{rows=ct.rows;}
else
{rows=ct.getElementsByTagName("TR");}
var rowIndex;if(cr.rowIndex>=0)
{rowIndex=cr.rowIndex;}
else
{for(var ri=0;ri<rows.length;ri++)
{if(rows[ri]==cr)
{rowIndex=ri;break;}}}
var realIndex;for(var j=0;j<tm[rowIndex].length;j++)
{if(tm[rowIndex][j]==cd.cellIndex)
{realIndex=j;break;}}
if(cd.colSpan>1)
{var newc=rows[rowIndex].insertCell(cd.cellIndex+1);cd.colSpan--;var nc=cd.cloneNode(false);nc.innerHTML="&nbsp;";rows[rowIndex].replaceChild(nc,newc);cd.colSpan=1;}
else
{var newc=rows[rowIndex].insertCell(cd.cellIndex+1);var nc=cd.cloneNode(false);nc.innerHTML="&nbsp;";rows[rowIndex].replaceChild(nc,newc);var cs;for(var i=0;i<tm.length;i++)
{if(i!=rowIndex&&tm[i][realIndex]!=-1)
{cs=rows[i].cells[tm[i][realIndex]].colSpan>1?rows[i].cells[tm[i][realIndex]].colSpan:1;rows[i].cells[tm[i][realIndex]].colSpan=cs+1;}}}}
editor.updateToolbar();editor.focus();}}
SpawPGcore.splitTableCellHorizontallyClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var ct=editor.getSelectedElementByTagName("table");var cr=editor.getSelectedElementByTagName("tr");var cd=editor.getSelectedElementByTagName("td");if(!cd)
cd=editor.getSelectedElementByTagName("th");if(cd&&cr&&ct)
{var tm=SpawPGcore.tableCellMatrix(ct);var rows;if(ct.rows&&ct.rows.length>0)
{rows=ct.rows;}
else
{rows=ct.getElementsByTagName("TR");}
var rowIndex;if(cr.rowIndex>=0)
{rowIndex=cr.rowIndex;}
else
{for(var ri=0;ri<rows.length;ri++)
{if(rows[ri]==cr)
{rowIndex=ri;break;}}}
var realIndex;for(var j=0;j<tm[rowIndex].length;j++)
{if(tm[rowIndex][j]==cd.cellIndex)
{realIndex=j;break;}}
if(cd.rowSpan>1)
{var i=realIndex;var ni;while(tm[rowIndex+1][i]==-1)i++;if(i==tm[rowIndex+1].length)
ni=rows[rowIndex+1].cells.length;else
ni=tm[rowIndex+1][i];var newc=rows[rowIndex+1].insertCell(ni);cd.rowSpan--;var nc=cd.cloneNode(false);nc.innerHTML="&nbsp;";rows[rowIndex+1].replaceChild(nc,newc);cd.rowSpan=1;}
else
{if(ct.rows&&ct.rows.length>0)
{ct.insertRow(rowIndex+1);}
else
{var pdoc=editor.getActivePageDoc();if(rowIndex<(rows.length-1))
{ct.insertBefore(pdoc.createElement("TR"),rows[rowIndex+1]);}
else
{ct.appendChild(pdoc.createElement("TR"));}}
var rs;for(var i=0;i<cr.cells.length;i++)
{if(i!=cd.cellIndex)
{rs=cr.cells[i].rowSpan>1?cr.cells[i].rowSpan:1;cr.cells[i].rowSpan=rs+1;}}
for(var i=0;i<rowIndex;i++)
{var tempr=rows[i];for(var j=0;j<tempr.cells.length;j++)
{if(tempr.cells[j].rowSpan>(rowIndex-i))
tempr.cells[j].rowSpan++;}}
var newc=rows[rowIndex+1].insertCell(0);var nc=cd.cloneNode(false);nc.innerHTML="&nbsp;";rows[rowIndex+1].replaceChild(nc,newc);}}
editor.updateToolbar();editor.focus();}}
SpawPGcore.toggleBordersClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var pdoc=editor.getActivePageDoc();editor.show_glyphs=!editor.show_glyphs;if(editor.show_glyphs)
{editor.addGlyphs(pdoc.body);}
else
{editor.removeGlyphs(pdoc.body);}
editor.updateToolbar();editor.focus();}}
SpawPGcore.toggleBordersPushed=function(editor,tbi)
{if(editor.isInDesignMode())
{return editor.show_glyphs;}
else
{return false;}}
SpawPGcore.codeCleanupClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{editor.cleanPageCode(null);editor.updateToolbar();editor.focus();}}
SpawPGcore.bgColorClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var cl=editor.getPageDoc(editor.getActivePage().name).queryCommandValue("hilitecolor");if(cl==null)
cl='#ffffff';SpawEngine.openDialog('core','colorpicker',editor,SpawColor.parseRGB(cl),'','SpawPGcore.bgColorClickCallback',tbi,sender);}}
SpawPGcore.bgColorClickCallback=function(editor,result,tbi,sender)
{var pdoc=editor.getPageDoc(editor.active_page.name);try
{pdoc.execCommand('hilitecolor',false,result);editor.focus();}
catch(e)
{}}
SpawPGcore.isBgColorEnabled=function(editor,tbi)
{if(editor.isInDesignMode())
{try
{return editor.getActivePageDoc().queryCommandEnabled("hilitecolor");}
catch(e)
{return false;}}
else
{return false;}}
function SpawThemespaw2()
{}
SpawThemespaw2.prefix="spaw2";SpawThemespaw2.preloadImages=function(tbi)
{tbi.image=new Image();tbi.image.src=tbi.base_image_url;tbi.image_over=new Image();tbi.image_over.src=tbi.base_image_url.substring(0,tbi.base_image_url.length-4)+'_over.gif';tbi.image_down=new Image();tbi.image_down.src=tbi.base_image_url.substring(0,tbi.base_image_url.length-4)+'_down.gif';tbi.image_off=new Image();tbi.image_off.src=tbi.base_image_url.substring(0,tbi.base_image_url.length-4)+'_off.gif';}
SpawThemespaw2.buttonOver=function(tbi,sender)
{if(!sender.disabled&&sender.disabled!="true"&&!tbi.is_pushed)
{sender.src=tbi.image_over.src;}
tbi.editor.getTargetEditor().showStatus(sender.title);}
SpawThemespaw2.buttonOut=function(tbi,sender)
{if(!sender.disabled&&sender.disabled!="true")
{if(!tbi.is_pushed)
sender.src=tbi.image.src;else
sender.src=tbi.image_down.src;tbi.editor.getTargetEditor().showStatus('');}}
SpawThemespaw2.buttonDown=function(tbi,sender)
{if(!sender.disabled&&sender.disabled!="true")
sender.src=tbi.image_down.src;}
SpawThemespaw2.buttonUp=function(tbi,sender)
{if(!sender.disabled&&sender.disabled!="true")
sender.src=tbi.image.src;}
SpawThemespaw2.buttonOff=function(tbi,sender)
{sender.src=tbi.image_off.src;}
SpawThemespaw2.dropdownOver=function(tbi,sender)
{}
SpawThemespaw2.dropdownOut=function(tbi,sender)
{}
SpawThemespaw2.dropdownDown=function(tbi,sender)
{}
SpawThemespaw2.dropdownUp=function(tbi,sender)
{}
SpawThemespaw2.dropdownOff=function(tbi,sender)
{}
SpawThemespaw2.getBaseButtonImageName=function(tbi,sender)
{var imgsrc=sender.src;if(imgsrc.lastIndexOf(tbi.name)>-1)
{imgsrc=imgsrc.substring(0,imgsrc.lastIndexOf(tbi.name)+tbi.name.length);}
else
{imgsrc=imgsrc.substring(0,imgsrc.lastIndexOf("_plugin")+"_plugin".length);}
return imgsrc;}
function SpawPGcustombutton()
{}
SpawPGcustombutton.myButtonClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var pdoc=editor.getActivePageDoc();var cur_node=editor.getNodeAtSelection();var tmp_node=pdoc.createElement("SPAN");tmp_node.appendChild(cur_node);var template=editor.getConfigValue('PG_CUSTOMBUTTON_TEMPLATE');tmp_node.innerHTML=template.replace('%%CURRENT_CONTENT%%',tmp_node.innerHTML);editor.insertNodeAtSelection(tmp_node);}}
SpawPGcustombutton.isMyButtonEnabled=function(editor,tbi)
{return editor.isInDesignMode();}
function SpawPGcustomdropdown(){}
SpawPGcustomdropdown.change=function(editor,tbi,sender)
{if(tbi.is_enabled){var cls=sender.options[sender.selectedIndex].value;if(!sender.selectedIndex||!cls)
return;var cls2=editor.getConfigValue(cls);cls=cls2?cls2:cls;sender.selectedIndex=0;if(editor.isInDesignMode()){editor.insertHtmlAtSelection(cls);editor.updateToolbar();editor.focus();}else{var pta=editor.getPageInput(editor.getActivePage().name);var ss=pta.selectionStart;pta.value=pta.value.substring(0,ss)+cls+pta.value.substring(pta.selectionEnd);pta.setSelectionRange(ss,ss+cls.length);pta.focus();}
return null;}}
SpawPGcustomdropdown.isEnabled=function(editor,tbi){return tbi.is_enabled;}
SpawPGcustomdropdown.statusCheck=function(editor,tbi){return null;}
function SpawPGimgpopup()
{}
SpawPGimgpopup.imagePopupClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{SpawEngine.openDialog('spawfm','spawfm',editor,'','type=images','SpawPGimgpopup.imagePopupClickCallback',null,null);}}
SpawPGimgpopup.imagePopupClickCallback=function(editor,result,tbi,sender)
{if(result)
{var pdoc=editor.getActivePageDoc();var a=editor.getSelectedElementByTagName("a");if(!a)
{a=pdoc.createElement("A");a.href="#";a.setAttribute('onclick',"window.open('"+editor.getConfigValue("PG_IMGPOPUP_DIALOG")+editor.getConfigValue("PG_IMGPOPUP_PARAMETER")+"="+result+"','Image','width=500,height=300,scrollbars=no,toolbar=no,location=no,status=no,resizable=yes,screenX=120,screenY=100');return false;");var sel=editor.getNodeAtSelection();if(sel.nodeType==1&&sel.outerHTML)
{a.innerHTML=sel.outerHTML;}
else
{a.appendChild(sel);}
if(SpawUtils.trim(a.innerHTML)=='')
{a.innerHTML=result;}
editor.insertNodeAtSelection(a);}
else
{a.href="#";a.setAttribute('onclick',"window.open("+editor.getConfigValue("PG_IMGPOPUP_DIALOG")+editor.getConfigValue("PG_IMGPOPUP_PARAMETER")+"="+result+"','Image','width=500,height=300,scrollbars=no,toolbar=no,location=no,status=no,resizable=yes,screenX=120,screenY=100');return false;");}}}
SpawPGimgpopup.isImagePopupEnabled=function(editor,tbi)
{return editor.isInDesignMode();}
function SpawPGinserthtml()
{}
SpawPGinserthtml.insertHtmlClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{SpawEngine.openDialog('inserthtml','inserthtml',editor,'','','SpawPGinserthtml.insertHtmlClickCallback',tbi,sender);}}
SpawPGinserthtml.insertHtmlClickCallback=function(editor,result,tbi,sender)
{if(result)
{editor.insertHtmlAtSelection(result);}
editor.updateToolbar();}
SpawPGinserthtml.isInsertHtmlEnabled=function(editor,tbi)
{return editor.isInDesignMode();}
function SpawPGyahooMaps()
{}
SpawPGyahooMaps.yahooMapsPropClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var i=editor.getSelectedElementByTagName("img");if(i)
{editor.stripAbsoluteUrl(i);SpawEngine.openDialog('yahooMaps','yahooMaps_prop',editor,i,'','',tbi,sender);}
else
{SpawEngine.openDialog('yahooMaps','yahooMaps_prop',editor,i,'','SpawPGyahooMaps.yahooMapsPropClickCallback',tbi,sender);}}}
SpawPGyahooMaps.yahooMapsPropClickCallback=function(editor,result,tbi,sender)
{if(result)
{editor.insertNodeAtSelection(result);}
editor.updateToolbar();}
SpawPGyahooMaps.isyahooMapsPropEnabled=function(editor,tbi)
{return editor.isInDesignMode();}
function SpawPGyoutube()
{}
SpawPGyoutube.youTubePropClick=function(editor,tbi,sender)
{if(tbi.is_enabled)
{var i=editor.getSelectedElementByTagName("img");if(i)
{editor.stripAbsoluteUrl(i);SpawEngine.openDialog('youtube','youtube_prop',editor,i,'','',tbi,sender);}
else
{SpawEngine.openDialog('youtube','youtube_prop',editor,i,'','SpawPGyoutube.youTubePropClickCallback',tbi,sender);}}}
SpawPGyoutube.youTubePropClickCallback=function(editor,result,tbi,sender)
{if(result)
{editor.insertNodeAtSelection(result);}
editor.updateToolbar();}
SpawPGyoutube.isYouTubePropEnabled=function(editor,tbi)
{return editor.isInDesignMode();}