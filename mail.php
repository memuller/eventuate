<?php require_once('../../../wp-load.php') ; 

if($_GET['id'] && is_numeric($_GET['id'])) {
  $Em_Mailer = new EM_Mailer() ;
  $EM_Booking = new EM_Booking($_GET['id']) ;
  $EM_Bookings = new EM_Bookings($EM_Booking->event_id) ;
  $EM_Bookings->payment_reminder_email($EM_Booking) ;
}

?>
