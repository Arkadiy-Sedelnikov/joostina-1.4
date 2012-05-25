Yahoo Maps Plugin for SPAW Editor PHP Edition v.2
--------------------------------------------------
In conjuction with the code from the "JavaScript" section of 
this document, this plugin allows users to show a Yahoo! map 
on their webpage.  The map parameters are specified in a popup 
dialog.


Installation
------------
Copy "yahooMaps" directory into "plugins" subdir of your SPAW v.2 
installation.

You will need to install the Yahoo! User Interface (YUI!), or just 
modify the following code to not use it.  I think you need the dom, 
event, and yahoo.js files.  Located at http://developer.yahoo.com/yui/
or can direct link from Yahoo!:

	<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/yahoo/yahoo-min.js"></script> 
	<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/dom/dom-min.js"></script> 
	<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/event/event-min.js"></script> 

You also need to include the Yahoo! maps API in your HTML document:
	
	<script type="text/javascript" src="http://maps.yahooapis.com/v3.5/fl/javascript/apiloader.js?appid=YahooDemo"></script>

The developer's page can be found here: 
http://developer.yahoo.com/maps/flash/jsGettingStarted.html

Insert the following javascript code in the head section of your HTML 
document:

	var yahooMaps = {	
		address : null, 
		map : null,
		about: null,
		init : function(container, address, zoom, about) {			
			yahooMaps.about=about;
			if (yahooMaps.about=='') yahooMaps.about='Here is where we are located.';
			yahooMaps.address = address;
			yahooMaps.map = new Map(container, "YahooDemo", address, zoom, about);
			yahooMaps.map.addEventListener(Map.EVENT_INITIALIZE, yahooMaps.onMapInit);
			yahooMaps.map.addEventListener(Map.EVENT_MARKER_GEOCODE_SUCCESS, yahooMaps.onMarkerGeocode);
		},
		onMapInit : function(eventObj) {
		    yahooMaps.map.addTool( new PanTool(), true );		    
		    var marker = new CustomPOIMarker(yahooMaps.address, '', yahooMaps.about, '0xFF0000', '0xFFFFFF');
		    yahooMaps.map.addMarkerByAddress( marker, yahooMaps.address);		    
		    yahooMaps.map.addWidget( new ZoomBarWidget() );
		},
		onMarkerGeocode : function(eventObj) { var geocodeResponse = eventObj.response; },
		updateMapDisplay : function() {
			var mapElement = YAHOO.util.Dom.get('mapContainer');
			if (mapElement) { yahooMaps.init('mapContainer', mapElement.getAttribute('address'), mapElement.getAttribute('zoom'), mapElement.getAttribute('about')); }
		}
	}

	YAHOO.util.Event.addListener(window, "load", yahooMaps.updateMapDisplay);



Configuration
-------------
I think it's just copy and paste.  Basically, when the page loads, any element with the ID of mapContainer will be converted 
to a Yahoo! map based on the HTML attributes of "address" and "description".  This requires Flash.

Copyright
---------
This plugin is (c)2007 by Michael Dowling of WeGoAll LLC
It is released under terms of GNU General Public License (see license.txt) in
"docs" subdirectory

Commercial SPAW license owners can use this plugin free of charge under the terms
of their respective license.