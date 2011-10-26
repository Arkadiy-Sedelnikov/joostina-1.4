/**
 * $RCSfile: editor_plugin_src.js,v $
 * $Revision: 1.24 $
 * $Date: 2006/02/10 16:29:38 $
 *
 * @author Moxiecode
 * @copyright Copyright Т– 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

//Modified and enhanced for JCE 1.1 - Ryan Demmer

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('advlink');

var TinyMCE_AdvancedLinkPlugin = {
	getInfo : function() {
		return {
			longname : 'Advanced link',
			author : 'Moxiecode Systems / Ryan Demmer',
			authorurl : 'http://www.cellardoor.za.net',
			infourl : 'http://www.cellardoor.za.net/index2.php?option=com_content&amp;task=findkey&amp;pop=1&amp;lang=en&amp;keyref=advlink.about',
			version : '1.1.3'
		};
	},

	initInstance : function(inst) {
		inst.addShortcut('ctrl', 'k', 'lang_advlink_desc', 'mceAdvLink');
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case "link":
				return tinyMCE.getButtonHTML(cn, 'lang_advlink_desc', '{$pluginurl}/images/advlink.gif', 'mceAdvLink');
		}

		return "";
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		switch (command) {
			case "mceAdvLink":
				var anySelection = false;
				var inst = tinyMCE.getInstanceById(editor_id);
				var focusElm = inst.getFocusElement();
				var selectedText = inst.selection.getSelectedText();

				if (tinyMCE.selectedElement)
					anySelection = (tinyMCE.selectedElement.nodeName.toLowerCase() == "img") || (selectedText && selectedText.length > 0);

				if (anySelection || (focusElm != null && focusElm.nodeName == "A")) {
					var template = new Array();

					template['file']   =  tinyMCE.getParam('site')+'/index2.php?option=com_jce&no_html=1&task=plugin&plugin=advlink&file=link.php';
					template['width']  = 600;
					template['height'] = 650;

					// Language specific width and height addons
					template['width']  += tinyMCE.getLang('lang_advlink_delta_width', 0);
					template['height'] += tinyMCE.getLang('lang_advlink_delta_height', 0);

					tinyMCE.openWindow(template, {editor_id : editor_id, inline : "yes"});
				}

				return true;
		}

		return false;
	},

	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
		if (node == null)
			return;

		do {
			if (node.nodeName == "A" && tinyMCE.getAttrib(node, 'href') != "") {
				tinyMCE.switchClass(editor_id + '_advlink', 'mceButtonSelected');
				return true;
			}
		} while ((node = node.parentNode));

		if (any_selection) {
			tinyMCE.switchClass(editor_id + '_advlink', 'mceButtonNormal');
			return true;
		}

		tinyMCE.switchClass(editor_id + '_advlink', 'mceButtonDisabled');

		return true;
	}
};

tinyMCE.addPlugin("advlink", TinyMCE_AdvancedLinkPlugin);
