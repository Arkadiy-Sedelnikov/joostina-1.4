$(document).ready(function() {
	var _js_defines = _js_defines || [];
	if((jQuery.inArray("load_tooltip", _js_defines)>-1)){
		$("a.edit_button").tooltip({ 
			track: true,
			delay: 0,
			showURL: false,
			showBody: " - ",
			extraClass: "pretty",
			fixPNG: true,
			opacity: 0.95
		});
	}
});