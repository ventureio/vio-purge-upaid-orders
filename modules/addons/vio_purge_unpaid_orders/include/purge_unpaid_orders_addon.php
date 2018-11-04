<?php

use Illuminate\Database\Capsule\Manager as DB;

if (!class_exists('base_addon')) {
    require_once __DIR__ . '/base_addon.php';
}

class purge_unpaid_orders_addon extends base_addon {
    
    public $moduleSettings;

    public function __construct($vars = null) {
        $this->getModuleConfig();
        $this->modulePath = dirname(__DIR__);
        if (!empty($vars['_lang'])) {
            $this->setLanguage($vars['_lang']);
        }
    }

    protected function getModuleConfig() {
        if (!empty($this->moduleSettings)) {
            return $this->moduleSettings;
        }
        $result = array();
        $rows = DB::table('tbladdonmodules')
                ->where('module', 'vio_purge_unpaid_orders')
                ->get();
        foreach ($rows as $row) {

            $result[$row->setting] = $row->value;
        }
        $this->moduleSettings = $result;
        return $this->moduleSettings;
    }

    protected function updateConfig($data) {
        DB::table('tbladdonmodules')
                ->where('module', 'vio_purge_unpaid_orders')
                ->whereNotIn('setting', array('version', 'access'))
                ->delete();
        foreach ($data as $k => $v) {
            DB::table('tbladdonmodules')->insert(array(
                'module' => 'vio_purge_unpaid_orders',
                'setting' => $k,
                'value' => $v,
            ));
        }
    }

    protected function getOrderStatuses() {
        $rows = DB::table('tblorderstatuses')->orderBy('sortorder')->get();
        $result = array();
        foreach ($rows as $row) {
            $result[$row->title] = $row->title;
        }
        return $result;
    }

    public function indexAction() {
        if (!empty($_POST)) {
            $data = $_POST;
            unset($data['token']);
            $this->updateConfig($data);
            $this->redirect('addonmodules.php?module=vio_purge_unpaid_orders&success=1');
        }
        echo $this->getSmartyTemplate('puoConfig', array(
            'orderStatuses' => $this->getOrderStatuses(),
            'admins' => $this->getAdmins(),
            'config' => $this->moduleSettings,
            'show_message' => !empty($_REQUEST['success'])
        ));
    }
    
    protected function getAdmins() {
        $rows = DB::table('tbladmins')->where('disabled', '0')->get();
        $result = array();
        foreach ($rows as $row) {
            $result[$row->username] = $row->firstname . ' ' . $row->lastname;
        }
        return $result;
    }
    
    public function manualAction() {
        $this->purgeUnpaidOrders();
        $this->redirect('systemactivitylog.php');
    }

    public function purgeUnpaidOrders() {
        logActivity('Purge unpaid orders started.');
        $orders = DB::table('tblorders')->where('status', 'Pending')->get();
        foreach($orders as $order) {
            $results = localAPI('CancelOrder', array('orderid' => $order->id), $this->moduleSettings['admin']);
            if($results['result'] == 'success') {
                DB::table('tblhosting')->where('orderid', $order->id)->delete();
            }
        }
        logActivity('Purge unpaid orders complete. Orders affected: ' . count($orders));
    }

}
