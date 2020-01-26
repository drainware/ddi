<?php

class MainView extends View {



    public function __construct($name = null) {
        parent::__construct($name);
    }


    public function display() {

        $action = parent::getActionName();

        switch ($action) {
	    case 'login':
		$this->disableSidebarMenu();
		$this->disableTopMenu();
		$this->disableFooter();
		parent::setMasterTemplate("base.tpl");
		break;
            case 'register':
		$this->disableSidebarMenu();
		$this->disableTopMenu();
		parent::setMasterTemplate("base.tpl");
		break;
            case 'cpuUsage':
                break;
            case 'memUsage':
                break;
            case 'testLDAPConnection':
                break;
            case 'sendInvitations':
                break;
            default:
                parent::setMasterTemplate("base.tpl");
                break;
        }


        parent::display();
    }


}

?>
