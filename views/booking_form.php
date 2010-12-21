<form id='dbem-rsvp-form' name='booking-form' method='post' action='<?php echo $destination ?>'>
	<fieldset id="" class="">
		<p class="grid_4">
			<?php #Nome completo ?>
			<label for=""><?php _e('Name', 'dbem') ?>:</label>
			<input type='text' name='person_name' value='<?php echo $_POST['person_name'] ?>'/>
		</p>
		<p class="grid_4">
			<?php #Email, validado via JS. ?>
			<label><?php _e('E-Mail', 'dbem') ?>:</label>
			<input type='text' name='person_email' value='<?php echo $_POST['person_email'] ?>'/>
		</p>
		
		<p class="grid_4">
			<?php /*	Daqui em diante, s�o os campos do endere�o do cliente.
						Estes devem ser separados em v�rios outros campos, como mostrado abaixo, ou o PagSeguro
						recusar� o pagamento imediatamente.
						Recomendo manifestar de alguma forma que o endere�o deve ser igual ao do recebimento
						da fatura de cart�o de cr�dito (se for usado), do contr�rio o PagSeguro pode recusar o
						pagamento quando for conferir os dados (o que � muito inconveniente para n�s, pois at�
						isso acontecer, o cliente ficar� registrado como pagante */ ?>
			<?php #Rua. SOMENTE A RUA. ?>
			<label><?php _e('Address', 'dbem') ?>:</label>
			<input type='text' name='person_address' value='<?php echo $_POST['person_address'] ?>'/>
		</p>
		<p class="grid_4">
			<?php #N�mero do im�vel. SOMENTE O N�MERO. SOMENTE ALGARISMOS NUM�RICOS.  ?>
			<label for="person_num"><?php _e('Number', 'dbem') ?>:</label>
			<input type='text' name='person_num' value='<?php echo $_POST['person_num'] ?>'/>	
		</p>
		
		<p class="grid_4">
			<?php #Complemento (apto, bloco, etc). Pode ser vazio. ?>
			<label for="person_compl"><?php _e('Complement', 'dbem') ?>:</label>
			<input type='text' name='person_compl' value='<?php echo $_POST['person_compl'] ?>'/>
		</p>
		
		<p class="grid_4">
			<?php #Bairro. ?>
			<label for="person_district"><?php _e('District', 'dbem') ?>:</label>
			<input type='text' name='person_district' value='<?php echo $_POST['person_district'] ?>'/>
		</p>
		
		<p class="grid_4">
			<?php #Cidade, por extenso. N�o abrevie. ?>
			<label for="person_city"><?php _e('City', 'dbem') ?>:</label>
			<input type='text' name='person_city' value='<?php echo $_POST['person_city'] ?>'/>
		</p>
		
		<p class="grid_4">
			<?php #Sigla de duas letras do estado. Irei trocar por um menu de sele��o.
				# SOMENTE DOIS CARACTERES. SOMENTE UM ESTADO BRASILEIRO V�LIDO, ou distrito federal (DF) ?>
			<label for="person_uf"><?php _e('UF', 'dbem') ?>:</label>
			<input type='text' name='person_uf' value='<?php echo $_POST['person_uf'] ?>' size='2'/>
		</p>
		
		<p class="grid_4">
			<?php #Cep. SOMENTE N�MEROS. SOMENTE UM CEP V�LIDO. Recomendo m�scara. Posso remover h�phens/etc se necess�rio. ?>
			<label for="person_zip"><?php _e('Zip', 'dbem') ?>:</label>
			<input type='text' name='person_zip' value='<?php echo $_POST['person_zip'] ?>'/>
		</p>
		
		<p class="grid_4">
			<?php #Dois campos de n�mero de telefone. Se for adequado, podem ser unidos, mas ent�o sera necess�rio o uso de m�scaras
				# para deixar claro aonde um acaba e o outro come�a (ex. for�ar ddd/telefone separados por um h�phen) ?>
			<label for="person_ddd"><?php _e('Phone number', 'dbem') ?>:</label>
			<?php #DDD. SOMENTE N�MEROS. SOMENTE DOIS CARACTERES. ?>
			<input type='text' name='person_ddd' value='<?php echo $_POST['person_ddd'] ?>' size='2' />
			<?php #Telefone. SEM O DDDD. SOMENTE N�MEROS. ?>
			<input type='text' name='person_phone' value='<?php echo $_POST['person_phone'] ?>'/>
		</p>
		
	</fieldset>
				
			<p>
				<input type='submit' value='<?php _e('Send your booking', 'dbem') ?>'/>&nbsp;&nbsp;&nbsp;&nbsp;
				<input type='hidden' name="booking_seats" value="1" />
				<input type='hidden' name='person_country' value='BRA' />
				<input type='hidden' name='eventAction' value='add_booking'/>
				<input type='hidden' name='event_id' value='<?php echo $EM_Event->id; ?>'/>
			</p>  
		</form>