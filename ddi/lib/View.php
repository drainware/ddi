<?php

class View {

    protected $name;
    public $smarty;
    private $modules;
    private $news;
    private $module;
    private $action;
    private $master_template;
    private $sidebarMenu;
    private $topMenu;
    private $footer;


    public function __construct($name = null, $smarty = null) {
        $this->name = $name;
        $this->smarty = new Smarty;
        $this->modules = new ModulesModel();
        $this->sidebarMenu = 1;
        $this->topMenu = 1;
        $this->footer = 1;

    }

    public function setOptions($options = Array()) {
        $this->assign($options);
    }

    private function assign($options) {
        $options = array_merge($options, $this->modules->getAvaibleMenu());
        $this->smarty->assign("container", $this->name);
        $this->smarty->assign("container_translated", $GLOBALS['lang'][$this->name]);
        $this->smarty->assign("news", $this->news);

        foreach ($options as $option => $value) {
            $this->smarty->assign($option, $value);
        }

        $this->module = $this->smarty->get_template_vars('container');
        $this->action = $this->smarty->get_template_vars('action');
        $this->master_template = $this->module . "/" . $this->action . ".tpl";
    }

    public function setMasterTemplate($template) {
        $this->master_template = $template;
    }

    public function getModuleName() {
        return $this->module;
    }

    public function getActionName() {
        return $this->action;
    }

    public function display() {
        $this->setupLayout();
        $this->smarty->display($this->master_template);
    }


    protected function disableSidebarMenu()
    {
        $this->sidebarMenu = 0;
    }

    protected function disableTopMenu()
    {
        $this->topMenu = 0;
    }

    protected function disableFooter()
    {
        $this->footer = 0;
    }

    public function setupLayout()
    {
        $this->smarty->assign('top_menu', $this->topMenu);
        $this->smarty->assign('sidebar_menu', $this->sidebarMenu);
        $this->smarty->assign('footer', $this->footer);

    }

}

?>
