<?php
	
	class Payment {
		
		const unpaid = 0 ;
		const paid = 1 ;
		const failed = -2 ;
		const validating = -1 ;
		const complete = 2 ;
		
		var $booking ;
		var $person ; 
		var $event ;
		var $cost ;
		var $status ; 
		var $uid ; 
		function Payment($booking_id) {
			if ( substr_count($booking_id, '-') == 1 )
			{
				$this->uid = $booking_id ; 
				$booking_id = explode("-", $booking_id) ; 
				$booking_id = $booking_id[1] ; 
			}
			$this->booking = new EM_Booking($booking_id) ;
			$this->person = new EM_Person($this->booking->person_id) ;
			$this->event = new EM_Event($this->booking->event_id) ; 
			$this->cost = $this->event->cost ; 
			$this->status = $this->booking->payment_status ;
			
			if( ! $this->uid ){
				$this->uid = $this->event->id . "-" . $this->booking->id ;
			}
		}
		
		function invite_link() {
                  require( WP_PLUGIN_DIR . "/eventuate/views/booking_invite.php") ;
		}

                function invite_link_form_fields(){
                  $data = $this->prepare_data() ; 
                  foreach( $data as $k => $v ) {
                    echo "<input type='hidden' name='$k' value='$v' />" ; 
                  }
                }
		
		function prepare_data(){
			$fields = array(
				'cliente_nome' => $this->person->name,
				'cliente_cep' => $this->person->zip,
				'cliente_end' => $this->person->address,
				'cliente_num' => $this->person->num,
				'cliente_compl' => $this->person->compl,
				'cliente_bairro' => $this->person->district,
				'cliente_cidade' => $this->person->city,
				'cliente_uf' => $this->person->uf,
				'cliente_pais' => 'BRA',
				'cliente_ddd' => $this->person->ddd,
				'cliente_tel' => $this->person->phone,
				'cliente_email' => $this->person->email,
				'email_cobranca' => get_option('dbem_payments_email'),
				'tipo' => 'CP',
				'moeda' => 'BRL',
				'item_id_1' => $this->uid,
				'item_descr_1' => $this->event->name,
				'item_valor_1' => $this->cost * 100,
				'item_quant_1' => 1,
				'item_frete_1' => 0,
				'ref_transacao' => $this->uid
			) ;
			return $fields ; 
		}
		
		function send_request(){
			$data = $this->prepare_data() ;
			
			$data = array();
			foreach( $fields as $k => $v )
			{
				$v = urlencode(stripslashes($v)) ;
				$data[]= "{$k}={$v}" ;
			}
			$data = implode('&', $data) ;
			$url = "https://pagseguro.uol.com.br/checkout/checkout.jhtml?{$data}" ;
			header("Location: $url") ;
		}
		
		
		function update($new_type, $new_status){
			switch ( $new_status )
			{
				case 'Completo':
					$this->booking->payment_status = Payment::complete ; 
					$this->booking->save();
				break ; 
				
				case 'Aprovado':
					$this->booking->payment_status = Payment::paid ;
					$this->booking->save() ;
				break;
				
				case 'Aguardando' :
				case 'Em Análise' :
					$this->booking->payment_status = Payment::validating ;
					$this->booking->save() ; 
				break ;
				
				case 'Cancelado' :
					$this->booking->payment_status = Payment::error ;
					$this->booking->save() ;
				break ;
						
				default:
					$this->booking->payment_status = Payment::unpaid ;
					$this->booking->save() ; 
				break;
			}
		}
	
	}
	?>
