<section id="alert" class="clearfix">
   <img src="<?php bloginfo('template_directory'); ?>/course/images/cartao.jpg" alt="Atenção nos pagamentos no cartão"/>
   <p>
      <strong>Leia com atenção:</strong>
     Se você for pagar com o cartão de crédito, preencha o seu <span>nome</span> e <span>endereço</span> exatamente como está impresso em sua fatura.
   </p>
</section> 
<form id='dbem-rsvp-form' name='booking-form' method='post' action='<?php echo $destination ?>'>
	<fieldset>
		<p class="wrap">
			<?php #Nome completo ?>
			<label for=""><?php _e('Name', 'dbem') ?>:</label>
			<input type='text' id='person_name' name='person_name' value='<?php echo $_POST['person_name'] ?>'/>
		</p>
		<p class="wrap">
			<?php #Email, validado via JS. ?>
			<label><?php _e('E-Mail', 'dbem') ?>:</label>
			<input type='text' id="person_email" name='person_email' value='<?php echo $_POST['person_email'] ?>'/>
		</p>
		
		<p class="wrap">
			<?php /*	Daqui em diante, s�o os campos do endere�o do cliente.
						Estes devem ser separados em v�rios outros campos, como mostrado abaixo, ou o PagSeguro
						recusar� o pagamento imediatamente.
						Recomendo manifestar de alguma forma que o endere�o deve ser igual ao do recebimento
						da fatura de cart�o de cr�dito (se for usado), do contr�rio o PagSeguro pode recusar o
						pagamento quando for conferir os dados (o que � muito inconveniente para n�s, pois at�
						isso acontecer, o cliente ficar� registrado como pagante */ ?>
			<?php #Rua. SOMENTE A RUA. ?>
			<label for="person_address"><?php _e('Address', 'dbem') ?>:</label>
			<input type='text' id='person_address' name='person_address' value='<?php echo $_POST['person_address'] ?>'/>
		</p>
		<p class="wrap">
			<?php #N�mero do im�vel. SOMENTE O N�MERO. SOMENTE ALGARISMOS NUM�RICOS.  ?>
			<label for="person_num"><?php _e('Number', 'dbem') ?>:</label>
			<input type='text' id='person_num' name='person_num' value='<?php echo $_POST['person_num'] ?>'/>	
		</p>
		
		<p class="wrap">
			<?php #Complemento (apto, bloco, etc). Pode ser vazio. ?>
			<label for="person_compl"><?php _e('Complement', 'dbem') ?>:</label>
			<input type='text' id="person_comp1" name='person_compl' value='<?php echo $_POST['person_compl'] ?>'/>
		</p>
		
		<p class="wrap">
			<?php #Bairro. ?>
			<label for="person_district"><?php _e('District', 'dbem') ?>:</label>
			<input type='text' id-"person_district" name='person_district' value='<?php echo $_POST['person_district'] ?>'/>
		</p>
		
		<p class="wrap">
			<?php #Cidade, por extenso. N�o abrevie. ?>
			<label for="person_city"><?php _e('City', 'dbem') ?>:</label>
			<input type='text' id="person_city" name='person_city' value='<?php echo $_POST['person_city'] ?>'/>
		</p>
		
		<p class="wrap">
			<?php #Sigla de duas letras do estado. Irei trocar por um menu de sele��o.
				# SOMENTE DOIS CARACTERES. SOMENTE UM ESTADO BRASILEIRO V�LIDO, ou distrito federal (DF) ?>
			<label for="person_uf"><?php _e('UF', 'dbem') ?>:</label>
			<select id="person_uf" name='person_uf' value='<?php echo $_POST['person_uf'] ?>'>
        <option value="AC">AC</option>
        <option value="AL">AL</option>
        <option value="AM">AM</option>
        <option value="AP">AP</option>
        <option value="BA">BA</option>
        <option value="CE">CE</option>
        <option value="DF">DF</option>
        <option value="ES">ES</option>
        <option value="GO">GO</option>
        <option value="MA">MA</option>
        <option value="MG">MG</option>
        <option value="MS">MS</option>
        <option value="MT">MT</option>
        <option value="PA">PA</option>
        <option value="PB">PB</option>
        <option value="PE">PE</option>
        <option value="PI">PI</option>
        <option value="PR">PR</option>
        <option value="RJ">RJ</option>
        <option value="RN">RN</option>
        <option value="RO">RO</option>
        <option value="RR">RR</option>
        <option value="RS">RS</option>
        <option value="SC">SC</option>
        <option value="SE">SE</option>
        <option value="SP">SP</option>
        <option value="TO">TO</option>
      </select>

		</p>
		
		<p class="wrap">
			<?php #Cep. SOMENTE N�MEROS. SOMENTE UM CEP V�LIDO. Recomendo m�scara. Posso remover h�phens/etc se necess�rio. ?>
			<label for="person_zip"><?php _e('Zip', 'dbem') ?>:</label>
			<input type='text' id='person_zip' name='person_zip' value='<?php echo $_POST['person_zip'] ?>'/>
		</p>
		
		<p class="wrap">
			<?php #Dois campos de n�mero de telefone. Se for adequado, podem ser unidos, mas ent�o sera necess�rio o uso de m�scaras
				# para deixar claro aonde um acaba e o outro come�a (ex. for�ar ddd/telefone separados por um h�phen) ?>
			<label for="person_ddd"><?php _e('Phone number', 'dbem') ?>:</label>
			<?php #DDD. SOMENTE N�MEROS. SOMENTE DOIS CARACTERES. ?>
			<input type='text' id='person_ddd' name='person_ddd' size='2' value="<?php echo $_POST['person_ddd'] ?>" />
			<?php #Telefone. SEM O DDDD. SOMENTE N�MEROS. ?>
			<input type='text' id="person_phone" name='person_phone' size='8' value='<?php echo $_POST['person_phone'] ?>'/>
		</p>
		
	</fieldset>
				
			<p>
				<?php if ( !empty($dbem_form_messages_booking_add['error']) ) { ?>
					<div class='dbem-rsvp-message-error'><?php echo $dbem_form_messages_booking_add['error'] ?></div>
				<?php } elseif( !empty($dbem_form_messages_booking_add['message']) ) { ?>
					<div class='dbem-rsvp-message'><?php echo $dbem_form_messages_booking_add['message'] ?></div>
				<?php  }  ?>
				
				<input type='submit' class="send" value='<?php _e('Send your booking', 'dbem') ?>'/>&nbsp;&nbsp;&nbsp;&nbsp;
				<input type='hidden' name="booking_seats" value="1" />
				<input type='hidden' name='person_country' value='BRA' />
				<input type='hidden' name='eventAction' value='add_booking'/>
				<input type='hidden' name='event_id' value='<?php echo $EM_Event->id; ?>'/>
			</p>  
		</form>