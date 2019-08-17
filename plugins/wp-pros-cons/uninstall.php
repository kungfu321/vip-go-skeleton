<?php

class wpc_uninstall {
  static public function plugin_uninstall() {
		delete_option('joomdev_wpc_options');
		delete_site_option('joomdev_wpc_options');
	}
}