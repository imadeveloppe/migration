<?php require_once("../../wp-load.php"); 

	
	$countries = get_posts(array(
		'post_type' => 'countries',
		'posts_per_page' => -1,
		'suppress_filters' => 0
	));

 
	
	$startDate = "2017-10-02 00:00:00";
	$weekTime = 60*60*24*7;

	global $wpdb; 
	$weeks = array(); 

	$i = 1;
	for ($time = strtotime($startDate) ; $time <= strtotime($startDate)+(3*$weekTime) ; ( $time += $weekTime ) ) {  
		
		$date_1 = date('Y-m-d H:i:s', $time);
		$date_2 = date('Y-m-d H:i:s', $time+$weekTime);


 		
 		foreach ($countries as $key => $country):

 			$country_translation = array(
 				$country->ID,
 				icl_object_id($country->ID, 'countries', false,'fr'),
 				icl_object_id($country->ID, 'countries', false,'es'),
 				icl_object_id($country->ID, 'countries', false,'it'),
 				icl_object_id($country->ID, 'countries', false,'de'),
 			);  
 

			$users_users = $wpdb->get_results("
				SELECT count(users.ID) as users
				From $wpdb->users as users
				JOIN $wpdb->usermeta as usermeta ON usermeta.user_id = users.ID
				WHERE 	usermeta.meta_key= 'country' 
						AND usermeta.meta_value in (".implode(',', $country_translation).") 
						AND usermeta.meta_value != '' 
						AND (users.user_registered BETWEEN '$date_1' AND '$date_2')

			"); 
  

			$weeks['S'.$i][] =  array(
				"users"	 	 => $users_users[0]->users,
				"id_country" => $country->ID,
				"country"	 => get_the_title( $country->ID  )
			);
  

		endforeach;



		$i++;
	}

 
	
		

?>

 

<pre>
	<?php print_r($weeks) ?>
</pre>