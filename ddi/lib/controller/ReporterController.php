<?PHP

class ReporterController extends Controller {

    private $redirect_to = '?module=main';

    public function __construct($name = null) {
        parent::__construct($name);

        $all_modules = array('webfilter', 'dlp', 'atp', 'forensics');

        $modules = array();
        foreach ($all_modules as $module) {
            if (in_array($module, $GLOBALS['conf']['ddi']['modules'])) {
                $modules[$module] = 1;
            } else {
                $modules[$module] = 0;
            }
        }

        if (in_array('webfilter', $GLOBALS['conf']['ddi']['modules'])) {
            $this->redirect_to = '?module=reporter&action=show';
        } elseif (in_array('dlp', $GLOBALS['conf']['ddi']['modules'])) {
            $this->redirect_to = '?module=reporter&action=showDlpStats';
        } elseif (in_array('atp', $GLOBALS['conf']['ddi']['modules'])) {
            $this->redirect_to = '?module=reporter&action=showATPStats';
        } elseif (in_array('forensics', $GLOBALS['conf']['ddi']['modules'])) {
            $this->redirect_to = '?module=reporter&action=showSearchReports';
        }

        $options = array(
            'mstats' => $modules,
        );

        $this->options = array_merge($this->options, $options);
    }

    /*
     * Depending on the passed action it runs a method.
     * It must to recieve the showAction method at least.
     */

    protected function showAction() {

        if (in_array('webfilter', $GLOBALS['conf']['ddi']['modules'])) {
            $group_model = new GroupModel();
            $groups = $group_model->getGroups();
            
            $options = array(
                'groups' => $groups,
                'ngroups' => $groups->count(),
            );

            $this->options = array_merge($this->options, $options);
        } else {
            header('Location: ' . $this->redirect_to);
        }
    }

    protected function showBlockedAction() {

        $group_model = new GroupModel();
        $groups = $group_model->getGroups();

        $options = array(
            'groups' => $groups,
            'ngroups' => $groups->count(),
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getAccessTableAction() {

        $start = $_GET['start'];
        $end = $_GET['end'];
        if (!empty($start) && !empty($end)) {
            $range = array('start' => $start, 'end' => $end);
        }

        $reporter = new ReporterModel();
        $table = $reporter->getAccessTable(100, $range, $_GET);
        $table = json_encode($table);

        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getAccessPieAction() {

        $start = $_GET['start'];
        $end = $_GET['end'];
        if (!empty($start) && !empty($end)) {
            $range = array('start' => $start, 'end' => $end);
        }

        $reporter = new ReporterModel();
        $pie = $reporter->getAccessPie(10, $range, $_GET);
        $pie = json_encode($pie);

        $options = array(
            'pie' => $pie,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function urlDetailsAction() {

        $group_model = new GroupModel();
        $groups = $group_model->getGroups();

        if (isset($_GET['ip'])) {
            $param_ip = explode('.', $_GET['ip']);
        } else {
            $param_ip = '';
        }

        $options = array(
            'groups' => $groups,
            'ngroups' => $groups->count(),
            'param_start' => $_GET['start'],
            'param_end' => $_GET['end'],
            'param_group' => $_GET['group'],
            'param_user' => $_GET['user'],
            'param_ip' => $param_ip,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function getAccessHistogramByUrlAction() {

        $start = $_GET['start'];
        $end = $_GET['end'];
        if (!empty($start) && !empty($end)) {
            $range = array('start' => $start, 'end' => $end);
        }

        $reporter = new ReporterModel();
        $histogram = $reporter->getAccessHistogramByUrl(10, $range, $_GET['url'], $_GET);
        $histogram = json_encode($histogram);

        $options = array(
            'histogram' => $histogram,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getBlockedUrlsTableAction() {

        $start = $_GET['start'];
        $end = $_GET['end'];
        if (!empty($start) && !empty($end)) {
            $range = array('start' => $start, 'end' => $end);
        }

        $reporter = new ReporterModel();
        $table = $reporter->getBlockedUrlsTable(100, $range, $_GET);
        $table = json_encode($table);

        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getBlockedUrlsPieAction() {

        $reporter = new ReporterModel();
        $pie = $reporter->getBlockedUrlsPie(10, $range, $_GET);
        $pie = json_encode($pie);

        $options = array(
            'pie' => $pie,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getBlockedCategoriesTableAction() {

        $start = $_GET['start'];
        $end = $_GET['end'];
        if (!empty($start) && !empty($end)) {
            $range = array('start' => $start, 'end' => $end);
        }

        $reporter = new ReporterModel();
        $table = $reporter->getBlockedCategoriesTable(50, $range, $_GET);
        $table = json_encode($table);

        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getBlockedCategoriesPieAction() {

        $reporter = new ReporterModel();
        $pie = $reporter->getBlockedCategoriesPie(10, $range, $_GET);
        $pie = json_encode($pie);

        $options = array(
            'pie' => $pie,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function consoleAction() {

        // DONT REMOVE THIS METHOD
        //flexigrid
    }

    protected function getConsoleAction() {

        $reporter = new ReporterModel();
        $page = 1; // The current page
        $sortname = 'id'; // Sort column
        $sortorder = 'asc'; // Sort order
        $qtype = ''; // Search column
        $query = ''; // Search string
        // Get posted data
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
        }
        if (isset($_POST['sortname'])) {
            $sortname = trim($_POST['sortname']);
        }
        if (isset($_POST['sortorder'])) {
            $sortorder = trim($_POST['sortorder']);
        }
        if (isset($_POST['qtype'])) {
            $qtype = trim($_POST['qtype']);
        }
        if (isset($_POST['query'])) {
            $query = trim($_POST['query']);
            if ($query != '')
                $query = array($qtype => $query);
        }
        if (isset($_POST['rp'])) {
            $rp = trim($_POST['rp']);
        }

        $pageStart = ($page - 1) * $rp;
        $data = $reporter->getConsole($pageStart, $rp, $query);
        $data['page'] = $page;
        $table = json_encode($data);
        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function showDlpStatsAction() {
        $query = '';
        if (isset($_GET['date'])) {
            if ($_GET['date']['start'] != '') {
                $query = $query . '&start=' . $_GET['date']['start'];
                $query = $query . '&end=' . $_GET['date']['end'];
            }
        }

        if (isset($_GET['origin'])) {
            $query = $query . '&origin=' . implode(',', $_GET['origin']);
        }

        if (isset($_GET['endpoint_module'])) {
            $query = $query . '&endpoint_module=' . implode(',', $_GET['endpoint_module']);
        }

        if (isset($_GET['whom'])) {
            if ($_GET['whom']['ip'][0] != '') {
                $query = $query . '&ip=' . implode(',', $_GET['whom']['ip']);
            }

            if ($_GET['whom']['group']['names'][0] != '') {
                $query = $query . '&groups=' . implode(',', $_GET['whom']['group']['names']);
            }

            if ($_GET['whom']['user'] != '') {
                $query = $query . '&user=' . $_GET['whom']['user'];
            }
        }

        if (isset($_GET['policies'])) {
            $query = $query . '&policies=' . implode(',', $_GET['policies']);
        }

        if (isset($_GET['concepts'])) {
            $query = $query . '&concept=' . implode(',', $_GET['concepts']);
        }

        if (isset($_GET['subconcepts'])) {
            $query = $query . '&id=' . implode(',', $_GET['subconcepts']);
        }

        if (isset($_GET['rules'])) {
            $query = $query . '&id=' . implode(',', $_GET['rules']);
        }

        if (isset($_GET['files'])) {
            $query = $query . '&id=' . implode(',', $_GET['files']);
        }

        if (isset($_GET['network_places'])) {
            $query = $query . '&id=' . implode(',', $_GET['network_places']);
        }
        
        if (isset($_GET['app'])) {
            $query = $query . '&app=' . implode(',', $_GET['app']);
        }
        
        if (isset($_GET['severity'])) {
            $query = $query . '&severity=' . implode(',', $_GET['severity']);
        }

        if (isset($_GET['limit'])) {
            $query = $query . '&limit=' . $_GET['limit'];
        }
        
        $_SESSION['DLP_REPORT_SEARCH'] = $_SERVER['REQUEST_URI'];

        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUser();
        $events = $client['events']['global']['dlp'];
        
        $group_model = new GroupModel();
        $groups = $group_model->getGroups();

        $policy_model = new PolicyModel();
        $policies = $policy_model->getPolicies($_SESSION['license']);

        $concept_model = new ConceptModel();
        $concepts = $concept_model->getConcepts();

        $subconcept_model = new SubconceptModel();
        foreach ($concepts as $id_concept => $concept_obj) {
            $subconcepts = $subconcept_model->getSubConceptsByIdConcept($id_concept);
            $concepts_subconcepts[$id_concept] = Array('concept' => $concept_obj, 'subconcepts' => $subconcepts);
        }

        $rule_model = new RuleModel();
        $rules = $rule_model->getRules($_SESSION['license']);

        $file_model = new FileModel();
        $files = $file_model->getFiles($_SESSION['license']);

        $network_place_model = new NetworkPlaceModel();
        $network_places = $network_place_model->getNetworkPlaces($_SESSION['license']);        
        
        $reporter_model = new ReporterModel();
        $applications = $reporter_model->getDLPEventApplications();

        
        $options = array(
            'policies' => $policies,
            'concepts_subconcepts' => $concepts_subconcepts,
            'rules' => $rules,
            'files' => $files,
            'network_places' => $network_places,
            'applications' => $applications,
            'groups' => $groups,
            'ngroups' => $groups->count(),
            'fields' => $_GET,
            'query' => $query,
            'events' => $events
        );

        $this->options = array_merge($this->options, $options);
    }

    public function showDlpStatsSliderAction() {
        
        $reporter_model = new ReporterModel();
        
        $options = array(
            'gpolicies' => $reporter_model->getDLPPolicyGroupList(),
            'upolicies' => $reporter_model->getDLPPolicyUserList()
        );

        $this->options = array_merge($this->options, $options);
        
    }

    public function getDlpEventsHistogramAction() {

        $params = $_GET;

        $query = array();
        
        $limit = 1000;
        if(isset($params['limit'])){
            if(is_numeric($params['limit'])){
                $limit = (int)$params['limit'];
            }
        }
        
        if (!(isset($params['start']) && isset($params['end']))) {
            $format = 'Y-m-d';
            $query['timetime']['$gte'] = new MongoDate(strtotime(date($format, mktime(0, 0, 0, date('m'), date('d') - 60, date('Y')))));
            $query['timetime']['$lte'] = new MongoDate(strtotime(date($format) . ' 23:59:59'));
        } else {
            $query['timetime']['$gte'] = new MongoDate(strtotime(str_replace('.', '-', $params['start'])));
            $query['timetime']['$lte'] = new MongoDate(strtotime(str_replace('.', '-', $params['end'])));
        }

        unset($params['module']);
        unset($params['action']);
        unset($params['start']);
        unset($params['end']);
        unset($params['limit']);

        foreach ($params as $key => $value) {
            if ($key == 'endpoint_module') {
                $query['origin']['$in'][] = 'endpoint';
            }
            if($key == 'app'){
                $key = "app.description";
            }
            $query[$key]['$in'] = explode(',', $value);
        }
        syslog(LOG_DEBUG, json_encode($query));
        $reporter = new ReporterModel();
        $dlp_histogram = $reporter->getDlpEventsHistogram($query, $limit);
        $histogram = json_encode($dlp_histogram);

        $options = array(
            'histogram' => $histogram,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getConsoleDLPAction() {
        $params = $_GET;

        $query = array();

        if (!(isset($params['start']) && isset($params['end']))) {
            $format = 'Y-m-d';
            $query['timetime']['$gte'] = new MongoDate(strtotime(date($format, mktime(0, 0, 0, date('m'), date('d') - 60, date('Y')))));
            $query['timetime']['$lte'] = new MongoDate(strtotime(date($format) . ' 23:59:59'));
        } else {
            $query['timetime']['$gte'] = new MongoDate(strtotime(str_replace('.', '-', $params['start'])));
            $query['timetime']['$lte'] = new MongoDate(strtotime(str_replace('.', '-', $params['end'])));
        }

        unset($params['module']);
        unset($params['action']);
        unset($params['start']);
        unset($params['end']);
        unset($params['limit']);

        foreach ($params as $key => $value) {
            if ($key == 'endpoint_module') {
                $query['origin']['$in'][] = "endpoint";
            }
            if($key == 'app'){
                $key = "app.description";
            }
            $query[$key]['$in'] = explode(',', $value);
        }

        $page = 1; // The current page
        $rowspage = 15; // Element per page

        if (isset($_POST['page'])) {
            $page = $_POST['page'];
        }

        $pageStart = ($page - 1) * $rowspage;

        $reporter_model = new ReporterModel();
        $data = $reporter_model->getConsoleDLP($query, $pageStart, $rowspage);
        $data['page'] = $page;
        $table = json_encode($data);
        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getDLPEventDetailsAction(){
        $cloud_model = new CloudModel();
        $cloud_user_object = $cloud_model->getCloudUserByLicense($_SESSION['license']);
        
        $event_id = $_GET['event_id'];
        $dlp_event = new DlpEventDetailsModel($event_id);
        $options = array(
            'event' => $dlp_event,
            'dlp_report_search' => $_SESSION['DLP_REPORT_SEARCH'],
            'cloud_user_type' => $cloud_user_object['type'],
        );

        $this->options = array_merge($this->options, $options);
    }


    protected function consoleDLPEventAction() {

        $cloud_model = new CloudModel();
        $cloud_user_object = $cloud_model->getCloudUserByLicense($_SESSION['license']);
        
        $event_id = $_GET['event_id'];
        $dlp_event = new DlpEventDetailsModel($event_id);
        $options = array(
            'event' => $dlp_event,
            'dlp_report_search' => $_SESSION['DLP_REPORT_SEARCH'],
            'cloud_user_type' => $cloud_user_object['type'],
        );

        $this->options = array_merge($this->options, $options);
    }

    
    private function  getPeriodData($period){
        $name = 'G';
        $query = array();
        
        if(isset($period)){
            switch ($period) {
                case '1D':
                    $name = 'M';
                    $query['date'] = date('Y.m.d', strtotime('-1 day'));
                    break;
                case '1M':
                    $name = 'Y';
                    $query['month'] = date('m');
                    break;
                case '6M':
                    $name = 'S';
                    break;
                case '1Y':
                    $name = 'G';
                    $query['year'] = date('Y');
                    break;
                case 'G':
                    $name = 'G';
                    break;
                default:
                    break;
            }
        }
        
        $data = array(
            'name' => $name,
            'query' => $query
        );
        
        return $data;
    }
    
    protected function getDlpBubbleActivityAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        $reporter = new ReporterModel();
        $dlp_bubble = $reporter->getDlpBubbleActivity($period_data['name'], $period_data['query']);
        $bubble = json_encode($dlp_bubble);

        $options = array(
            'bubble' => $bubble,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getDlpTableActivityAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        $reporter = new ReporterModel();
        $table = json_encode($reporter->getDlpTableActivity($period_data['name'], $period_data['query']));

        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getDlpPolicyGroupBarAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        $period_data['query']['policy'] = $_GET['policy'];
        
        $reporter = new ReporterModel();
        $dlp_bar = $reporter->getDlpPolicyGroupBar($period_data['name'], $period_data['query'], 10);
        $bar = json_encode($dlp_bar);

        $options = array(
            'bar' => $bar,
        );

        $this->options = array_merge($this->options, $options);
    }
    
    protected function getDlpPolicyGroupTableAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        $period_data['query']['policy'] = $_GET['policy'];
        
        $reporter = new ReporterModel();
        $dlp_table = $reporter->getDlpPolicyGroupTable($period_data['name'], $period_data['query'], 10);
        $table = json_encode($dlp_table);

        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }
    
    protected function getDlpPolicyUserBarAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        $period_data['query']['policy'] = $_GET['policy'];
        
        $reporter = new ReporterModel();
        $dlp_bar = $reporter->getDlpPolicyUserBar($period_data['name'], $period_data['query'], 10);
        $bar = json_encode($dlp_bar);

        $options = array(
            'bar' => $bar,
        );

        $this->options = array_merge($this->options, $options);
    }
    
    protected function getDlpPolicyUserTableAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        $period_data['query']['policy'] = $_GET['policy'];
        
        $reporter = new ReporterModel();
        $dlp_table = $reporter->getDlpPolicyUserTable($period_data['name'], $period_data['query'], 10);
        $table = json_encode($dlp_table);

        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }
    
    protected function getDlpPieAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        $reporter = new ReporterModel();
        $dlp_pie = $reporter->getDlpPie($_GET['filter'], $period_data['name'], $period_data['query'], 10);
        $pie = json_encode($dlp_pie);

        $options = array(
            'pie' => $pie,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getDlpTableAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        $reporter = new ReporterModel();
        $dlp_table = $reporter->getDlpTable($_GET['filter'], $period_data['name'], $period_data['query'], 10);
        $table = json_encode($dlp_table);

        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function getDlpReportAction() {

        $params = $_GET;

        $query = array();

        if (!(isset($params['start']) && isset($params['end']))) {
            $format = 'Y-m-d';
            $query['timetime']['$gte'] = new MongoDate(strtotime(date($format, mktime(0, 0, 0, date('m'), date('d') - 60, date('Y')))));
            $query['timetime']['$lte'] = new MongoDate(strtotime(date($format) . ' 23:59:59'));
        } else {
            $query['timetime']['$gte'] = new MongoDate(strtotime(str_replace('.', '-', $params['start'])));
            $query['timetime']['$lte'] = new MongoDate(strtotime(str_replace('.', '-', $params['end'])));
        }

        unset($params['module']);
        unset($params['action']);
        unset($params['start']);
        unset($params['end']);

        foreach ($params as $key => $value) {
            if ($key == 'endpoint_module') {
                $query['origin']['$in'][] = 'endpoint';
            }
            if($key == 'app') {
                $key = "app.description";
            }
            $query[$key]['$in'] = explode(',', $value);
        }

        $reporter_model = new ReporterModel();
        $data = $reporter_model->getDLPReport($query);
        $rows = sizeof($data) + 1;

        header('Content-disposition: attachment; filename=report.csv');
        header('Content-type: text/csv');

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()->setCreator('Drainware')
                ->setLastModifiedBy('Drainware')
                ->setTitle('Office 2007 XLSX Test Document')
                ->setSubject('Office 2007 XLSX Test Document')
                ->setDescription('Test document for Office 2007 XLSX, generated using Drainware.')
                ->setKeywords('office 2007 openxml php')
                ->setCategory('Test result file');



        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);


        $objPHPExcel->getActiveSheet()->getStyle('A1:Q' . $rows)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);

        for ($i = 0; $i <= 16; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension(chr(65 + $i))->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet(0)->setCellValue('A1', 'Date')
                ->setCellValue('B1', 'Ip')
                ->setCellValue('C1', 'Groups')
                ->setCellValue('D1', 'User')
                ->setCellValue('E1', 'Origin')
                ->setCellValue('F1', 'Module')
                ->setCellValue('G1', 'Policies')
                ->setCellValue('H1', 'Types')
                ->setCellValue('I1', 'Concept')
                ->setCellValue('J1', 'SubConcept')
                ->setCellValue('K1', 'Rule')
                ->setCellValue('L1', 'File')
                ->setCellValue('M1', 'App')
                ->setCellValue('N1', 'Filename')
                ->setCellValue('O1', 'Match')
                ->setCellValue('P1', 'Action')
                ->setCellValue('Q1', 'Severity');
                
        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                //$objPHPExcel->getActiveSheet()->setCellValue($cel, $data);
                $objPHPExcel->getActiveSheet(0)->setCellValue($key, $value);
            }
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->save('php://output');
    }

    public function showATPStatsAction() {

        $query = '';

        if ($_GET['date']['start'] != '') {
            $query = $query . '&start=' . $_GET['date']['start'];
            $query = $query . '&end=' . $_GET['date']['end'];
        }

        if ($_GET['whom']['ip'][0] != '') {
            $query = $query . '&ip=' . implode(',', $_GET['whom']['ip']);
        }

        if ($_GET['whom']['group']['names'][0] != '') {
            $query = $query . '&groups=' . implode(',', $_GET['whom']['group']['names']);
        }

        if ($_GET['whom']['user'] != '') {
            $query = $query . '&user=' . $_GET['whom']['user'];
        }

        if ($_GET['app']['names'][0] != '') {
            $query = $query . '&app=' . implode(',', $_GET['app']['names']);
        }

        if (isset($_GET['limit'])) {
            $query = $query . '&limit=' . implode(',', $_GET['limit']);
        }
        
        $_SESSION['DLP_REPORT_SEARCH'] = $_SERVER['REQUEST_URI'];

        $group_model = new GroupModel();
        $groups = $group_model->getGroups();

        $app_model = new AppModel();
        $user_apps = $app_model->getApps();
        $predefined_apps = $app_model->getPredefinedApps();

        $tmp_user_apps = array();
        foreach ($user_apps as $user_app_id => $user_app_object) {
            if (isset($user_app_object['editable'])) {
                $predefined_app = $app_model->getPredefinedApp($user_app_object['predefined_app']);
                $user_app_object['name'] = $predefined_app['name'];
                $user_app_object['description'] = $predefined_app['description'];
            }
            $tmp_user_apps[$user_app_id] = $user_app_object;
        }

        $apps = array_merge($tmp_user_apps, iterator_to_array($predefined_apps));
        
        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUser();
        $events = $client['events']['global']['atp'];
        
        $options = array(
            'groups' => $groups,
            'apps' => $apps,
            'ngroups' => $groups->count(),
            'fields' => $_GET,
            'query' => $query,
            'events' => $events
        );

        $this->options = array_merge($this->options, $options);
    }

    public function showATPStatsSliderAction() {
        
    }    

    public function getATPEventsHistogramAction() {

        $params = $_GET;

        $query = array();
        
        $limit = 1000;
        if(isset($params['limit'])){
            $limit = $params['limit'];
        }
        
        if (!(isset($params['start']) && isset($params['end']))) {
            $format = 'Y-m-d';
            $query['timetime']['$gte'] = new MongoDate(strtotime(date($format, mktime(0, 0, 0, date('m'), date('d') - 60, date('Y')))));
            $query['timetime']['$lte'] = new MongoDate(strtotime(date($format) . ' 23:59:59'));
        } else {
            $query['timetime']['$gte'] = new MongoDate(strtotime(str_replace('.', '-', $params['start'])));
            $query['timetime']['$lte'] = new MongoDate(strtotime(str_replace('.', '-', $params['end'])));
        }

        unset($params['module']);
        unset($params['action']);
        unset($params['start']);
        unset($params['end']);
        unset($params['limit']);

        foreach ($params as $key => $value) {
            $query[$key]['$in'] = explode(',', $value);
        }

        $reporter = new ReporterModel();
        $events_histogram = $reporter->getATPEventsHistogram($query, $limit);
        $histogram = json_encode($events_histogram);

        $options = array(
            'histogram' => $histogram,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getConsoleATPAction() {

        $params = $_GET;

        $query = array();

        if (!(isset($params['start']) && isset($params['end']))) {
            $format = 'Y-m-d';
            $query['timetime']['$gte'] = new MongoDate(strtotime(date($format, mktime(0, 0, 0, date('m'), date('d') - 60, date('Y')))));
            $query['timetime']['$lte'] = new MongoDate(strtotime(date($format) . ' 23:59:59'));
        } else {
            $query['timetime']['$gte'] = new MongoDate(strtotime(str_replace('.', '-', $params['start'])));
            $query['timetime']['$lte'] = new MongoDate(strtotime(str_replace('.', '-', $params['end'])));
        }

        unset($params['module']);
        unset($params['action']);
        unset($params['start']);
        unset($params['end']);
        unset($params['limit']);

        foreach ($params as $key => $value) {
            $query[$key]['$in'] = explode(',', $value);
        }

        $page = 1; // The current page
        $rowspage = 15; // Element per page

        if (isset($_POST['page'])) {
            $page = $_POST['page'];
        }

        $pageStart = ($page - 1) * $rowspage;

        $reporter_model = new ReporterModel();
        $data = $reporter_model->getConsoleATP($query, $pageStart, $rowspage);
        $data['page'] = $page;
        $table = json_encode($data);
        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getATPPieByAppAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        
        $reporter = new ReporterModel();
        $pie_app = $reporter->getATPPieByApp($period_data['name'], $period_data['query'], 10);
        $pie = json_encode($pie_app);

        $options = array(
            'pie' => $pie,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getATPTableByAppAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        
        $reporter = new ReporterModel();
        $table_app = $reporter->getATPTableByApp($period_data['name'], $period_data['query'], 10);
        $table = json_encode($table_app);

        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getATPPieByGroupAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        
        $reporter = new ReporterModel();
        $pie_group = $reporter->getATPPieByGroup($period_data['name'], $period_data['query'], 10);
        $pie = json_encode($pie_group);

        $options = array(
            'pie' => $pie,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function getATPTableByGroupAction() {
        $period_data = $this->getPeriodData($_GET['period']);
        
        $reporter = new ReporterModel();
        $table_group = $reporter->getATPTableByGroup($period_data['name'], $period_data['query'], 10);
        $table = json_encode($table_group);

        $options = array(
            'table' => $table,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function showSearchReportsAction() {

        $search_report = array();
        $report_list = array();
        $report_list_count = array();
        if (isset($_GET['report'])) {
            $report_model = new ReporterModel();
            $report = $report_model->getForensicsQueryList($_GET['report']);

            $remote_query_model = new ForensicsModel();

            foreach ($report['list'] as $query_id => $query_args) {
                $report_list[$query_id] = implode(', ', $query_args);

                $query_results_cursor = $remote_query_model->getObjectsResults($query_id, "search");

                $report_list_count[$query_id] = $query_results_cursor->count();

                $query_results = array();
                foreach ($query_results_cursor as $query_result) {
                    $query_results[] = $query_result;
                }

                $filter = new WordFilterModel();
                $filter_options = array('where_apply' => 'context', 'term' => 'SuP3rP455W0rD');
                array_walk_recursive($query_results, array($filter, 'configureDescription'), $filter_options);

                $search_report[$query_id] = $query_results;
            }
        }

        $options = array(
            'report_list' => $report_list,
            'report_list_count' => $report_list_count,
            'search_report' => $search_report,
        );
        $this->options = array_merge($this->options, $options);
    }

    public function removeSearchReportAction(){
        $reporter_model = new ReporterModel();
        $reporter_model->removeReport($_POST['id']);
    }

    public function getListReportAction() {

        $query = array();

        $format = 'Y-m-d';
        $query['timetime']['$gte'] = new MongoDate(strtotime(date($format, mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')))));
        $query['timetime']['$lte'] = new MongoDate(strtotime(date($format) . ' 23:59:59'));
        if (strlen($_POST['start']) != 0) {//if (isset($_POST['start'])) {
            if (strlen($_POST['end']) != 0) {//if(isset($_POST['end'])) {
                $query['timetime']['$gte'] = new MongoDate(strtotime($_POST['start'] . ' 00:00:00'));
                $query['timetime']['$lte'] = new MongoDate(strtotime($_POST['end'] . ' 23:59:59'));
            }
        }

        $report_model = new ReporterModel();
        $mongo_cursor = $report_model->getForensicsReportList(100000, $query);

        $search_reports = array();
        foreach ($mongo_cursor as $element) {
            $search_reports[] = $element;
        }

        $options = array(
            'search_reports' => json_encode($search_reports),
        );

        $this->options = array_merge($this->options, $options);
    }

    public function getForensicsReportAction() {

        if (isset($_GET['report'])) {

            $reporter_model = new ReporterModel();

            header('Content-disposition: attachment; filename=forensics_report.xlsx');
            header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            $objPHPExcel = new PHPExcel();

            $objPHPExcel->getProperties()->setCreator('Drainware')
                    ->setLastModifiedBy('Drainware')
                    ->setTitle('Office 2007 XLSX Test Document')
                    ->setSubject('Office 2007 XLSX Test Document')
                    ->setDescription('Test document for Office 2007 XLSX, generated using Drainware.')
                    ->setKeywords('office 2007 openxml php')
                    ->setCategory('Test result file');

            $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
            $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

            $report = $reporter_model->getForensicsQueryList($_GET['report']);

            $number_sheet = 0;
            foreach ($report['list'] as $query_id => $query_args) {
                $data = $reporter_model->getForensicsSearchReport($query_id);
                if ($data != null) {
                    $rows = sizeof($data) + 1;

                    $objPHPExcel->setActiveSheetIndex($number_sheet);
                    $objPHPExcel->getActiveSheet()->setTitle(implode(', ', $query_args));
                    $objPHPExcel->getActiveSheet()->getStyle('A1:I' . $rows)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);

                    for ($i = 0; $i <= 8; $i++) {
                        $objPHPExcel->getActiveSheet()->getColumnDimension(chr(65 + $i))->setAutoSize(true);
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Date')
                            ->setCellValue('B1', 'Machine')
                            ->setCellValue('C1', 'IP')
                            ->setCellValue('D1', 'Path')
                            ->setCellValue('E1', 'Name')
                            ->setCellValue('F1', 'Modified')
                            ->setCellValue('G1', 'Type')
                            ->setCellValue('H1', 'Coincidence')
                            ->setCellValue('I1', 'Context');
                    //->setCellValue('J1', 'Register Date');            

                    foreach ($data as $row) {
                        foreach ($row as $key => $value) {
                            $objPHPExcel->getActiveSheet()->setCellValue($key, $value);
                        }
                    }

                    if ($number_sheet <= sizeof($report['list'])) {
                        $objPHPExcel->createSheet();
                    }
                    $number_sheet++;
                }
            }

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            $objWriter->save('php://output');
        }
    }
    
    public function showDailyDLPEventsAction(){
        
    }


    public function getDailyDLPEventsAction(){
        $reporter_model = new ReporterModel();
        $events = $reporter_model->getDailyDLPEvents();
        
        $data = array();
        foreach ($events as $elem) {
            $data[] = array( $elem['date'], $elem['count']);
        }
        
        $options = array(
            'response' => json_encode($data),
        );

        $this->options = array_merge($this->options, $options);
    }
}

?>
