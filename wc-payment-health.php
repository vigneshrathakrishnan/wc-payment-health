<?php
/**
 * Plugin Name: WC Payment Health
 * Description: Passive health monitoring for WooCommerce payment gateways.
 * Version: 1.0.3
 * Author: Vignesh R
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/vendor/autoload.php';

WCPH\Bootstrap::init();
