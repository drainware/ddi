<?

class GroupView extends View {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    public function display() {

        $action = parent::getActionName();

        switch ($action) {
            case 'getGroups':
                break;
            case 'create':
                break;
            default:
                parent::setMasterTemplate("base.tpl");
                break;
        }

        parent::display();
    }

}

?>
