<?php
/**
 * phpmailer support
 *
 */
class EM_Mailer {
	
	/**
	 * if any errors crop up, here they are
	 * @var array
	 */
	var $errors = array();
	
	/**
	 * @param $subject
	 * @param $body
	 * @param $receiver
	 */
	function send($subject="no title",$body="No message specified", $receiver='') {
		//TODO add an EM_Error global object, for this sort of error reporting. (@marcus like StatusNotice)
		global $smtpsettings, $phpmailer, $cformsSettings;
	
		if( preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3}$/i', $receiver) ){
			$this->load_phpmailer();
			$mail = new EM_PHPMailer();
			//$mail->SMTPDebug = true; 
			$mail->ClearAllRecipients();
			$mail->ClearAddresses();
			$mail->ClearAttachments();
			$mail->CharSet = 'utf-8';
		    $mail->SetLanguage('en', dirname(__FILE__).'/');
			$mail->PluginDir = dirname(__FILE__).'/phpmailer/';
			$mail->Host = get_option('dbem_smtp_host');
			$mail->port = get_option('dbem_rsvp_mail_port');
			$mail->Username = get_option('dbem_smtp_username');  
			$mail->Password = get_option('dbem_smtp_password');  
			$mail->From = get_option('dbem_mail_sender_address');
		
			//Protocols
		 	if( get_option('dbem_rsvp_mail_send_method') == 'qmail' ){       
				$mail->IsQmail(); 
			} else {
				$mail->Mailer = get_option('dbem_rsvp_mail_send_method');	
			}                     
			if(get_option('dbem_rsvp_mail_SMTPAuth') == '1'){
				$mail->SMTPAuth = TRUE;
		 	}       
		
			$mail->FromName = get_option('dbem_mail_sender_name'); // This is the from name in the email, you can put anything you like here
			$mail->Body = $body;
			$mail->Subject = $subject;  
			$mail->AddAddress($receiver);  
		
			if(!$mail->Send()){   
				$this->errors[] = $mail->ErrorInfo;
				return false;
			}else{
				return true;
			}
		}else{
			$this->errors = __('Please supply a valid email format.', 'dbem');
			return false;
		}
	}
	
	/**
	 * load phpmailer classes
	 */
	function load_phpmailer(){
		require_once(dirname(__FILE__) . '/phpmailer/class.phpmailer.php');
		require_once(dirname(__FILE__) . '/phpmailer/class.smtp.php');
	}
}
?>