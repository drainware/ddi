<?php

class MailModel extends Model {

    private $mailer;
    private $mail_from;
    private $dest;
    private $subject;
    private $smarty;
    private $template;	  
    private $vars;
    private $body_html;
    private $body_plain;

    public function __construct() {
        //Get configuration
        $server = $GLOBALS['conf']['ddi']['configuration']['mail']['server']['value'];
        $port = $GLOBALS['conf']['ddi']['configuration']['mail']['port']['value'];
        $user = $GLOBALS['conf']['ddi']['configuration']['mail']['username']['value'];
        $pass = $GLOBALS['conf']['ddi']['configuration']['mail']['password']['value'];
        $this->mail_from = $GLOBALS['conf']['ddi']['configuration']['mail']['e-mail']['value'];
        $ssl = $GLOBALS['conf']['ddi']['configuration']['mail']['ssl']['value'];
        $this->smarty = new Smarty();

        if ($ssl) {
            $transport = Swift_SmtpTransport::newInstance($server, $port, "ssl")->setUsername($user)->setPassword($pass);
        } else {
            $transport = Swift_SmtpTransport::newInstance($server, $port)->setUsername($user)->setPassword($pass);
        }

        $this->mailer = Swift_Mailer::newInstance($transport);
    }
    public function setSubject($subject){
        $this->subject = $subject;
    }

    public function setDest($dest){
        $this->dest = $dest;
    }
    
    public function setVars($vars){
        $this->vars = $vars;
    }

    public function setTemplate($template){
        $this->template = $template;
    }


    public function get_html()
    {
        return $this->body_html;
    }


    public function get_plain()
    {
        return $this->body_plain;
    }


    public function sendMail() {

	foreach($this->vars as $key=>$value){

	    if($key == 'mode' || $key == 'template')
	      throw new Exception($key . ' cannot be used, please use other name');

            $this->smarty->assign($key, $value);
        }

	$this->smarty->assign('mode', 'html'); 
	$this->smarty->assign('translations', $GLOBALS['lang']);
	$this->smarty->assign('template', $this->template); 
        $this->body_html = $this->smarty->fetch('mail/base.tpl');

	// Clear mode 
	//$this->smarty->clear_assign('mode');
        $this->smarty->assign('mode', 'plain');
        $this->smarty->assign('template', $this->template);
        $this->body_plain = $this->smarty->fetch('mail/base.tpl');

        $message = Swift_Message::newInstance($this->subject)
                ->setFrom(array($this->mail_from => 'Drainware'))
                ->setTo(array($this->dest => $dest))
		->setBody($this->body_html, 'text/html')
		->addPart($this->body_plain, 'text/plain');

        $numSent = $this->mailer->send($message);
        return $numSent;
    }

}

?>
