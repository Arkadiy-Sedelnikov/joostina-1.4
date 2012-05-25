<?php
// Image popup config
// Url to be used as a link for popup
SpawConfig::setStaticConfigItem("PG_IMGPOPUP_DIALOG", SpawConfig::getStaticConfigValue('SPAW_DIR').'plugins/imgpopup/img_popup.php?', SPAW_CFG_TRANSFER_JS);
// Query string parameter name
SpawConfig::setStaticConfigItem("PG_IMGPOPUP_PARAMETER", 'img_url', SPAW_CFG_TRANSFER_JS);
?>
