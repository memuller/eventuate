<?php
/**
 * Object that holds location info and related functions
 * @author marcus
 */
class EM_Location extends EM_Object {
	//DB Fields
	var $id = '';
	var $name = '';
	var $address = '';
	var $town = '';
	var $latitude = '';
	var $longitude = '';
	var $description = '';
	var $image_url = '';
	//Other Vars
	var $fields = array( 
		'location_id' => array('name'=>'id','type'=>'%d'), 
		'location_name' => array('name'=>'name','type'=>'%s'), 
		'location_address' => array('name'=>'address','type'=>'%s'),
		'location_town' => array('name'=>'town','type'=>'%s'),
		//Not Used - 'location_province' => array('name'=>'province','type'=>'%s'),
		'location_latitude' =>  array('name'=>'latitude','type'=>'%f'),
		'location_longitude' => array('name'=>'longitude','type'=>'%f'),
		'location_description' => array('name'=>'description','type'=>'%s')
	);
	var $required_fields;
	var $feedback_message = "";
	var $mime_types = array(1 => 'gif', 2 => 'jpg', 3 => 'png'); 
	var $errors = array();
	
	/**
	 * Gets data from POST (default), supplied array, or from the database if an ID is supplied
	 * @param $location_data
	 * @return null
	 */
	function EM_Location( $location_data = 0 ) {
		//Initialize
		$this->required_fields = array("name" => __('The location name', 'dbem'), "address" => __('The location address', 'dbem'), "town" => __('The location town', 'dbem'));
		if( $location_data != 0 ){
			//Load location data
			if( is_array($location_data) && isset($location_data['location_name']) ){
				$location = $location_data;
			}elseif( $location_data > 0 ){
				//Retreiving from the database		
				global $wpdb;
				$sql = "SELECT * FROM ". $wpdb->prefix.EM_LOCATIONS_TABLE ." WHERE location_id ='{$location_data}'";   
			  	$location = $wpdb->get_row($sql, ARRAY_A);
			}
			//If gmap is turned off, values may not be returned and set, so we set it here
			if(empty($location['location_latitude'])) {
				$location['location_latitude']  = 0;
				$location['location_longitude'] = 0;
			}
			//Save into the object
			$this->to_object($location, true);
			$this->get_image_url();
		} 
	}
	
	function get_post(){
		//We are getting the values via POST or GET
		$location = array();
		$location['location_id'] = $_POST['location_id'];
		$location['location_name'] = stripslashes($_POST['location_name']);
		$location['location_address'] = stripslashes($_POST['location_address']); 
		$location['location_town'] = stripslashes($_POST['location_town']); 
		$location['location_latitude'] = $_POST['location_latitude'];
		$location['location_longitude'] = $_POST['location_longitude'];
		$location['location_description'] = stripslashes($_POST['content']);
		$this->to_object($location);
	}
	
	function save(){
		global $wpdb;
		$table = $wpdb->prefix.EM_LOCATIONS_TABLE;
		$data = $this->to_array();
		unset($data['location_id']);
		unset($data['location_image_url']);
		if($this->id != ''){
			$where = array( 'location_id' => $this->id );  
			$wpdb->update($table, $data, $where, $this->get_types($data));
		}else{
			$wpdb->insert($table, $data, $this->get_types($data));
		    $this->id = $wpdb->insert_id;   
		}
		$image_upload = $this->image_upload();
		return ( $this->id > 0 && $image_upload );
	}
	
	function delete(){
		global $wpdb;	
		$table_name = $wpdb->prefix.EM_LOCATIONS_TABLE;
		$sql = "DELETE FROM $table_name WHERE location_id = '{$this->id}';";
		$wpdb->query($sql);
		$this->image_delete();	
	}
	
	function get_image_url(){
		if($this->image_url == ''){
		  	foreach($this->mime_types as $type) { 
				$file_path = "/".EM_IMAGE_UPLOAD_DIR."/location-{$this->id}.$type";
				if( file_exists( ABSPATH . $file_path) ) {
					$result = get_bloginfo('wpurl').$file_path;
		  			$this->image_url = $result;
				}
			}
		}
		return $this->image_url;
	}
	
	function image_delete() {
		$file_name= ABSPATH.EM_IMAGE_UPLOAD_DIR."/location-".$this->id;
		foreach($this->mime_types as $type) { 
			if (file_exists($file_name.".".$type))
	  		unlink($file_name.".".$type);
		}
	}
	
	function image_upload(){	
		//TODO better image upload error handling
		if ($_FILES['location_image']['size'] > 0 ) {	
		  	if( !file_exists(ABSPATH.EM_IMAGE_UPLOAD_DIR) ){
				mkdir(ABSPATH.EM_IMAGE_UPLOAD_DIR, 0777);
		  	}
			$this->image_delete();   
			list($width, $height, $type, $attr) = getimagesize($_FILES['location_image']['tmp_name']);
			$image_path = ABSPATH.EM_IMAGE_UPLOAD_DIR."/location-".$this->id.".".$this->mime_types[$type];
			if (!move_uploaded_file($_FILES['location_image']['tmp_name'], $image_path)){
				$this->errors = __('The image could not be loaded','dbem');
				return false;
			}else{
				return true;
			}
		}
		return true;
	}

	function load_similar($criteria){
		global $wpdb;
		$locations_table = $wpdb->prefix.EM_LOCATIONS_TABLE; 
		$prepared_sql = $wpdb->prepare("SELECT * FROM $locations_table WHERE location_name = %s AND location_address = %s AND location_town = %s", stripcslashes($criteria['location_name']), stripcslashes($criteria['location_address']), stripcslashes($criteria['location_town']) );
		//$wpdb->show_errors(true);
		$location = $wpdb->get_row($prepared_sql, ARRAY_A);
		if( is_array($location) ){
			$this->to_object($location);
		}
		return $location;
	}

	/**
	 * Validates the location. Should be run during any form submission or saving operation.
	 * @return boolean
	 */
	function validate(){
		foreach ( $this->required_fields as $field => $description) {
			if ( $this->$field == "" ) {
				$this->errors[] = $description.__(" is missing!", "dbem");
			}       
		}
		if ($_FILES['location_image']['size'] > 0 ) { 
			if (is_uploaded_file($_FILES['location_image']['tmp_name'])) {
	 	 		$mime_types = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
				$maximum_size = get_option('dbem_image_max_size'); 
				if ($_FILES['location_image']['size'] > $maximum_size){ 
			     	$this->errors[] = __('The image file is too big! Maximum size:', 'dbem')." $maximum_size";
				}
		  		list($width, $height, $type, $attr) = getimagesize($_FILES['location_image']['tmp_name']);
				$maximum_width = get_option('dbem_image_max_width'); 
				$maximum_height = get_option('dbem_image_max_height'); 
			  	if (($width > $maximum_width) || ($height > $maximum_height)) { 
					$this->errors[] = __('The image is too big! Maximum size allowed:')." $maximum_width x $maximum_height";
			  	}
			  	if (($type!=1) && ($type!=2) && ($type!=3)){ 
					$this->errors[] = __('The image is in a wrong format!');
			  	}
	  		}
		}
		return ( count($this->errors) == 0 );
	}
	
	function has_events(){
		global $wpdb;	
		$events_table = $wpdb->prefix.EM_EVENTS_TABLE;
		$sql = "SELECT event_id FROM $events_table WHERE location_id = {$this->id}";   
	 	$affected_events = $wpdb->get_results($sql);
		return (count($affected_events) > 0);
	}
	
	function output_single($target = 'html'){
		$format = get_option ( 'dbem_single_location_format' );
		return $this->output($format, $target);	
	}
	
	function output($format, $target="html") {
		$location_string = $format;		 
		preg_match_all("/#_[A-Za-z]+/", $format, $placeholders);
		foreach($placeholders[0] as $result) {
			$match = true;
			$replace = '';
			switch( $result ){
				case '#_MAP': //Depreciated
				case '#_LOCATIONMAP':
			 		$replace = EM_Map::get_single( array('location' => $this) );
					break;
				case '#_DESCRIPTION':  //Depreciated
				case '#_EXCERPT': //Depreciated
				case '#_LOCATIONNOTES':
				case '#_LOCATIONEXCERPT':	
					$replace = $this->description;
					if($result == "#_EXCERPT" || $result == "#_LOCATIONEXCERPT"){
						$matches = explode('<!--more', $this->description);
						$replace = $matches[0];
					}
					break;
				case '#_LOCATIONURL':
				case '#_LOCATIONLINK':
				case '#_LOCATIONPAGEURL': //Depreciated
					$joiner = (stristr(EM_URI, "?")) ? "&amp;" : "?";
					$link = EM_URI.$joiner."location_id=".$this->id;
					$replace = ($result == '#_LOCATIONURL' || $result == '#_LOCATIONPAGEURL') ? $link : '<a href="'.$link.'">'.$this->name.'</a>';
					break;
				case '#_PASTEVENTS': //Depreciated
				case '#_LOCATIONPASTEVENTS':
				case '#_NEXTEVENTS': //Depreciated
				case '#_LOCATIONNEXTEVENTS':
				case '#_ALLEVENTS': //Depreciated
				case '#_LOCATIONALLEVENTS':
					if ($result == '#_PASTEVENTS' || $result == '#_LOCATIONPASTEVENTS'){ $scope = 'past'; }
					elseif ( $result == '#_NEXTEVENTS' || $result == '#_LOCATIONNEXTEVENTS' ){ $scope = 'future'; }
					else{ $scope = 'all'; }
					$events = EM_Events::get( array('location'=>$this->id, 'scope'=>$scope) );
					if ( count($events) > 0 ){
						foreach($events as $event){
							$replace .= $event->output(get_option('dbem_location_event_list_item_format'));
						}
					} else {
						$replace = get_option('dbem_location_no_events_message');
					}
					break;
				case '#_IMAGE': //Depreciated
				case '#_LOCATIONIMAGE':
	        		if($this->image_url != ''){
						$replace = "<img src='".$this->image_url."' alt='".$this->name."'/>";
	        		}
					break;
				case '#_NAME': //Depreciated
				case '#_LOCATIONNAME':
					$replace = $this->name;
					break;
				case '#_ADDRESS': //Depreciated
				case '#_LOCATIONADDRESS': 
					$replace = $this->address;
					break;
				case '#_TOWN': //Depreciated
				case '#_LOCATIONTOWN':
					$replace = $this->town;
					break;
				default:
					$match = false;
					break;
			}
			if($match){ //if true, we've got a placeholder that needs replacing
				//TODO FILTER - placeholder filter
				$replace = apply_filters('em_placeholder', $replace, $result, $target); //USE WITH CAUTION! THIS MIGHT GET RENAMED
				$location_string = str_replace($result, $replace , $location_string );
			}
		}
		$name_filter = ($target == "html") ? 'dbem_general':'dbem_general_rss';
		$location_string = str_replace('#_LOCATION', apply_filters($name_filter, $this->name) , $location_string ); //Depreciated
		return $location_string;	
	}
}