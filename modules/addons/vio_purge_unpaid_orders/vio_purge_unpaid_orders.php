<?php
/**
 * VIO Purge Unpaid Orders
 *
 * Addon module for automatically purging orders when left unpaid.
 *
 * @package    WHMCS
 * @author     Venture I/O <code@ventureio.com>
 * @copyright  Copyright (c) Venture I/O 2015
 * @link       http://ventureio.com
 */

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

define("puoVersion", "1.0.4");

use Illuminate\Database\Capsule\Manager as DB;

function vio_purge_unpaid_orders_config() {
    return array(
        "name" => "VIO Purge Unpaid Orders",
        "description" => "Addon module for automatically purging orders when left unpaid.",
        "version" => puoVersion,
        "author" => "Venture I/O",
        "language" => "english",
        "fields" => array()
    );
}

function vio_purge_unpaid_orders_output($vars) {
    require_once dirname(__FILE__) . '/include/purge_unpaid_orders_addon.php';
    $action = empty($_REQUEST['action']) ? 'index' : $_REQUEST['action'];
    $module = new purge_unpaid_orders_addon($vars);
    if (!method_exists($module, $action . 'Action')) {
        throw new Exception('Module hasn\'t method ' . $action . 'Action');
    }
    if (!empty($vars['_lang'])) {
        $module->setLanguage($vars['_lang']);
    }
    $module->setModuleLink($vars['modulelink'])->{$action . 'Action'}();
}
