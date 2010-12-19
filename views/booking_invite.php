<?php
  $data = $this->prepare_data() ;

  _e('In order to confirm your reservation, you will now be redirected to PagSeguro, where you can make your payment.', 'dbem');
?>
<?php // This form sends the user to Pagseguro. Please do not change anything besides the form id/name and the submit. ?>
  <form id='pagseguro-form' name='pagseguro-form' method='post' action='https://pagseguro.uol.com.br/security/webpagamentos/webpagto.aspx'>
    <?php foreach( $data as $k => $v ) {
      echo "<input type='hidden' name='$k' value='$v' />" ; 
    }?>
    <input type='submit' value='<?php _e('Make payment', 'dbem') ?>'/>
  </form>
