<?php
	class Payment {
		
		var $booking ;
		var $person ; 
		var $event ;
		var $cost ;
		var $status ; 
		
		function Payment($booking_id) {
			$booking = EM_Booking::get(array('booking_id' => $booking_id)) ;
			$person = EM_Person::get(array('person_id' => $booking->person_id)) ;
			$event = new EM_Event($booking->event_id) ; 
			$cost = $event->cost ; $status = $booking->payment_status ; 
		}
		
		function invite_link() {
			_e('In order to confirm your reservation, you will now be redirected to PagSeguro, where you can make your payment.', 'dbem'); ?>
			<a href="payment_url"><? _e('Make payment', 'dbem')  ;?></a><?php
			echo "Link was " . $booking_id ;
		}
	
	}
	?>