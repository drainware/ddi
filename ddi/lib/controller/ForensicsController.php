<?php

class ForensicsController extends Controller {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    /*
     * Depending on the passed action it runs a method.
     * It must to recieve the showAction method at least.
     */

    protected function showAction() {
        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUser();
        
        $options = array(
            "account_type" => $client['type']
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showMultipleRemoteSearchAction() {
        $options = array(
            "hostname" => $_SERVER['SERVER_NAME']
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showRemoteFileExplorerAction() {
        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUser();
        
        $options = array(
            "account_type" => $client['type'],
            'channel' => $_GET['device'] . '_' . $_GET['ip'],
            'device' => $_GET['device'],
            'ip' => $_GET['ip'],
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showRemoteDevicesAction() {
        
        $remote_devices_id = (string)new MongoID();

        $channel = '*';

        $amqp_model = new AMQPModel();
        $amqp_model->setExchange('server');
        $amqp_model->setRoutingKey($channel);
        $amqp_model->setMessageId($remote_devices_id);
        $amqp_model->setMessageModule('forensics');
        $amqp_model->setMessageCommand("geodata");
        $amqp_model->setMessageArgs("U2VhcmNoIGNvbm5lY3RlZCBkZXZpY2Vz");
        $amqp_model->sendMessage();

        $options = array(
            'remote_devices_id' => $remote_devices_id,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function remoteQueryAction() {
	//syslog(LOG_DEBUG, "remoteQueryAction()");

        $remote_query_id = (string)new MongoID();

        $channel = $_POST['channel'];

        $message_arg = $_POST['args'];
        
        if($_POST['command'] == 'search'){
                        
            if (isset($_POST['type']) && $_POST['type'] != 'all') {
                $message_arg = 'kind:=' . $_POST['type'] . ' ' . $message_arg;
            }
            
            $fields = array('name', 'path', 'url', 'size', 'modified', 'type', 'itype', 'context', 'title');
            $query = 'SELECT TOP 500 "System.FileName", "System.ItemFolderPathDisplay", "System.ItemUrl", "System.Size", "System.DateModified", "System.MIMEType", "System.ItemType", "System.Search.AutoSummary", "System.Title" FROM "SystemIndex" WHERE CONTAINS(*,\'"' . $message_arg . '*"\',0)';        
            
            $message = array();
            $message['keyword'] = $_POST['args'];
            $message['fields'] = $fields;
            $message['query'] = $query;

            $message_arg = $message;
            
        }
	//syslog(LOG_DEBUG,"Command = " . $_POST['command']);
        
        $amqp_model = new AMQPModel();
        $amqp_model->setExchange('server');
        $amqp_model->setRoutingKey($channel);
        $amqp_model->setMessageId($remote_query_id);
        $amqp_model->setMessageModule('forensics');
        $amqp_model->setMessageCommand($_POST['command']);
        $amqp_model->setMessageArgs($message_arg);
        $amqp_model->sendMessage();

        $json_object = json_encode(
                array(
                    'id' => $remote_query_id,
                    'args' => $_POST['args'],
                )
        );

        $options = array(
            "json_object" => $json_object
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function remoteQueryListAction() {
        if ($_FILES['list']['name'] != "") {
            $extension = end(explode('.', $_FILES['list']['name']));
            if ($extension == "txt") {
                $target_path = $_FILES['list']['tmp_name'];
                if (file_exists($target_path)) {

                    $channel = '*';

                    $remote_query_model = new ForensicsModel();
                    $csv_file = fopen($target_path, 'r');
                    $list = array();
                    while (($line = fgetcsv($csv_file)) !== FALSE) {
                        $remote_query_id = (string)new MongoID();;
                        foreach ($line as $element) {

                            
                            $fields = array('name', 'path', 'url', 'size', 'modified', 'type', 'itype', 'context', 'title');
                            $query = 'SELECT TOP 500 "System.FileName", "System.ItemFolderPathDisplay", "System.ItemUrl", "System.Size", "System.DateModified", "System.MIMEType", "System.ItemType", "System.Search.AutoSummary", "System.Title" FROM "SystemIndex" WHERE CONTAINS(*,\'"' . $message_arg . '*"\',0)';        

                            $message = array();
                            $message['keyword'] = $element;
                            $message['fields'] = $fields;
                            $message['query'] = $query;
                            
                            $amqp_model = new AMQPModel();
                            $amqp_model->setExchange("server");
                            $amqp_model->setRoutingKey($channel);
                            $amqp_model->setMessageId($remote_query_id);
                            $amqp_model->setMessageModule('forensics');
                            $amqp_model->setMessageCommand("search");
                            $amqp_model->setMessageArgs($message);
                            $amqp_model->sendMessage();
                        }
                        $list[$remote_query_id] = $line;
                    }
                    fclose($csv_file);


                    $remote_query_model->newRemoteQueryList($_POST['name'], $list);
                    $msg = "It has successfully launched the search, the results can be displayed in the reports section";
                } else {
                    $msg = "There was an error uploading the file, please try again!";
                }
            } else {
                $msg = "You can only upload files with txt extension";
            }
        }

        $options = array(
            'msg' => $msg,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function getLastSearchResultsAction() {

        $remote_query_model = new ForensicsModel();
        $results = $remote_query_model->getLastSearchResults($_POST['id'], $_POST['last_id']);

        $search_results = array();

        foreach ($results as $result_id => $result_object) {
            if (empty($result_object['payload'])) {
                $result_object['payload'] = null;
            } else {
                foreach ($result_object['payload'] as $index => $value) {
                    $url_id = preg_replace('/[^\w]/', '_', $value['url']);
                    $url_path = preg_replace('/file\:/', '', $value['url']);
                    $result_object['payload'][$index]['url'] = preg_replace('/\//', '\\', $url_path);
                    $result_object['payload'][$index]['file_id'] =  $result_id . '_' . $url_id;
                }
            }

            $search_results[$result_id] = $result_object;
        };


        $filter = new WordFilterModel();
        $filter_options = array("where_apply" => "context");

        array_walk_recursive($search_results, array(&$filter, 'configureDescription'), $filter_options);

        $json_search_results = json_encode($search_results);

        $options = array(
            "json_search_results" => $json_search_results,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function getLastListResultAction() {
        $remote_query_model = new ForensicsModel();
        $list_result = $remote_query_model->getLastObjectResult($_POST['id']);

        $json_list_result = json_encode($list_result);

        $options = array(
            "json_list_result" => $json_list_result,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function getLastGetResultAction() {

        $remote_query_model = new ForensicsModel();
        $get_result = $remote_query_model->getLastObjectResult($_POST['id']);

        if ($get_result != null) {
            $message = "success";
        }

        $options = array(
            "json_get_result" => $message
        );

        $this->options = array_merge($this->options, $options);
    }

    public function getRemoteDevicesAction() {
        $remote_devices_id = $_GET['id'];

        $remote_query_model = new ForensicsModel();
        $devices_cursor = $remote_query_model->getObjectsResults($remote_devices_id, "geodata");

        $devices_array = array();
        $devices_array['name'] = ""; //"Remote devices";
        foreach ($devices_cursor as $device) {
            $device_id = $device['device'] . '_' . $device['ip'];
            $device_network = explode('.', $device['ip']);

            $devices_array['children'][$device_network[0]]['name'] = ""; //$device_network[0];
            $devices_array['children'][$device_network[0]]['children'][$device_network[1]]['name'] = ""; //$device_network[1];
            $devices_array['children'][$device_network[0]]['children'][$device_network[1]]['children'][$device_network[2]]['name'] = ""; //$device_network[2];
            $devices_array['children'][$device_network[0]]['children'][$device_network[1]]['children'][$device_network[2]]['children'][$device_network[3]]['name'] = $device_id;
            $devices_array['children'][$device_network[0]]['children'][$device_network[1]]['children'][$device_network[2]]['children'][$device_network[3]]['size'] = 50;
        }

        $remote_devices = array();
        $remote_devices['name'] = ""; //"Remote devices";
        foreach ($devices_array['children'] as $device_c) {
            $layout = array();
            $layout['name'] = $device_c['name'];
            foreach ($device_c['children'] as $device_cc) {
                $layout2 = array();
                $layout2['name'] = $device_cc['name'];
                foreach ($device_cc['children'] as $device_ccc) {
                    $layout3 = array();
                    $layout3['name'] = $device_ccc['name'];
                    foreach ($device_ccc['children'] as $device_cccc) {
                        $layout3['children'][] = $device_cccc;
                    }
                    $layout2['children'][] = $layout3;
                }
                $layout['children'][] = $layout2;
            }
            $remote_devices['children'][] = $layout;
        }
        $remote_devices['total'] = $devices_cursor->count();

        header("Content-type: application/json");
        echo json_encode($remote_devices);
    }

    public function getRemoteDevicesMarkerAction() {
        $remote_devices_id = $_GET['id'];

        $remote_query_model = new ForensicsModel();
        $devices_cursor = $remote_query_model->getObjectsResults($remote_devices_id, "geodata");

        $devices_marker = array();
        $nro_markers = 0;
        $nro_no_markers = 0;
        foreach ($devices_cursor as $device_id => $device_object) {
            if (isset($device_object['geodata'])) {
                $device_marker = array();
                $device_marker['id'] = $device_id;
                $device_marker['device'] = $device_object['device'];
                $device_marker['ip'] = $device_object['ip'];
                $device_marker['lat'] = $device_object['geodata']['location']['lat'];
                $device_marker['lng'] = $device_object['geodata']['location']['lng'];
                $device_marker['accuracy'] = $device_object['geodata']['accuracy'];
                $devices_marker[$device_id] = $device_marker;
                $nro_markers++;
            } else {
                $nro_no_markers++;
            }
        }

        $devices = array();
        $devices['markers'] = $devices_marker;
        $devices['nro_markers'] = $nro_markers;
        $devices['nro_no_markers'] = $nro_no_markers;
        $devices['total'] = $devices_cursor->count();

        $options = array(
            "remote_devices_marker" => json_encode($devices)
        );

        $this->options = array_merge($this->options, $options);
    }

    public function viewRemoteFileAction() {
        
    }

    public function downloadRemoteFileAction() {

        $forensics_model = new ForensicsModel();
        $get_result = $forensics_model->getLastObjectResult($_GET['id']);

        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=' . $get_result['payload']['filename']);

        $contents = $forensics_model->getRemoteFile($get_result['payload']['contents']);

        file_put_contents('php://output', $contents);
        
    }

}

?>
