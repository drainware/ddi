<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AMQPModel
 *
 * @author csandoval
 */
class AMQPModel {
    //put your code here

    private $amqpOptions;
    private $amqpMessageDelay;

    public function __construct() {

        $this->amqpOptions = array();
        $this->amqpOptions['host'] = $GLOBALS['conf']['ddi']['configuration']['amqp']['host']['value'];
        $this->amqpOptions['port'] = $GLOBALS['conf']['ddi']['configuration']['amqp']['port']['value'];
        $this->amqpOptions['exchange'] = "";
        $this->amqpOptions['routing_key'] = "";
        $this->amqpOptions['message']['id'] = "";
        $this->amqpOptions['message']['module'] = "";
        $this->amqpOptions['message']['command'] = "";
        $this->amqpOptions['message']['args'] = "";
        $this->amqpMessageDelay = 0;
    }

    public function setExchange($exchange){
        $this->amqpOptions['exchange'] = $exchange;
    }

    public function setRoutingKey($routing_key, $license = null){
        if(!isset($license)){
            $license = $_SESSION['license'];
        }
        if (isset($license)){
            switch ($routing_key) {
                case '*':
                    $routing_key = $license;
                    break;
                default:
                    $routing_key = $license . '_' . $routing_key;
                    break;
            }
        }
        $this->amqpOptions['routing_key'] = $routing_key;
    }

    public function setMessageId($message_id){
        $this->amqpOptions['message']['id'] = $message_id;
    }

    public function setMessageModule($message_module){
        $this->amqpOptions['message']['module'] = $message_module;
    }

    public function setMessageCommand($message_command){
        $this->amqpOptions['message']['command'] = $message_command;
    }

    public function setMessageArgs($message_args){
        $this->amqpOptions['message']['args'] = $message_args;
    }

    public function setMessageDelay($delay){
        if(is_integer($delay)){
            $this->amqpMessageDelay = $delay;
        }
    }

    private function newMessage(){
        $this->amqpOptions['message'] = array();
        $this->amqpOptions['message']['id'] = "";
        $this->amqpOptions['message']['module'] = "";
        $this->amqpOptions['message']['command'] = "";
        $this->amqpOptions['message']['args'] = "";
    }

    public function sendMessage(){
        syslog(LOG_DEBUG, "##########################################");
        syslog(LOG_DEBUG, "Routing Key: " . $this->amqpOptions['routing_key']);
        syslog(LOG_DEBUG, "Message: " . json_encode($this->amqpOptions['message']));
        syslog(LOG_DEBUG, "##########################################");

        $this->amqpOptions['message'] = base64_encode(json_encode($this->amqpOptions['message']));
        $amqp_options = json_encode($this->amqpOptions);

        if ($this->amqpMessageDelay == 0){
            $command = "/opt/drainware/scripts/ddi/amqp_driver.py '" . $amqp_options . "' > /dev/null &";
        } else {
            $command = "(sleep " . $this->amqpMessageDelay . "; /opt/drainware/scripts/ddi/amqp_driver.py '" . $amqp_options . "') > /dev/null &";
        }

        $file_path = "/tmp/debug.txt";
        file_put_contents($file_path,"$command\n", FILE_APPEND|LOCK_EX);
        file_put_contents($file_path,"\n--\n", FILE_APPEND|LOCK_EX);
        system($command);
        $this->newMessage();

    }

}

?>
