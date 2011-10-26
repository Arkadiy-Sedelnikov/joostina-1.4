/**
* @package Joostina
* @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или LICENSE.php
* Joostina! - свободное программное обеспечение распостраняемое по условиям лицензиии GNU/GPL
* Для просмотра подробностей и замечаний об авторском праве, смотрите файл COPYRIGHT.php.
* PrettyTable - скрипт чудесных таблиц
* скрипт выделения строк таблицы
* базируется на оригинальной статье "Row Locking with CSS and JavaScript": http://www.askthecssguy.com/2006/12/row_locking_with_css_and_javas.html
* доработка для CMS Joostina
* @boston. 06.03.2008, 00:47.
**/
function addLoadEvent(func) {
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			oldonload();
			func();
		}
	}
}
function addClass(element,value) {
	if (!element.className) {
		element.className = value;
	} else {
		newClassName = element.className;
		newClassName+= " ";
		newClassName+= value;
		element.className = newClassName;
	}
}
function removeClassName(oElm, strClassName){
	var oClassToRemove = new RegExp((strClassName + "\s?"), "i");
	oElm.className = oElm.className.replace(oClassToRemove, "").replace(/^\s?|\s?$/g, "");
}
function selectRowRadio(row) {
	var radio = row.getElementsByTagName("input")[0];

	if(radio.checked==true)
		radio.checked = false
	else{
		radio.checked = true
		document.adminForm.boxchecked.value = 1;
	}
}
function removeSelectedStateFromOtherRows() {
	if (noremclass = document.getElementById("prettytablenoremclass"))
		if(noremclass.value==1) return;
	var tables = document.getElementsByTagName("table");
	for (var m=0; m<tables.length; m++) {
		if (tables[m].className == "adminlist") {
			var tbodies = tables[m].getElementsByTagName("tbody");
			for (var j=0; j<tbodies.length; j++) {
				var rows = tbodies[j].getElementsByTagName("tr");
				for (var i=0; i<rows.length; i++) {
					if (rows[i].className.indexOf("selected") != -1) {
						removeClassName(rows[i], "selected");
					}
				}
			}
		}
	}
}
function lockRow() {
	var tables = document.getElementsByTagName("table");
	for (var m=0; m<tables.length; m++) {
		if (tables[m].className == "adminlist") {
			var tbodies = tables[m].getElementsByTagName("tbody");
			for (var j=0; j<tbodies.length; j++) {
				var rows = tbodies[j].getElementsByTagName("tr");
				for (var i=0; i<rows.length; i++) {
					rows[i].oldClassName = rows[i].className;
					rows[i].onclick = function() {
						if (this.className.indexOf("selected") != -1) {
							this.className = this.oldClassName;
						} else {
							removeSelectedStateFromOtherRows();
							addClass(this,"selected");
						}
						selectRowRadio(this);
					}
					var checkbox = rows[i].getElementsByTagName("input")[0]
					if (checkbox) checkbox.onclick = function(event){
						isChecked(this.checked);
						if (!event) event = window.event;
						event.cancelBubble = true;
						if (event.stopPropagation) event.stopPropagation();
					}
				}
			}
		}
	}
}
addLoadEvent(lockRow);
