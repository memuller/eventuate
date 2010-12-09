<?php
	require_once('../../../wp-load.php') ;
	require_once('payments_callback.php') ;
	define('TOKEN', get_option('dbem_payments_token')) ;
	
	function retorno_automatico ( $VendedorEmail, $TransacaoID, $Referencia, $TipoFrete, $ValorFrete, $Anotacao, $DataTransacao, $TipoPagamento, $StatusTransacao, $CliNome, $CliEmail, $CliEndereco, $CliNumero, $CliComplemento, $CliBairro, $CliCidade, $CliEstado, $CliCEP, $CliTelefone, $produtos, $NumItens) {
		
	}


	
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