<?php

	require_once('../../../wp-load.php') ;
	define('TOKEN', get_option('dbem_payments_token')) ;
	require_once('payments_callback.php') ;
	
	function retorno_automatico ( $VendedorEmail, $TransacaoID, $Referencia, $TipoFrete, $ValorFrete, $Anotacao, $DataTransacao, $TipoPagamento, $StatusTransacao, $CliNome, $CliEmail, $CliEndereco, $CliNumero, $CliComplemento, $CliBairro, $CliCidade, $CliEstado, $CliCEP, $CliTelefone, $produtos, $NumItens) {
		$payment = new Payment($Referencia) ; 
		$payment->update($TipoPagamento , $StatusTransacao) ;
	}
	
	
	if( $_GET['id']) {
		$payment = new Payment($_GET['id']) ; 
		switch ( $payment->status )
			{
				case Payment::unpaid : 
					$payment->send_request();
				break;

				case Payment::paid : 
					_e("You have already paid your reservation.") ; 
				break;
				
				case Payment::validating :
					_e("Your payment is currently being validated. During this time, you may receive mail and phone calls from PagSeguro or your credit card operator, so pay attention.") ; 
				break ; 
				
				case Payment::failed : 
					$payment->send_request();
				break;
				
				default: #ERROR
					_e("This reservation link is invalid. Please use the payment link sent to you by e-mail.") ;
				break;
			}
	} else {
		_e("Thank you for your payment. We will contact you as soon as it is processed.") ;
		 
	} 
?>