<?php
if (! defined('ABSPATH') && ! defined('WP_UNINSTALL_PLUGIN')) { // Exit if accessed directly
    exit;
}

delete_option('gncpl_options');
