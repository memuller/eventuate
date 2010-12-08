<?php
	require_once('../../../wp-load.php') ;
	if( $_GET['id']) {
		$payment = new Payment($_GET['id']) ; 
		switch ( $payment->status )
			{
				case 0: #UNPAID
					$payment->send_request();
				break;

				case 1: #PAID
					_e("You have already paid your reservation.") ; 
				break;

				case -1: #FAILED
					$payment->send_request();
				break;
				
				default: #ERROR

				break;
			}
	} else {
		_e("This reservation link is invalid. Please use the payment link sent to you by e-mail.") ; 
	} 
?>