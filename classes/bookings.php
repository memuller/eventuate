<?php
/**
 * Deals with the booking info for an event
 * @author marcus
 *
 */
class EM_Bookings extends EM_Object{
	//TODO Bookings needs redoing
	
	/**
	 * Array of EM_Booking objects for a specific event
	 * @var array
	 */
	var $bookings = array();
	/**
	 * Event ID
	 * @var int
	 */
	var $event_id;
	/**
	 * Number of seats for this event
	 * @var int
	 */
	var $seats;
	
	var $feedback_message = "";
	var $errors = array();
	
	/**
	 * Creates an EM_Bookings instance, 
	 * @param EM_Event $event
	 * @return null
	 */
	function EM_Bookings( $event ){
		//TODO maybe not load bookings here (for speed), and have an init() function called by all functions?
		if( is_object($event) && get_class($event) == "EM_Event" ){ //Creates a blank bookings object if needed
			global $wpdb;
			$this->event_id = $event->id;
			$this->seats = $event->seats;
			$sql = "SELECT * FROM ". $wpdb->prefix . EM_BOOKINGS_TABLE ." b, ". $wpdb->prefix . EM_PEOPLE_TABLE ." p WHERE event_id ='{$this->event_id}' AND p.person_id=b.person_id";
			$bookings = $wpdb->get_results($sql, ARRAY_A);
			foreach ($bookings as $booking){
				$this->bookings[] = new EM_Booking($booking);
			}
		}
	}
	
	/**
	 * Add a booking into this event (or add seats if person already booked this), checking that there's enough space for the event
	 * @param $EM_Booking
	 * @return boolean
	 */
	function add( $EM_Booking ){
		global $wpdb,$EM_Mailer;
		if ( $this->get_available_seats() >= $EM_Booking->seats ) {  
			$EM_Booking->event_id = $this->event_id;
			// checking whether the booker has already booked places
			$previous_booking = $this->find_previous_booking( $EM_Booking );
			$email = false;
			if ( is_object($previous_booking) ) { 
				// New behavior: no multiple bookings are allowed.
				$this->errors[] = "<strong>" . __("Sorry, but you cannot book more than one seat per event.") . "</strong>" ;
				return false ; 
				//Previously booked, so we add these seats to the booking
				$new_seats = $EM_Booking->seats;
				$EM_Booking = $previous_booking;
				$EM_Booking->seats += $new_seats;	
				$result = $EM_Booking->save();
				if($result){
					$email = $this->email($EM_Booking);
				}
			} else {
				//New booking, so let's save the booking
				$result = $EM_Booking->save();
				if($result){
					$email = $this->email($EM_Booking);
				}
			}
			if($result){
				//Success
				$this->feedback_message = __('Booking successful.', 'dbem');
				if(!$email){
					$this->feedback_message .= ' '.__('However, we were not able to send you an email.', 'dbem');
					if( current_user_can('activate_plugins') ){
						if( is_array($this->errors) ){
							$this->feedback_message .= '<br/><strong>Errors:</strong> (only admins see this message)<br/><ul><li>'. implode('</li><li>', $EM_Mailer->errors).'</li></ul>';
						}else{
							$this->feedback_message .= '<br/><strong>No errors returned by mailer</strong> (only admins see this message)';
						}
					}
				}
				return true;
			}else{
				//Failure
				$this->errors[] = "<strong>".__('Booking could not be created').":</strong><br />". implode('<br />', $EM_Booking->errors);
			}
		} else {
			 $this->errors[] = __('Booking cannot be made, not enough seats available!', 'dbem');
			 return false;
		} 
	}
	
	/**
	 * Delete bookings on this id
	 * @return boolean
	 */
	function delete(){
		global $wpdb;
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix.EM_BOOKINGS_TABLE." WHERE event_id='{$this->event_id}'");
		return ($result);
	}

	/**
	 * Returns number of available seats for this event
	 * @return int
	 */
	function get_available_seats(){
		$booked_seats = 0;
		foreach ( $this->bookings as $booking ){
			if ( $booking->is_paid() ){
				$booked_seats += 1;
			}
		}
		return $this->seats - $booked_seats;
		
	}
	
	function get_paid_seats(){
		$paid_seats = 0 ;
		foreach( $this->bookings as $booking ){
			if( $booking->is_paid() ){
				$paid_seats += 1 ;
			}
		}
		return $paid_seats ; 
	}
	
	/**
	 * Returns number of booked seats for this event
	 * @return int
	 */
	function get_booked_seats(){
		$booked_seats = 0;
		foreach ( $this->bookings as $booking ){
			$booked_seats += $booking->seats;
		}
		return $booked_seats;
	}
	
	/**
	 * Checks if a person with similar details has booked for this before
	 * @param $person_id
	 * @return EM_Booking
	 */
	function find_previous_booking($EM_Booking){
		//First see if we have a similar person on record that's making this booking
		$EM_Booking->person->load_similar();
		//If person exists on record, see if they've booked this event before, if so return the booking.
		if( is_numeric($EM_Booking->person->id) && $EM_Booking->person->id > 0 ){
			$EM_Booking->person_id = $EM_Booking->person->id;
			foreach ($this->bookings as $booking){
				if( $booking->person_id == $EM_Booking->person->id ){
					return $booking;
				}
			}
		}
		return false;
	}

	/**
	 * @param $EM_Booking
	 * @return boolean
	 */

        function replace_shortcodes($text){
          global $EM_Event, $EM_Booking ;
          $EM_Payment = new Payment($EM_Booking->id) ;
          $placeholders = array(
            '#_RESPNAME' =>  '#_BOOKINGNAME',//Depreciated
            '#_RESPEMAIL' => '#_BOOKINGEMAIL',//Depreciated
            '#_RESPPHONE' => '#_BOOKINGPHONE',//Depreciated
            '#_SPACES' => '#_BOOKINGSPACES',//Depreciated
            '#_COMMENT' => '#_BOOKINGCOMMENT',//Depreciated
            '#_RESERVEDSPACES' => '#_BOOKEDSEATS',//Depreciated
            '#_BOOKINGNAME' =>  $EM_Booking->person->name,
            '#_BOOKINGEMAIL' => $EM_Booking->person->email,
            '#_BOOKINGPHONE' => $EM_Booking->person->phone,
            '#_BOOKINGSPACES' => $EM_Booking->seats,
            '#_BOOKINGCOMMENT' => $EM_Booking->comment,
            '#_BOOKINGPAYMENTLINK' => $EM_Payment->payment_processor_link() 
          ) ;

          $event_placeholders = array(
            '#_EVENTNAME' => '#_NAME',
            '#_EVENTCOST' => '#_COST'
          );

          foreach ($placeholders as $k => $v){
            $text = str_replace($k, $v, $text) ;
          }

          foreach ($event_placeholders as $k => $v){
            $text = str_replace($k , $v , $text) ;
          }

          $text = $EM_Event->output($text) ;

          return $text ;
        }
        
        function payment_reminder_email($EM_Booking){
          global $EM_Event, $EM_Mailer, $EM_Person , $EM_Booking; 
          $EM_Person = new EM_Person($EM_Booking->person_id) ;
          $EM_Event = new EM_Event($EM_Booking->event_id) ;
          $email_subject = __('Payment Reminder', 'dbem') ;
          $email_body = get_option('dbem_payment_reminder_email_body') ;
          $email_body = $this->replace_shortcodes($email_body) ;
          if ( $EM_Mailer->send($email_subject, $email_body, $EM_Person->email)) {
            _e('Email reminder was send successfully.', 'dbem'); 
          } else {
            _e('There was an error while sending the reminder email. Please check your Wordpress email configuration, and if the problem persists, consult with your webmaster.', 'dbem') ;
          }
        }


	function email($EM_Booking){
		global $EM_Event, $EM_Mailer;
		
		$contact_id = ( $EM_Event->contactperson_id != "") ? $EM_Event->contactperson_id : get_option('dbem_default_contact_person');
				 
		$contact_body = get_option('dbem_contactperson_email_body');
		$booker_body = get_option('dbem_respondent_email_body');
		
                $contact_body = $this->replace_shortcodes($contact_body) ;
                $booker_body = $this->replace_shortcodes($booker_body) ;
		
		//TODO offer subject changes
		if( !$EM_Mailer->send(__('Reservation confirmed','dbem'),$booker_body, $EM_Booking->person->email) ){
			foreach($EM_Mailer->errors as $error){
				$this->errors[] = $error;
			}
			return false;
		}
		if( !$EM_Mailer->send(__("New booking",'dbem'), $contact_body, $EM_Event->contact->user_email) && current_user_can('activate_plugins')){
			foreach($EM_Mailer->errors as $error){
				$this->errors[] = $error;
			}
			$this->errors[] = 'Confirmation email could not be sent to contact person. Registrant should have gotten their email (only admin see this warning).';
			return false;
		}
		
		//TODO need error checking for booking mail send
		return true;
	}
	
}
?>
