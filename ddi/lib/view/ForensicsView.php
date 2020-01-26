<?

class ForensicsView extends View {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    public function display() {

        $action = parent::getActionName();

        switch ($action) {
            case 'remoteQuery':
                break;
            case 'getLastSearchResults':
                break;
            case 'getLastListResult':
                break;
            case 'getLastGetResult':
                break;
            case 'downloadRemoteFile':
                break;
            case 'viewRemoteFile':
                break;
            case 'getRemoteDevices':
                break;
            case 'getRemoteDevicesMarker':
                break;
            default:
                parent::setMasterTemplate("base.tpl");
                break;
        }

        parent::display();
    }

}

?>
