<?php
echo "hhhhhhhhhhhhhhhhh";
/**
 * @author memuller
 * Standard events calendar widget
 */
class EM_Booking_Widget extends WP_Widget {
    // constructor
    function EM_Booking_Widget() {
    	$widget_ops = array('description' => __( "Displays the booking add form for a given event", 'dbem') );
        parent::WP_Widget(false, $name = 'Add Booking', $widget_ops);
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    	echo $args['before_widget'];
	    echo $args['before_title'];
	    echo $instance['title'];
	    echo $args['after_title'];
            em_add_booking_form();
	    echo $args['after_widget'];
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
    	//filter the new instance and replace blanks with defaults
    	$defaults = array(
    		'title' => __('Add Booking','dbem'),
    		'event_id' => 'future'

        );
    	foreach($defaults as $key => $value){
    		if($new_instance[$key] == ''){
    			$new_instance[$key] = $value;
    		}
    	}
    	return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Show number of locations','dbem'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" value="<?php echo $instance['limit']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('scope'); ?>"><?php _e('Scope of the locations','dbem'); ?>:</label><br/>
			<select id="<?php echo $this->get_field_id('scope'); ?>" name="<?php echo $this->get_field_name('scope'); ?>" >
				<option value="future" <?php echo ($instance['scope'] == 'future') ? 'selected="selected"':''; ?>><?php _e('Locations with upcoming events','dbem'); ?></option>
				<option value="all" <?php echo ($instance['scope'] == 'all') ? 'selected="selected"':''; ?>><?php _e('All locations','dbem'); ?></option>
				<option value="past" <?php echo ($instance['scope'] == 'past') ? 'selected="selected"':''; ?>><?php _e('Locations with past events ','dbem'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order of the locations','dbem'); ?>:</label><br/>
			<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>" >
				<option value="ASC" <?php echo ($instance['order'] == 'ASC') ? 'selected="selected"':''; ?>><?php _e('Ascendant','dbem'); ?></option>
				<option value="DESC" <?php echo ($instance['order'] == 'DESC') ? 'selected="selected"':''; ?>><?php _e('Descendant','dbem'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('format'); ?>"><?php _e('List item format','dbem'); ?>: </label>
			<textarea rows="5" cols="24" id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>"><?php echo $instance['format']; ?></textarea>
		</p>
        <?php
    }
}
add_action('widgets_init', create_function('', 'return register_widget("EM_Booking_Widget");'));
?>