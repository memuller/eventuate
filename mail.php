<?php require_once('../../../wp-load.php') ; 

  if($_GET['id'] && is_numeric($_GET['id'])) {
    $EM_Booking = new EM_Booking($_GET['id']) ;
    $EM_Booking->payment_reminder_email() ;
  }

?>
