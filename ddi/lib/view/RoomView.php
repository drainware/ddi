<?

class RoomView extends View {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    public function display() {
        $action = parent::getActionName();

        switch ($action) {
            default:
                parent::setMasterTemplate("base.tpl");
                break;
        }

        parent::display();
    }

}

?>
