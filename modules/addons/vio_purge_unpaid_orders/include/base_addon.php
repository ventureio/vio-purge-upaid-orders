<?php

class base_addon {

    protected $link;
    protected $language;
    protected $modulePath;

    public function setModuleLink($link) {
        $this->link = (string) $link;
        return $this;
    }

    public function setLanguage(array $language) {
        $this->language = $language;
        return $this;
    }

    protected function redirect($url) {
        header("Location: " . $url);
        die;
    }

    protected function getSmartyTemplate($name, $data = array()) {

        global $templates_compiledir;

        if (!class_exists('Smarty')) {
            throw new Exception('Smarty not found');
        }
        if (empty($this->modulePath)) {
            throw new Exception('Module path is not defined');
        }
        $smarty = new Smarty();
        $smarty->setCompileDir(!empty($templates_compiledir) ? $templates_compiledir : $this->modulePath . '/../../../templates_c');
        $smarty->setTemplateDir($this->modulePath . '/templates/');
        $data = array_merge(array('modulelink' => $this->link, 'lang' => $this->language), $data);
        $smarty->assign($data);
        return $smarty->fetch($name . '.tpl');
    }

    protected function getDbRows($sql) {
        $rows = array();
        $q = mysql_query($sql);
        while ($row = mysql_fetch_assoc($q)) {
            array_push($rows, $row);
        }
        return $rows;
    }

    public function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

}
