<?php
/**
 * @author memuller
 * Standard events calendar widget
 */
class EM_Booking_Widget extends WP_Widget {
    // constructor
    function EM_Booking_Widget() {
    	$widget_ops = array('description' => __( "Displays the booking add form for a given event", 'dbem') );
        parent::WP_Widget(false, $name = __('Add booking', 'dbem'), $widget_ops);
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
		global $EM_Event;
		$EM_Event = new EM_Event($instance['event_id']); 
		
    	echo $args['before_widget'];
	    echo $args['before_title'];
	    echo $instance['title'];
	    echo $args['after_title'];
		
		echo em_add_booking_form();
	    
		echo $args['after_widget'];
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$events = EM_Events::get() ;
		$instance = $old_instance ;
		$instance['title'] = ($new_instance['title'] <> '' ) ? $new_instance['title'] : __('Make your booking', 'dbem') ;
		$instance['event_id'] = $new_instance['event_id'] ;
    	return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $instance['title'] = (isset($instance['title'])) ? $instance['title'] : __('Make your booking', 'dbem') ;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<select name="<?php echo $this->get_field_name('event_id') ?>" id='<?php echo $this->get_field_id('event_id') ?>'>
				<?php foreach( EM_Events::get() as $k => $v ){ ?>
					<option value="<?php echo $v->id ?>" <?php echo (isset($instance['event_id']) && $instance['event_id'] == $v->id) ? "selected=selected" : "" ; ?> ><?php echo $v->name ?></option>
				<?php } ?>
			</select>
		</p>
        <?php
    }
}
add_action('widgets_init', create_function('', 'return register_widget("EM_Booking_Widget");'));
?>