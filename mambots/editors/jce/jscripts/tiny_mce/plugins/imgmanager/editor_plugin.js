tinyMCE.importPluginLanguagePack('imgmanager');

var TinyMCE_ImageManagerPlugin = {
	getInfo : function() {
		return {
			longname : 'JCE Image Manager',
			author : 'Ryan Demmer',
			authorurl : 'http://www.cellardoor.za.net',
			infourl : 'http://www.cellardoor.za.net/index2.php?option=com_content&amp;task=findkey&amp;pop=1&amp;lang=en&amp;keyref=imgmanager.about',
			version : '1.1.3'
		};
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case "image":
				return tinyMCE.getButtonHTML(cn, 'lang_imgmanager_desc', '{$pluginurl}/images/imgmanager.gif', 'mceImageManager');
		}

		return "";
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		switch (command) {
			case "mceImageManager":
				var template = new Array();

				template['file']   = tinyMCE.getParam('site') + '/index2.php?option=com_jce&no_html=1&task=plugin&plugin=imgmanager&file=manager.php';
				template['width']  = 750;
				template['height'] = 650;
				
				template['width']  += tinyMCE.getLang('lang_imgmanager_delta_width', 0);
				template['height'] += tinyMCE.getLang('lang_imgmanager_delta_height', 0);

				var inst = tinyMCE.getInstanceById(editor_id);
				var elm = inst.getFocusElement();

				if (elm != null && tinyMCE.getAttrib(elm, 'class').indexOf('mceItem') != -1)
					return true;

				tinyMCE.openWindow(template, {editor_id : editor_id, inline : "yes"});

				return true;
		}

		return false;
	},

	cleanup : function(type, content) {
		switch (type) {
			case "insert_to_editor_dom":
				var imgs = content.getElementsByTagName("img");
				for (var i=0; i<imgs.length; i++) {
					var onmouseover = tinyMCE.cleanupEventStr(tinyMCE.getAttrib(imgs[i], 'onmouseover'));
					var onmouseout = tinyMCE.cleanupEventStr(tinyMCE.getAttrib(imgs[i], 'onmouseout'));

					if ((src = this._getImageSrc(onmouseover)) != "") {
						if (tinyMCE.getParam('convert_urls'))
							src = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], src);

						imgs[i].setAttribute('onmouseover', "this.src='" + src + "';");
					}

					if ((src = this._getImageSrc(onmouseout)) != "") {
						if (tinyMCE.getParam('convert_urls'))
							src = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], src);

						imgs[i].setAttribute('onmouseout', "this.src='" + src + "';");
					}
				}
				break;

			case "get_from_editor_dom":
				var imgs = content.getElementsByTagName("img");
				for (var i=0; i<imgs.length; i++) {
					var onmouseover = tinyMCE.cleanupEventStr(tinyMCE.getAttrib(imgs[i], 'onmouseover'));
					var onmouseout = tinyMCE.cleanupEventStr(tinyMCE.getAttrib(imgs[i], 'onmouseout'));

					if ((src = this._getImageSrc(onmouseover)) != "") {
						if (tinyMCE.getParam('convert_urls'))
							src = eval(tinyMCE.settings['urlconverter_callback'] + "(src, null, true);");

						imgs[i].setAttribute('onmouseover', "this.src='" + src + "';");
					}

					if ((src = this._getImageSrc(onmouseout)) != "") {
						if (tinyMCE.getParam('convert_urls'))
							src = eval(tinyMCE.settings['urlconverter_callback'] + "(src, null, true);");

						imgs[i].setAttribute('onmouseout', "this.src='" + src + "';");
					}
				}
				break;
		}

		return content;
	},

	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
		if (node == null)
			return;

		do {
			if (node.nodeName == "IMG" && tinyMCE.getAttrib(node, 'class').indexOf('mceItem') == -1) {
				tinyMCE.switchClass(editor_id + '_imgmanager', 'mceButtonSelected');
				return true;
			}
		} while ((node = node.parentNode));

		tinyMCE.switchClass(editor_id + '_imgmanager', 'mceButtonNormal');

		return true;
	},

	/**
	 * Returns the image src from a scripted mouse over image str.
	 *
	 * @param {string} s String to get real src from.
	 * @return Image src from a scripted mouse over image str.
	 * @type string
	 */
	_getImageSrc : function(s) {
		var sr, p = -1;

		if (!s)
			return "";

		if ((p = s.indexOf('this.src=')) != -1) {
			sr = s.substring(p + 10);
			sr = sr.substring(0, sr.indexOf('\''));

			return sr;
		}

		return "";
	}
};

tinyMCE.addPlugin("imgmanager", TinyMCE_ImageManagerPlugin);
