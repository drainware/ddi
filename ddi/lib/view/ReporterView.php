<?

class ReporterView extends View {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    public function display() {

        $action = parent::getActionName();

        switch ($action) {
            case 'getAccessPie':
                break;
            case 'getAccessTable':
                break;
            case 'getAccessHistogramByUrl':
                break;
            case 'getAccessPieByGroup':
                break;
            case 'getBlockedCategoriesPie':
                break;
            case 'getBlockedCategoriesTable':
                break;
            case 'getBlockedUrlsPie':
                break;
            case 'getBlockedUrlsTable':
                break;
            case 'getConsole':
                break;
            case 'getDlpEventsHistogram':
                break;
            case 'getConsoleDLP':
                break;
            case 'getDlpReport':
                break;
            case 'getDlpBubbleActivity':
                break;
            case 'getDlpTableActivity':
                break;
            case 'getDlpPolicyGroupBar':
                break;
            case 'getDlpPolicyGroupTable':
                break;
            case 'getDlpPolicyUserBar':
                break;
            case 'getDlpPolicyUserTable':
                break;
            case 'getDlpPie':
                break;
            case 'getDlpTable':
                break;
            case 'getATPEventsHistogram':
                break;
            case 'getConsoleATP':
                break;
            case 'getATPPieByApp':
                break;
            case 'getATPTableByApp':
                break;
            case 'getATPPieByGroup':
                break;
            case 'getATPTableByGroup':
                break;
            case 'getListReport':
                break;
            case 'consoleDlpEvent':
		$this->disableSidebarMenu();
		$this->disableTopMenu();
		parent::setMasterTemplate("base.tpl");
		break;
            case 'showDailyDLPEvents':
                break;
            case 'getDailyDLPEvents':
                break;
            default:
                parent::setMasterTemplate("base.tpl");
                break;
        }


        parent::display();
    }

}

?>
