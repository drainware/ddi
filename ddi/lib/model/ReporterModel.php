<?

class ReporterModel extends Model {

    private $db;
    private $wf_events_col_name;
    private $dlp_events_col_name;
    private $atp_events_col_name;
    
    private $col_dlp_events_daily;

    public function __construct() {
        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUser();
        date_default_timezone_set($client['timezone']);
        
        $mongo_model = new MongoModel();
        $con = $mongo_model->connect();
        $this->db = $con->drs;
        $this->col_dlp_events_daily = $this->db->dlp_events_daily;
        
        $this->wf_events_col_name = 'wf_events';
        $this->dlp_events_col_name = 'dlp_events';
        $this->atp_events_col_name = 'atp_events';

        if (isset($_SESSION['license'])) {
            $license = 'LIC_' . preg_replace('/-/', '_', $_SESSION['license']);
            $this->wf_events_col_name = $license . '_' . $this->wf_events_col_name;
            $this->dlp_events_col_name = $license . '_' . $this->dlp_events_col_name;
            $this->atp_events_col_name = $license . '_' . $this->atp_events_col_name;
        }
        
    }

    private function getAccess($limit, $range, $params) {

        if (empty($range)) {
            $format = 'Y.m.d';
            $start = date($format, mktime(0, 0, 0, date('m'), date('d') - 60, date('Y')));
            $end = date($format);
        } else {
            $start = str_replace('/', '.', $range['start']);
            $end = str_replace('/', '.', $range['end']);
        }

        $query = array('day' => array('$gte' => $start, '$lte' => $end));

        if (isset($params['group'])) {
            $col = $this->wf_events_col_name . '_access_urls_by_group';
            $query['group'] = $params['group'];
        } elseif (isset($params['user'])) {
            $col = $this->wf_events_col_name . '_access_urls_by_user';
            $query['user'] = $params['user'];
        } elseif (isset($params['ip'])) {
            $col = $this->wf_events_col_name . '_access_urls_by_client_ip';
            $query['ip'] = $params['ip'];
        } else {
            $col = $this->wf_events_col_name . '_access_urls';
        }

        $map = new MongoCode('function() {
      emit({key : {url: this.url}}, {count: this.count});
      }');

        $reduce = new MongoCode('function(key, values) {
      var count = 0;
      values.forEach(function(v) {
      count += v["count"];
      });

      return {count: count};
      }');

        $res = $this->db->command(array('mapreduce' => $col,
            'out' => $this->wf_events_col_name . '_access_urls_tmp',
            'map' => $map,
            'reduce' => $reduce,
            'query' => $query));

        try {
            $cursor = $this->db->selectCollection($res['result'])->find()->limit($limit)->sort(array('value' => -1));
        } catch (Exception $ex) {
            $cursor = null;
        }
        return $cursor;
    }

    private function getBlockedUrls($limit, $range, $params) {

        if (empty($range)) {
            $format = 'Y.m.d';
            $start = date($format, mktime(0, 0, 0, date('m'), date('d') - 60, date('Y')));
            $end = date($format);
        } else {
            $start = str_replace('/', '.', $range['start']);
            $end = str_replace('/', '.', $range['end']);
        }

        $query = array('day' => array('$gte' => $start, '$lte' => $end));

        if (isset($params['group'])) {
            $col = $this->wf_events_col_name . 'wf_blocked_urls_by_group';
            $query['group'] = $params['group'];
        } elseif (isset($params['user'])) {
            $col = $this->wf_events_col_name . 'wf_blocked_urls_by_user';
            $query['user'] = $params['user'];
        } elseif (isset($params['ip'])) {
            $col = $this->wf_events_col_name . 'wf_blocked_urls_by_client_ip';
            $query['ip'] = $params['ip'];
        } else {
            $col = $this->wf_events_col_name . 'wf_blocked_urls';
        }

        $map = new MongoCode('function() {
      emit({key : {url: this.url}}, {count: this.count});
    }');

        $reduce = new MongoCode('function(key, values) {
     var count = 0;
     values.forEach(function(v) {
       count += v["count"];
     });

     return {count: count};
    }');

        $res = $this->db->command(array('mapreduce' => $col,
            'out' => $this->wf_events_col_name . '_blocked_urls_tmp',
            'map' => $map,
            'reduce' => $reduce,
            'query' => $query));

        try {
            $cursor = $this->db->selectCollection($res['result'])->find()->limit($limit)->sort(array('value' => -1));
        } catch (Exception $ex) {
            $cursor = null;
        }
        return $cursor;
    }

    public function getBlockedCategories($limit, $range, $params) {

        if (empty($range)) {
            $format = 'Y.m.d';
            $start = date($format, mktime(0, 0, 0, date('m'), date('d') - 60, date('Y')));
            $end = date($format);
        } else {
            $start = str_replace('/', '.', $range['start']);
            $end = str_replace('/', '.', $range['end']);
        }

        $query = array('day' => array('$gte' => $start, '$lte' => $end));

        if (isset($params['group'])) {
            $col = $this->wf_events_col_name . '_blocked_categories_by_group';
            $query['group'] = $params['group'];
        } elseif (isset($params['user'])) {
            $col = $this->wf_events_col_name . '_blocked_categories_by_user';
            $query['user'] = $params['user'];
        } elseif (isset($params['ip'])) {
            $col = $this->wf_events_col_name . '_blocked_categories_by_client_ip';
            $query['ip'] = $params['ip'];
        } else {
            $col = $this->wf_events_col_name . '_blocked_categories';
        }

        $map = new MongoCode('function() {
      emit({key : {category: this.category}}, {count: this.count});
    }');
        $reduce = new MongoCode('function(key, values) {
     var count = 0;
     values.forEach(function(v) {
       count += v["count"];
     });

     return {count: count};
    }');


        $res = $this->db->command(array('mapreduce' => $col,
            'out' => $this->wf_events_col_name . '_blocked_categories_tmp',
            'map' => $map,
            'reduce' => $reduce,
            'query' => $query));

        try {
            $cursor = $this->db->selectCollection($res['result'])->find()->sort(array('value' => -1))->limit($limit);
        } catch (Exception $ex) {
            $cursor = null;
        }
        return $cursor;
    }

    public function getAccessTable($limit, $range, $params) {
        $cursor = $this->getAccess($limit, $range, $params);

        $i = 0;
        foreach ($cursor as $element) {
            //$percent = number_format(($element['value']['count']/$total)*100,0);
            $percent = $element['value']['count'];
            $data[] = array('id' => $i, 'cell' => array($element['_id']['key']['url'], $percent));
            $i++;
        }
        $table_array = array('rows' => $data, 'page' => 1, 'total' => count($data));
        return $table_array;
    }

    public function getAccessPie($limit, $range, $params) {
        $cursor = $this->getAccess($limit, $range, $params);

        $data = array();
        foreach ($cursor as $element) {
            //$percent = number_format(($element['value']['count']/$total)*100,0);
            $percent = $element['value']['count'];
            $data[] = array($element['_id']['key']['url'], $percent);
        }

        return $data;
    }

    public function getAccessHistogramByUrl($limit, $range, $url, $params) {

        if (empty($range)) {
            $format = 'Y.m.d';
            $start = date($format, mktime(0, 0, 0, date('m'), date('d') - 60, date('Y')));
            $end = date($format);
        } else {
            $start = str_replace('/', '.', $range['start']);
            $end = str_replace('/', '.', $range['end']);
        }

        $query = array('url' => $url, 'day' => array('$gte' => $start, '$lte' => $end));

        if (isset($params['group'])) {
            $col = $this->wf_events_col_name . '_access_urls_by_group';
            $query['group'] = $params['group'];
        } elseif (isset($params['user'])) {
            $col = $this->wf_events_col_name . '_access_urls_by_user';
            $query['user'] = $params['user'];
        } elseif (isset($params['ip'])) {
            $col = $this->wf_events_col_name . '_access_urls_by_client_ip';
            $query['ip'] = $params['ip'];
        } else {
            $col = $this->wf_events_col_name . '_access_urls';
        }

        $access_col = $this->db->$col;
        $cursor = $access_col->find($query)->limit($limit)->sort(array('count' => -1));

        $data = array();
        foreach ($cursor as $element) {
            $date = DateTime::createFromFormat('yy.m.d', $element['day']);
            //$percent = number_format(($element['value']['count']/$total)*100,0);
            $data[date('d-M-y', $date->getTimestamp())] = $element['count'];
        }

        $ret_data = Array();
        foreach ($data as $mydate => $mycount)
            $ret_data[] = array($mydate, $mycount);

        return $ret_data;
    }

    public function getBlockedUrlsTable($limit, $range, $params) {

        $cursor = $this->getBlockedUrls($limit, $range, $params);

        $i = 0;
        foreach ($cursor as $element) {
            //$percent = number_format(($element['value']['count']/$total)*100,0);
            $percent = $element['value']['count'];
            $data[] = array('id' => $i, 'cell' => array($element['_id']['key']['url'], $percent));
            $i++;
        }
        $table_array = array('rows' => $data, 'page' => 1, 'total' => count($data));
        return $table_array;
    }

    public function getBlockedUrlsPie($limit, $range, $params) {

        $cursor = $this->getBlockedUrls($limit, $range, $params);

        $data = array();
        foreach ($cursor as $element) {
            //$percent = number_format(($element['value']['count']/$total)*100,0);
            $percent = $element['value']['count'];
            $data[] = array($element['_id']['key']['url'], $percent);
        }

        return $data;
    }

    public function getBlockedCategoriesTable($limit, $range, $params) {

        $cursor = $this->getBlockedCategories($limit, $range, $params);

        $data = array();
        $i = 0;
        foreach ($cursor as $element) {
            //$percent = number_format(($element['value']['count']/$total)*100,0);
            $percent = $element['value']['count'];
            $data[] = array('id' => $i, 'cell' => array($element['_id']['key']['category'], $percent));
            $i++;
        }
        $table_array = array('rows' => $data, 'page' => 1, 'total' => count($data));

        return $table_array;
    }

    public function getBlockedCategoriesPie($limit, $range, $params) {

        $cursor = $this->getBlockedCategories($limit, $range, $params);

        $data = array();
        foreach ($cursor as $element) {
            //$percent = number_format(($element['value']['count']/$total)*100,0);
            $percent = $element['value']['count'];
            $data[] = array($element['_id']['key']['category'], $percent);
        }

        return $data;
    }

    public function getConsole($offset, $limit, $query) {
        $coll = $this->wf_events_col_name;
        $event = $this->db->$coll;

        //$query = array('client_ip' => '192.168.200.241');
        if (!is_array($query) || empty($query)) {
            $count = $event->find()->count();
            $cursor = $event->find()->skip($offset)->limit($limit)->sort(array('timetime' => -1));
        } else {
            $count = $event->find($query)->count();
            $cursor = $event->find($query)->skip($offset)->limit($limit)->sort(array('timetime' => -1));
        }
        foreach ($cursor as $element) {
            //$data[]= array('id' => $element['_id'], 'cell' => array($element['timetime'],$element['client_ip'],$element['user_name'],$element['group_name'],$element['action'],$element['url']));
            $data[] = array('id' => $element['_id'], 'cell' => array(date('Y.m.d h:i:s', $element['timetime']->sec), $element['client_ip'], $element['user_name'], $element['groups'], $element['action'], $element['url']));
        }
        $table_array = array('rows' => $data, 'total' => $count);
        return $table_array;
    }

    public function getDLPEventApplications() {
        $coll = $this->dlp_events_col_name;
        $event = $this->db->$coll;
        return $event->distinct("app.description");
    }
    
    public function getDlpEventsHistogram($query, $limit) {

        $coll = $this->dlp_events_col_name;
        $event = $this->db->$coll;
        $cursor = $event->find($query)->sort(array('_id' => -1))->limit($limit);

        $data = array();
        $ids = array();
        foreach ($cursor as $element) {
            $ids[] = (string) $element['_id'];
            $date = (string) date('d-M-y', $element['timetime']->sec);
            if (isset($data[$date])) {
                $data[$date] += 1;
            } else {
                $data[$date] = 1;
            }
        }
        $_SESSION['QUERY_IDS'] = $ids;

        $ret_data = Array();
        foreach ($data as $mydate => $mycount)
            $ret_data[] = array($mydate, $mycount);

        return $ret_data;
    }

    public function getConsoleDLP($query, $offset, $limit) {

        $coll = $this->dlp_events_col_name;
        $event = $this->db->$coll;
        $count = $event->find($query)->count();
        $cursor = $event->find($query)->skip($offset)->limit($limit)->sort(array('_id' => -1));
        
        foreach ($cursor as $element) {
            $data[] = array(
                'id' => (string) $element['_id'],
                'cell' => array(
                    date('Y.m.d H:i:s', $element['timetime']->sec),
                    $element['user'],
                    $element['origin'],
                    $element['type'],
                    $element['identifier'],
                    $element['action'],
                    $element['severity'],
                )
            );
        }
        $table_array = array('rows' => $data, 'total' => $count);
        return $table_array;
    }
    
    public function getLastDLPEvents($criteria = null){
        $coll = $this->dlp_events_col_name;
        $event = $this->db->$coll;

        if(isset($criteria)){
            $cursor = $event->find($criteria)->sort(array('_id' => 1));
        } else {
            $cursor = $event->find()->sort(array('_id' => -1))->limit(1);
        }
        
        return $cursor;
    }


    public function getDlpBubbleActivity($period = 'G', $query = array()) {
        $policies_col_name = $this->dlp_events_col_name . '_' . $period . '_activity';

        $policies_collection = $this->db->$policies_col_name;
        $cursor = $policies_collection->find($query)->sort(array('count' => -1));

        $data = array();
        foreach ($cursor as $element) {
            $policy = $element['policy'];
            $action = $element['action'];
            $severity = $element['severity'];
            $count = $element['count'];
            $data[] = array($count, $action, $severity, $policy);
        }
        return $data;
    }

    public function getDlpTableActivity($period = 'G', $query = array()) {
        $policies_col_name = $this->dlp_events_col_name . '_' . $period . '_activity';

        $policies_collection = $this->db->$policies_col_name;
        $cursor = $policies_collection->find($query)->sort(array('count' => -1, 'policy' => -1, 'action_value' => -1, 'severity_value' => -1));

        $id = 0;
        foreach ($cursor as $element) {
            $policy = $element['policy'];
            $action = $element['action'];
            $severity = $element['severity'];
            $count = $element['count'];
            $data[] = array('id' => $id, 'cell' => array($policy, $action, $severity, $count));
            $id++;
        }
        $table_array = array('rows' => $data, 'page' => 1, 'total' => count($data));

        return $table_array;
    }

    public function getDLPPolicyGroupList($period = 'G', $query = array()){
        $policies_col_name = $this->dlp_events_col_name . '_' . $period . '_group_policy';
        $policies_collection = $this->db->$policies_col_name;
        return $policies_collection->distinct("policy", $query);
    }
    
    public function getDlpPolicyGroupBar($period = 'G', $query = array(), $limit = 10) {
        $policies_col_name = $this->dlp_events_col_name . '_' . $period . '_group_policy';
        $policies_collection = $this->db->$policies_col_name;
        $cursor = $policies_collection->find($query)->sort(array('count' => -1))->limit($limit);

        $data = array();
        foreach ($cursor as $element) {
            $policy = $element['group'];
            $percent = $element['count'];
            $data[] = array($policy, $percent);
        }

        return $data;
    }
    
    public function getDlpPolicyGroupTable($period = 'G', $query = array(), $limit = 10) {
        $policies_col_name = $this->dlp_events_col_name . '_' . $period . '_group_policy';
        $policies_collection = $this->db->$policies_col_name;
        $cursor = $policies_collection->find($query)->sort(array('count' => -1))->limit($limit);

        $id = 0;
        foreach ($cursor as $element) {
            $policy = $element['group'];
            $percent = $element['count'];
            $data[] = array('id' => $id, 'cell' => array($policy, $percent));
            $id++;
        }
        $table_array = array('rows' => $data, 'page' => 1, 'total' => count($data));

        return $table_array;
    }
    
    public function getDLPPolicyUserList($period = 'G', $query = array()){
        $policies_col_name = $this->dlp_events_col_name . '_' . $period . '_user_policy';
        $policies_collection = $this->db->$policies_col_name;
        return $policies_collection->distinct("policy", $query);
    }
    
    public function getDlpPolicyUserBar($period = 'G', $query = array(), $limit = 10) {
        $policies_col_name = $this->dlp_events_col_name . '_' . $period . '_user_policy';
        $policies_collection = $this->db->$policies_col_name;
        $cursor = $policies_collection->find($query)->sort(array('count' => -1))->limit($limit);

        $data = array();
        foreach ($cursor as $element) {
            $policy = $element['user'];
            $percent = $element['count'];
            $data[] = array($policy, $percent);
        }

        return $data;
    }
    
    public function getDlpPolicyUserTable($period = 'G', $query = array(), $limit = 10) {
        $policies_col_name = $this->dlp_events_col_name . '_' . $period . '_user_policy';
        $policies_collection = $this->db->$policies_col_name;
        $cursor = $policies_collection->find($query)->sort(array('count' => -1))->limit($limit);

        $id = 0;
        foreach ($cursor as $element) {
            $policy = $element['user'];
            $percent = $element['count'];
            $data[] = array('id' => $id, 'cell' => array($policy, $percent));
            $id++;
        }
        $table_array = array('rows' => $data, 'page' => 1, 'total' => count($data));

        return $table_array;
    }
    
    public function getDlpPie($field, $period = 'G', $query = array(), $limit = 10) {
        $policies_col_name = $this->dlp_events_col_name . '_' . $period . '_by_' . $field;
        $policies_collection = $this->db->$policies_col_name;
        $cursor = $policies_collection->find($query)->sort(array('count' => -1))->limit($limit);

        $data = array();
        foreach ($cursor as $element) {
            $policy = $element[$field];
            $percent = $element['count'];
            $data[] = array($policy, $percent);
        }

        return $data;
    }

    public function getDlpTable($field, $period = 'G', $query = array(), $limit = 10) {
        $policies_col_name = $this->dlp_events_col_name . '_' . $period . '_by_' . $field;
        $policies_collection = $this->db->$policies_col_name;
        $cursor = $policies_collection->find($query)->sort(array('count' => -1))->limit($limit);

        $id = 0;
        foreach ($cursor as $element) {
            $policy = $element[$field];
            $percent = $element['count'];
            $data[] = array('id' => $id, 'cell' => array($policy, $percent));
            $id++;
        }
        $table_array = array('rows' => $data, 'page' => 1, 'total' => count($data));

        return $table_array;
    }

    public function getDLPReport($query) {

        $coll = $this->dlp_events_col_name;
        $event = $this->db->$coll;
        $cursor = $event->find($query)->sort(array('_id' => -1));

        $data = array();
        $row_number = 2;
        foreach ($cursor as $element) {
            $row = array();
            $row['A' . $row_number] = date('Y.m.d h:i:s', $element['timetime']->sec);
            $row['B' . $row_number] = $element['ip'];
            $row['C' . $row_number] = implode(', ', $element['groups']);
            $row['D' . $row_number] = $element['user'];
            $row['E' . $row_number] = $element['origin'];
            $row['F' . $row_number] = $element['endpoint_module'];
            $row['G' . $row_number] = implode(', ', $element['policies_name']);
            $row['H' . $row_number] = $element['type'];
            switch ($element['type']) {
                case 'subconcept':
                    $row['I' . $row_number] = $element['concept_name'];
                    $row['J' . $row_number] = $element['identifier'];
                    break;
                case 'rule':
                    $row['K' . $row_number] = $element['identifier'];
                    break;
                case 'file':
                    $row['L' . $row_number] = $element['identifier'];
                    break;
            }
            $row['M' . $row_number] = $element['app'];
            $row['N' . $row_number] = $element['filename'];
            $row['O' . $row_number] = trim($element['match']);
            $row['P' . $row_number] = $element['action'];
            $row['Q' . $row_number] = $element['severity'];
            
            $data[] = $row;
            $row_number++;
        }

        return $data;
    }

    public function countDLPEvents() {
        $coll = $this->dlp_events_col_name;
        $dlp_event = $this->db->$coll;
        $count = $dlp_event->count();
        return $count;
    }

    public function getATPEventsHistogram($query, $limit) {

        $coll = $this->atp_events_col_name;
        $event = $this->db->$coll;
        $cursor = $event->find($query)->sort(array('_id' => -1))->limit($limit);

        $data = array();
        $ids = array();
        foreach ($cursor as $element) {
            $ids[] = (string) $element['_id'];
            $date = DateTime::createFromFormat('yy.m.d', $element['date']);
            $data[date('d-M-y', $date->getTimestamp())] += 1;
        }
        $_SESSION['QUERY_IDS'] = $ids;

        $ret_data = Array();
        foreach ($data as $mydate => $mycount)
            $ret_data[] = array($mydate, $mycount);

        return $ret_data;
    }

    public function getConsoleATP($query, $offset, $limit) {

        $coll = $this->atp_events_col_name;
        $event = $this->db->$coll;
        $count = $event->find($query)->count();
        $cursor = $event->find($query)->skip($offset)->limit($limit)->sort(array('_id' => -1));

        foreach ($cursor as $element) {
            $data[] = array(
                'id' => (string) $element['_id'],
                'cell' => array(
                    date('Y.m.d H:i:s', $element['timetime']->sec),
                    $element['user'],
                    $element['processname'],
                    $element['details'],
                    isset($element['startaddress']) ? $element['startaddress'] : '-',
                    isset($element['privateusage']) ? $element['privateusage'] . ' MB' : '-',
                    isset($element['string']) ? $element['string'] : '-',
                    isset($element['countnopmax']) ? $element['countnopmax'] : '-',
                    isset($element['operationlargestsled']) ? $element['operationlargestsled'] : '-',
                    isset($element['startnopsledmax']) ? $element['startnopsledmax'] : '-',
                )
            );
        }
        $table_array = array('rows' => $data, 'total' => $count);
        return $table_array;
    }

    public function getATPPieByApp($period = 'G', $query = array(), $limit = 10) {
        $apps_col_name = $this->atp_events_col_name . '_'. $period . '_by_app';
        $apps_collection = $this->db->$apps_col_name;
        $cursor = $apps_collection->find($query)->sort(array('count' => -1))->limit($limit);

        $data = array();
        foreach ($cursor as $element) {
            $app = isset($element['app']) ? $element['app'] : '-';
            $percent = $element['count'];
            $data[] = array($app, $percent);
        }

        return $data;
    }

    public function getATPTableByApp($period = 'G', $query = array(), $limit = 10) {
        $apps_col_name = $this->atp_events_col_name . '_'. $period . '_by_app';
        $apps_collection = $this->db->$apps_col_name;
        $cursor = $apps_collection->find($query)->sort(array('count' => -1))->limit($limit);

        $id = 0;
        foreach ($cursor as $element) {
            $app = isset($element['app']) ? $element['app'] : '-';
            $percent = $element['count'];
            $data[] = array('id' => $id, 'cell' => array($app, $percent));
            $id++;
        }
        $table_array = array('rows' => $data, 'page' => 1, 'total' => count($data));

        return $table_array;
    }

    public function getATPPieByGroup($period = 'G', $query = array(), $limit = 10) {
        $apps_col_name = $this->atp_events_col_name . '_'. $period . '_by_group';
        $apps_collection = $this->db->$apps_col_name;
        $cursor = $apps_collection->find($query)->sort(array('count' => -1))->limit($limit);

        $data = array();
        foreach ($cursor as $element) {
            $group = $element['group'];
            $percent = $element['count'];
            $data[] = array($group, $percent);
        }

        return $data;
    }

    public function getATPTableByGroup($period = 'G', $query = array(), $limit = 10) {
        $apps_col_name = $this->atp_events_col_name . '_'. $period . '_by_group';
        $apps_collection = $this->db->$apps_col_name;
        $cursor = $apps_collection->find($query)->sort(array('count' => -1))->limit($limit);

        $id = 0;
        foreach ($cursor as $element) {
            $group = $element['group'];
            $percent = $element['count'];
            $data[] = array('id' => $id, 'cell' => array($group, $percent));
            $id++;
        }
        $table_array = array('rows' => $data, 'page' => 1, 'total' => count($data));

        return $table_array;
    }

    public function countATPEvents() {
        $coll = $this->atp_events_col_name;
        $atp_event = $this->db->$coll;
        $count = $atp_event->count();
        return $count;
    }

    public function getForensicsReportList($limit, $query) {

        $forensics_model = new ForensicsModel();
        return $forensics_model->getRemoteQueryLists($query, $limit);
    }

    public function getForensicsQueryList($query_list_id) {

        $forensics_model = new ForensicsModel();
        return $forensics_model->getRemoteQueryList($query_list_id);
    }

    public function removeReport($report_id){
        $forensics_model = new ForensicsModel();
        $report = $forensics_model->getRemoteQueryList($report_id);
        foreach (array_keys($report['list']) as $report_item_id) {
            $forensics_model->removeObjectsResults($report_item_id, "search");
        }
        $forensics_model->removeRemoteQueryList($report_id);
    }


    public function getForensicsSearchReport($query_id) {
        $forensics_model = new ForensicsModel();
        $cursor = $forensics_model->getObjectsResults($query_id, "search");
        $data = null;
        if ($cursor->count() > 0) {
            $data = array();
            $row_number = 2;
            foreach ($cursor as $element) {
                foreach ($element['payload'] as $result) {
                    $row = array();
                    $row['A' . $row_number] = $element['datetime'];
                    $row['B' . $row_number] = $element['device'];
                    $row['C' . $row_number] = $element['ip'];
                    $row['D' . $row_number] = $result['path'];
                    $row['E' . $row_number] = $result['name'];
                    $row['F' . $row_number] = $result['modified'];
                    $row['G' . $row_number] = $result['type'];
                    $row['H' . $row_number] = $result['coincidence'];
                    $row['I' . $row_number] = $result['context'];
                    //$row['J' . $row_number] = $element['register_datetime'];
                    $data[] = $row;
                    $row_number++;
                }
            }
        }

        return $data;
    }

    public function getDailyDLPEvents(){
        return $this->col_dlp_events_daily->find()->sort(array('date' => 1));
    }
    
}

?>
