Custom Button Plugin for SPAW Editor PHP Edition v.2
--------------------------------------------------
This plugin is actually a plugin template rather than ready to use plugin. 
It allows you to specify (in config file) some text or html to be inserted in 
front and after the current selection.

Installation
------------
Just copy "custombutton" directory into "plugins" subdir of your SPAW v.2 
installation

Configuration
-------------
Specify code template that should be inserted in place of current selection in
config/config.php file. Template should use %%CURRENT_CODE%% string to specify 
where currently selected code should be inserted.

Since this is not a ready to use plugin you might want to create a special 
button for it in place of included empty button and modify the text in language
file.

Toolbar button is added to "plugins" toolbar so it should be enabled for button
to be displayed (it is enabled by default)

Copyright
---------
This plugin is (c)2007 by UAB Solmetra.
It is released under terms of GNU General Public License (see license.txt) in
"docs" subdirectory

Commercial SPAW license owners can use this plugin free of charge under the terms
of their respective license.
