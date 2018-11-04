<?php

use Illuminate\Database\Capsule\Manager as DB;

require_once dirname(__FILE__) . '/include/purge_unpaid_orders_addon.php';

function paid_orders_change_status($params) {
    $invoice_id = $params['invoiceid'];
    //$invoice = DB::table('tblinvoices')->find($invoice_id);
    $addon = new purge_unpaid_orders_addon();
    if (!empty($addon->moduleSettings['paid_status'])) {
        DB::table('tblorders')->where('invoiceid', $invoice_id)->update(array('status' => $addon->moduleSettings['paid_status']));
    }
}

function purge_unpaid_orders() {
    $addon = new purge_unpaid_orders_addon();
    if (!empty($addon->moduleSettings['cancel_unpaid'])) {
        $addon->purgeUnpaidOrders();
    }
}

function mark_orders_without_invoice($params) {
    if (!empty($params['orderid'])) {
        $changeStatus = false;
        if (!empty($params['invoiceid'])) {
            $invoice = DB::table('tblinvoices')->find($params['invoiceid']);
            if($invoice->status == 'Paid') {
                $changeStatus = true;
            }
        } else {
            $changeStatus = true;
        }
        if ($changeStatus) {
            $addon = new purge_unpaid_orders_addon();
            if (!empty($addon->moduleSettings['paid_status'])) {
                DB::table('tblorders')->where('id', $params['orderid'])->update(array('status' => $addon->moduleSettings['paid_status']));
            }
        }
    }
}

add_hook('ClientAreaPageUpgrade', 1, 'mark_orders_without_invoice');
add_hook('InvoicePaid', 1, 'paid_orders_change_status');
add_hook('DailyCronJob', 1, 'purge_unpaid_orders');