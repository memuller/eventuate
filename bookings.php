<?php
/* Contains functions to handle save and delete requests on the front-end for bookings */
global $dbem_form_messages_booking_delete;
$dbem_form_messages_booking_delete = array();
global $dbem_form_messages_booking_add;
$dbem_form_messages_booking_add = array();

/**
 * Check if there's any actions to take for bookings
 * @return null
 */
function em_actions_bookings() {
	global $dbem_form_messages_booking_delete, $dbem_form_messages_booking_add;
	global $wpdb;
	global $EM_Event;
	
	if( @get_class($EM_Event) == 'EM_Event' ){
		//ADD/EDIT Booking
		if (isset($_POST['eventAction']) && $_POST['eventAction'] == 'add_booking') {
			//$EM_Event->get_bookings();
			if( $EM_Event->get_bookings()->add( new EM_Booking($_POST) ) ){
				$dbem_form_messages_booking_add['success'] = $EM_Event->get_bookings()->feedback_message;
			}else{
				$dbem_form_messages_booking_add['error'] = implode('<br />', $EM_Event->get_bookings()->errors);
			}		
	  	}
	  	//DELETE Booking
		if (isset($_POST['eventAction']) && $_POST['eventAction'] == 'delete_booking') {
			$EM_Person = new EM_Person();
			if( $EM_Person->get(array('person_name' => $_POST['person_name'], 'person_email' => $_POST['person_email'])) ){
				$deleted = 0;
				foreach($EM_Event->get_bookings()->bookings as $EM_Booking){
					if($EM_Booking->person->id == $EM_Person->id ){
						$EM_Booking->delete();
						$deleted++;
					}
				}
			}
			if($deleted > 0){
				$dbem_form_messages_booking_delete['success'] = __('Booking deleted', 'dbem');
			}else{
				$dbem_form_messages_booking_delete['error'] = __('There are no bookings associated to this name and e-mail', 'dbem');
			}
	  	}
	}
}   
add_action('init','em_actions_bookings');

/**
 * Returns the booking form for the front-end, displayed when using placeholder #_ADDBOOKINGFORM
 * @return string
 */
function em_add_booking_form() {                
	global $dbem_form_messages_booking_add, $EM_Event;
	$destination = "?".$_SERVER['QUERY_STRING']."#dbem-rsvp";
	ob_start();
	?>
	<div id="dbem-rsvp">
		<a name="dbem-rsvp"></a>
		<h3><?php _e('Book now!','dbem') ?></h3>
		
		<?php if( !empty($dbem_form_messages_booking_add['success']) ) : ?>
		<div class='dbem-rsvp-message-success'><?php echo $dbem_form_messages_booking_add['success'] ?></div>
		<?php elseif( !empty($dbem_form_messages_booking_add['error']) ) : ?>
		<div class='dbem-rsvp-message-error'><?php echo $dbem_form_messages_booking_add['error'] ?></div>
		<?php elseif( !empty($dbem_form_messages_booking_add['message']) ) : ?>
		<div class='dbem-rsvp-message'><?php echo $dbem_form_messages_booking_add['message'] ?></div>
		<?php endif; ?>
		
		<form id='dbem-rsvp-form' name='booking-form' method='post' action='<?php echo $destination ?>'>
				<table class='dbem-rsvp-form'>
					<?php #Nome completo ?>
					<tr><th scope='row'><?php _e('Name', 'dbem') ?>:</th>
						<td><input type='text' name='person_name' value='<?php echo $_POST['person_name'] ?>'/></td>
					</tr>
					<?php #Email, validado via JS. ?>
					<tr><th scope='row'><?php _e('E-Mail', 'dbem') ?>:</th>
						<td><input type='text' name='person_email' value='<?php echo $_POST['person_email'] ?>'/></td>
					</tr>
					<?php /*	Daqui em diante, s�o os campos do endere�o do cliente.
								Estes devem ser separados em v�rios outros campos, como mostrado abaixo, ou o PagSeguro
								recusar� o pagamento imediatamente.
								Recomendo manifestar de alguma forma que o endere�o deve ser igual ao do recebimento
								da fatura de cart�o de cr�dito (se for usado), do contr�rio o PagSeguro pode recusar o
								pagamento quando for conferir os dados (o que � muito inconveniente para n�s, pois at�
								isso acontecer, o cliente ficar� registrado como pagante */ ?>
					<?php #Rua. SOMENTE A RUA. ?>
					<tr><th scope='row'><?php _e('Address', 'dbem') ?>:</th>
						<td><input type='text' name='person_address' value='<?php echo $_POST['person_address'] ?>'/></td>
					</tr>
					<?php #N�mero do im�vel. SOMENTE O N�MERO. SOMENTE ALGARISMOS NUM�RICOS.  ?>
					<tr><th scope='row'><?php _e('Number', 'dbem') ?>:</th>
						<td><input type='text' name='person_num' value='<?php echo $_POST['person_num'] ?>'/></td>
					</tr>
					<?php #Complemento (apto, bloco, etc). Pode ser vazio. ?>
					<tr><th scope='row'><?php _e('Complement', 'dbem') ?>:</th>
						<td><input type='text' name='person_compl' value='<?php echo $_POST['person_compl'] ?>'/></td>
					</tr>
					<?php #Bairro. ?>
					<tr><th scope='row'><?php _e('District', 'dbem') ?>:</th>
						<td><input type='text' name='person_district' value='<?php echo $_POST['person_district'] ?>'/></td>
					</tr>
					<?php #Cidade, por extenso. N�o abrevie. ?>
					<tr><th scope='row'><?php _e('City', 'dbem') ?>:</th>
						<td><input type='text' name='person_city' value='<?php echo $_POST['person_city'] ?>'/></td>
					</tr>
					<?php #Sigla de duas letras do estado. Irei trocar por um menu de sele��o.
						# SOMENTE DOIS CARACTERES. SOMENTE UM ESTADO BRASILEIRO V�LIDO, ou distrito federal (DF) ?>
					<tr><th scope='row'><?php _e('UF', 'dbem') ?>:</th>
						<td><input type='text' name='person_uf' value='<?php echo $_POST['person_uf'] ?>' size='2'/></td>
					</tr>
					<?php #Cep. SOMENTE N�MEROS. SOMENTE UM CEP V�LIDO. Recomendo m�scara. Posso remover h�phens/etc se necess�rio. ?>
					<tr><th scope='row'><?php _e('Zip', 'dbem') ?>:</th> 
						<td><input type='text' name='person_zip' value='<?php echo $_POST['person_zip'] ?>'/></td>
					</tr>
					<?php #Dois campos de n�mero de telefone. Se for adequado, podem ser unidos, mas ent�o sera necess�rio o uso de m�scaras
						# para deixar claro aonde um acaba e o outro come�a (ex. for�ar ddd/telefone separados por um h�phen) ?>
					<tr><th scope='row'><?php _e('Phone number', 'dbem') ?>:</th>
						<?php #DDD. SOMENTE N�MEROS. SOMENTE DOIS CARACTERES. ?>
						<td><input type='text' name='person_ddd' value='<?php echo $_POST['person_ddd'] ?>' size='2' />
						<?php #Telefone. SEM O DDDD. SOMENTE N�MEROS. ?>
						<input type='text' name='person_phone' value='<?php echo $_POST['person_phone'] ?>'/>
						</td>
					</tr>
					<tr><th scope='row'><?php _e('Comment', 'dbem') ?>:</th><td><textarea name='booking_comment'><?php echo $_POST['booking_comment'] ?></textarea></td></tr>
			</table>
			<p>
				<input type='submit' value='<?php _e('Send your booking', 'dbem') ?>'/>&nbsp;&nbsp;&nbsp;&nbsp;
				<input type='hidden' name="booking_seats" value="1" />
				<input type='hidden' name='person_country' value='BRA' />
			 	<input type='hidden' name='eventAction' value='add_booking'/>
			 	<input type='hidden' name='event_id' value='<?php echo $EM_Event->id; ?>'/>
			</p>  
		</form>
	</div>
	<script type="text/javascript">
		jQuery(document).ready( function($){
			if( $('#dbem-booking-delete').size() > 0 ){
				var triggerText = '<?php ($_POST['eventAction'] == 'delete_booking') ? _e('Hide cancellation form', 'dbem') : _e('Cancel a booking', 'dbem'); ?>';
				$('#dbem-rsvp input[type=submit]').after(' <a href="#" id="dbem-booking-cancel">'+triggerText+'</a>');
				if( $('#dbem-booking-cancel').html() == '<?php _e('Cancel a booking', 'dbem'); ?>' ) { $('#dbem-booking-delete').hide(); }
				$('#dbem-booking-cancel').click( function(event){
					event.preventDefault();
					if( $('#dbem-booking-cancel').html() == '<?php _e('Cancel a booking', 'dbem'); ?>' ){
						$('#dbem-booking-cancel').html('<?php _e('Hide cancellation form', 'dbem'); ?>');
						$('#dbem-booking-delete').slideDown();
					}else{
						$('#dbem-booking-cancel').html('<?php _e('Cancel a booking', 'dbem'); ?>');
						$('#dbem-booking-delete').slideUp();
					}
				});
			}
		});
	</script>
	<?php
	return ob_get_clean();	
}

/**
 * Booking removal in front end, called by placeholder #_REMOVEBOOKINGFORM
 * @return string
 */
function em_delete_booking_form() {   
	global $dbem_form_messages_booking_delete, $EM_Event;	
	$destination = "?".$_SERVER['QUERY_STRING'];
	ob_start();
	?>
	<div id="dbem-booking-delete">
		<a name="dbem-booking-delete"></a>
		<h3><?php _e('Cancel your booking', 'dbem') ?></h3>
		
		<?php if( !empty($dbem_form_messages_booking_delete['success']) ) : ?>
		<div class='dbem-rsvp-message-success'><?php echo $dbem_form_messages_booking_delete['success'] ?></div>
		<?php elseif( !empty($dbem_form_messages_booking_delete['error']) ) : ?>
		<div class='dbem-rsvp-message-error'><?php echo $dbem_form_messages_booking_delete['error'] ?></div>
		<?php elseif( !empty($dbem_form_messages_booking_delete['message']) ) : ?>
		<div class='dbem-rsvp-message'><?php echo $dbem_form_messages_booking_delete['message'] ?></div>
		<?php endif; ?>
		
		<form name='booking-delete-form' method='post' action='<?php echo $destination ?>#dbem-booking-delete'>
			<table class='dbem-rsvp-form'>
				<tr>
					<th scope='row'><?php _e('Name', 'dbem') ?>:</th><td><input type='text' name='person_name' value='<?php echo $_POST['person_name'] ?>'/></td>
				</tr>
			  	<tr>
			  		<th scope='row'><?php _e('E-Mail', 'dbem') ?>:</th><td><input type='text' name='person_email' value='<?php echo $_POST['person_email'] ?>'/></td>
			  	</tr>
			</table>
			<input type='hidden' name='eventAction' value='delete_booking'/>
			 <input type='hidden' name='event_id' value='<?php echo $EM_Event->id; ?>'/>
			<input type='submit' value='<?php _e('Cancel your booking', 'dbem') ?>'/>
		</form>
	</div>
	<?php
	return ob_get_clean();	
}
?>