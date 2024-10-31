<?php 

// Make sure this is a legitimate uninstall request
if( ! defined( 'ABSPATH') or ! defined('WP_UNINSTALL_PLUGIN') or ! current_user_can( 'delete_plugins' ) )
        exit();
	
	delete_option('pm_options');

?>