<?php


namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

use Zend\Mail\Message as MailMessages;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime as Mime_Mime;
use Zend\Mail\Address as EmailAddress;

//use Zend\Mime\Mime;


use Zend\Mail\Transport\Sendmail as SendmailTransport;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

use Zend\Mail\Transport\File as FileTransport;
use Zend\Mail\Transport\FileOptions;

class EmailService implements FactoryInterface{
    
    private $serviceLocator;
    private $transport;
    private $from_name;
    private $from_email;
    
    /**
     * Create service
     *  sample SMPT options
      array(
            'name'              => 'localhost.localdomain',
            'host'              => '127.0.0.1',
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' => 'user',
                'password' => 'pass',
            )
     * 
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->serviceLocator = $serviceLocator;  
        
        $config = $serviceLocator->get('Config');

        $emailConfig=$config['application']['email'];

        
        switch ($emailConfig['transport']['type']) {
            case 'sendmail':
                $this->transport = new SendmailTransport();
                break;
            case 'smtp':
                $this->transport = new SmtpTransport();
                $options   = new SmtpOptions($emailConfig['transport']['smtp_options']);
                $this->transport->setOptions($options);
                break;
            case 'file':
                $this->transport = new FileTransport();
                $options   = new FileOptions($emailConfig['transport']['file_options']);
                $this->transport->setOptions($options);
                break;
                
        }
        
        if(!$this->transport){
            throw new \Exception("Unable to create send emai using ".$emailConfig['transport']['type']);
        }
        
        if($emailConfig['from']){
            $this->from_name=$emailConfig['from']['name'];
            $this->from_email=$emailConfig['from']['email'];
        }
        return $this;
    }




    /**
     * Sends an email
     * @param $subject email subject
     * @param $to mixed (array or string) if array contains list of emails addresses to receive email
     * @param $template name of the template to use for email, it is expected to be under view/email dir
     * @param $param and array of variables to be passed to tempalte for rendering
     * @param $bothTextAndHtml indicates if email is to be sent as multipart with a text and a html part, 
     * if so the template name passed is assumed part of tempalte with _html and _text for each type of email
     * 
     */
    public function sendTemplatedEmail($subject,$to,$template,$param=array(),$bothTextAndHtml=false){
        $this->renderer = $this->serviceLocator->get('ViewRenderer');  
        
        $contentHtml=null;
        $contentText=null;
        
        if($bothTextAndHtml){
            $contentHtml = $this->renderer->render('email/'.$template.'_html', $param);          
            $contentText = $this->renderer->render('email/'.$template.'_text', $param);  
        }else{
            $contentHtml = $this->renderer->render('email/'.$template, $param);          
        }

        
        $body = new MimeMessage();  
        // add html  
        if($contentHtml){
            $html = new MimePart($contentHtml);  
            $html->type =  'text/html';//Mime::TYPE_HTML;
              
            $html->disposition = 'inline';//Mime::DISPOSITION_INLINE;
            $html->encoding = 'quoted-printable';//Mime::ENCODING_QUOTEDPRINTABLE;
            $html->charset = 'utf-8'; 
    
            $body->addPart($html);            
        }

        // if($contentText){
            // $text = new MimePart($contentText);  
            // $text->type = "text/txt";  
            // $body->addPart($text);            
        // }

        if(!is_array($to)){
            $to=array($to);
        }
        
        //We don't CC on mass emails
        $cc_email='';
        if(count($to)<2 && strlen($param['cc_email'])){
            $cc_email=trim($param['cc_email']);
        }
        foreach ($to as $to_address) {
            try{
                $emailAddress = new EmailAddress(trim($to_address));
                // instance mail   
                $mail = new MailMessages();  
                $mail->setFrom(trim($this->from_email),$this->from_name);  
                $mail->setSubject($subject);  
                $mail->setTo(trim($to_address));  
                if(strlen($cc_email)>1){
                    $mail->setCc($cc_email);
                }
                $mail->setBody($body); // will generate our code html from template.phtml  
                  
                $this->transport->send($mail);  
            }catch(\Exception $e){
                error_log("Invalid email detected [$to_address] : $e");
            }
        }
    }
    
    

    /**
     * Sends an email
     * @param $subject email subject
     * @param $to mixed (array or string) if array contains list of emails addresses to receive email
     * @param $messageHTML HTML message body
     * @param $messageText simple text message body
     * 
     */
    public function sendHTMLEmail($to,$subject,$messageText,$messageHTML,$attachments=null,$from){

        try{
        
            $emailAddress = new EmailAddress(trim($to));
        
        }catch(\Exception $e){
            return false;
        }
        
        $parts = array();
                        
    	// add html if content provided
        if($messageHTML){
            $html = new MimePart($messageHTML);  
            $html->type =  Mime_Mime::TYPE_HTML;
            $html->disposition = Mime_Mime::DISPOSITION_INLINE;
            $html->encoding = Mime_Mime::ENCODING_QUOTEDPRINTABLE;
            $html->charset = 'utf-8'; 
    
            $parts[]=($html);            
        }

        if($messageText && !empty($messageText) && $messageText!=''){
            $text = new MimePart($messageText);  
            $text->type = Mime_Mime::TYPE_TEXT; 
            $text->disposition = Mime_Mime::DISPOSITION_INLINE;             
            $parts[]=($text);            
        }
        
	$acount=0;
        if($attachments && is_array($attachments)){

            $docUtil = $this->serviceLocator->get('DocUtil');
            
            foreach ($attachments as $file) {
                //attach file if no error
		if($file['status'] == 'error'){
		 error_log('skipping file attachment'); continue;
		}
                //decrypt source file and save to tmp file
                $temp_decrypted_file = tempnam(sys_get_temp_dir(),'enc');
                
                $success = $docUtil->cryptFileChunks($docUtil->getFullPath($file['saved_as'],$docUtil::UPLOAD_TYPE_ATTACHMENT), $temp_decrypted_file,'decrypt');
                
                if(!$success){
                    throw new \Exception('Unabel to decrypt document');
                }
                 
                //register on shutdown to delete decrypted file once download is done.
                register_shutdown_function('unlink',$temp_decrypted_file);
        
                
                $fileContent = fopen($temp_decrypted_file, 'r');
                $attachment = new MimePart($fileContent);
                $attachment->type = $file['type'];
                $attachment->filename = $file['name'];
                $attachment->disposition = Mime_Mime::DISPOSITION_ATTACHMENT;
                // Setting the encoding is recommended for binary data
                $attachment->encoding = Mime_Mime::ENCODING_BASE64;
                $parts[]=($attachment);
		$acount++;
            }
        }
error_log("acount is $acount\n");
        if(!is_array($to)){
            $to=array($to);
        }

        $body = new MimeMessage();  
        $body->setParts($parts);
                
        foreach ($to as $key => $to_address) {
            // instance mail   
            $mail = new MailMessages();  
            
            //if from is defined cahnge name for the from
            //but keep reporting system from address
            //set reply-to header to from
            $reply_to_email='';
            $reply_to_name='';
            if($from && !empty($from) && is_string($from)){
                //use default from
                $reply_to_email=$from;
            }elseif ($from && !empty($from) && is_array($from) && key_exists('email', $from)) {
                $reply_to_email=$from['email'];
                $reply_to_name=$from['name']; 
            }

            //sanitize reply addresses
            $reply_to_name = str_replace("?","'",mb_convert_encoding($reply_to_name,"ASCII"));

            if($reply_to_email){
                $mail->setReplyTo($reply_to_email,$reply_to_name);
            }
            $from_name=$this->from_name;
            if($reply_to_name){
                $from_name= "$reply_to_name via ".$this->from_name;
            }

            //sanitize from and to email addresses
            $from_name = str_replace("?","'",mb_convert_encoding($from_name,"ASCII"));

            //use default from
            $mail->setFrom(trim($this->from_email),$from_name);                                  
            
            $mail->setSubject($subject);  
            $mail->setTo(trim($to_address));  

            //$mail->setEncoding("UTF-8");
            $mail->setBody($body);// will generate our code html from template.phtml
            //$mail->getHeaders()->get('content-type')->setType('multipart/alternative');
            
error_log('Ready to send email no error so far ...'. $to_address);            
            $this->transport->send($mail); 
error_log(' ... done sending to '. $to_address); 
        }
error_log('Done sending all emails no error so far');            
        return true;
    }
    
}
