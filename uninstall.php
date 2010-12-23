<?php 
require_once('../../../wp-load.php'); 
if( current_user_can('activate_plugins') ){
    $prefix = $wpdb->prefix ; 
    #kills of all event_manager options
    $wpdb->query("delete from " . $prefix . 'options' . " where option_name like 'dbem%' ;") or die ('failed to remove pertinent wp_options') ;
    
    #deletes all tables, except em_recurrence_table
    $sql = "drop table ${prefix}em_bookings, ${prefix}em_categories, ${prefix}em_events, ${prefix}em_locations, ${prefix}em_people ;" ;
    $wpdb->query($sql) or die ('failed to destroy tables') ;
    echo "looks like everything worked." ;
} else {
    echo "sorry, you can't do that." ;    

}
?>
