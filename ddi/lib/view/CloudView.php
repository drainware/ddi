<?

class CloudView extends View {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    public function display() {

        $action = parent::getActionName();

        switch ($action) {
            case 'savePremiumUpgrade':
                break;
            case 'checkPayment':
                break;
            case 'validateLicense':
                break;
            case 'registerUser':
		$this->disableSidebarMenu();
		$this->disableTopMenu();
		parent::setMasterTemplate("base.tpl");
		break;
            case 'registered':
		$this->disableSidebarMenu();
		$this->disableTopMenu();
		parent::setMasterTemplate("base.tpl");
		break;
            case 'validateUser':
		$this->disableSidebarMenu();
		$this->disableTopMenu();
		parent::setMasterTemplate("base.tpl");
		break;
            case 'recoveryPassword':
		$this->disableSidebarMenu();
		$this->disableTopMenu();
		parent::setMasterTemplate("base.tpl");
		break;
            case 'sendRecoveryEmail':
		$this->disableSidebarMenu();
		$this->disableTopMenu();
		parent::setMasterTemplate("base.tpl");
		break;
            case 'resetPassword':
		$this->disableSidebarMenu();
		$this->disableTopMenu();
		parent::setMasterTemplate("base.tpl");
		break;
            case 'newPassword':
		$this->disableSidebarMenu();
		$this->disableTopMenu();
		parent::setMasterTemplate("base.tpl");
		break;
            default:
                parent::setMasterTemplate("base.tpl");
                break;
        }

        parent::display();
    }

}

?>
