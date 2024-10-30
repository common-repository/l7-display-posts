<?php
/**
 * Plugin Name: L7 Display Posts
 * Plugin URI:  http://layer7web.com/plugins
 * Description: Display posts according to tag or category.
 * Version:     0.1.1
 * Author:      Jeffrey S. Mattson
 * Author URI:  https://github.com/jeffreysmattson
 * License:     GPLv2+
 * Text Domain: ptp
 * Domain Path: /languages
 */

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using yo wp-make:plugin
 * Copyright (c) 2015 10up, LLC
 * https://github.com/10up/generator-wp-make
 */

// Useful global constants
define( 'PTP_VERSION', '0.1.0' );
define( 'PTP_URL',     plugin_dir_url( __FILE__ ) );
define( 'PTP_PATH',    dirname( __FILE__ ) . '/' );
define( 'PTP_INC',     PTP_PATH . 'includes/' );
define( 'PTP_DIR',	   plugin_dir_path( __FILE__ ) );

// Include files
require_once PTP_INC . 'php/shortcode.php';
require_once PTP_INC . 'functions/core.php';
require_once PTP_INC . 'functions/functions.php';

// Activation/Deactivation
register_activation_hook( __FILE__, '\L7w\Primary_Tag_Plugin\Core\activate' );

// Bootstrap
L7w\Primary_Tag_Plugin\Core\setup();