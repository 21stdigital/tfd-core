<?php
/**
 * Plugin Name:       21st digital core functionality
 * Plugin URI:        https://21st.digital
 * Description:       WordPress helper functions
 * Version:           1.0.0
 * Author:            21st digital GmbH
 * Author URI:        https://21st.digital
 * Text Domain:       hyrox
 * Domain Path:       /languages.
 */
if (!defined('WPINC')) {
    die;
}

require_once dirname(__FILE__).'/vendor/extended-cpts/extended-cpts.php';

require_once dirname(__FILE__).'/debug/_main.php';

require_once dirname(__FILE__).'/main/_main.php';
