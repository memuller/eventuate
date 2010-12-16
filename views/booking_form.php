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
			</table>
			<p>
				<input type='submit' value='<?php _e('Send your booking', 'dbem') ?>'/>&nbsp;&nbsp;&nbsp;&nbsp;
				<input type='hidden' name="booking_seats" value="1" />
				<input type='hidden' name='person_country' value='BRA' />
				<input type='hidden' name='eventAction' value='add_booking'/>
				<input type='hidden' name='event_id' value='<?php echo $EM_Event->id; ?>'/>
			</p>  
		</form>