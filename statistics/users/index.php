<?php 
require_once("../../wp-load.php"); 
require '../php-export-data.class.php'; 
require_once("functions.php");  
 
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);			

	$countries = get_posts(array(
		'post_type' => 'countries',
		'posts_per_page' => -1,
		'suppress_filters' => 0
	));
	

	$list_countries = array();
	
	foreach ($countries as $key => $country):

		$list_countries[] = array(
			"name" => $country->post_title,
			"ids"  => array(
				$country->ID,
				icl_object_id($country->ID, 'countries', false,'fr'),
	 			icl_object_id($country->ID, 'countries', false,'es'),
	 			icl_object_id($country->ID, 'countries', false,'it'),
	 			icl_object_id($country->ID, 'countries', false,'de')
			),
			"users" => 0
		);

	endforeach;


	
 
	
	$startDate = "2017-06-02 00:00:00";
	$weekTime = 60*60*24*7;

	global $wpdb; 
	$weeks = array();  
 
	for ($time = strtotime($startDate) ; $time <= strtotime("2017-06-15 00:00:00") ; ( $time += $weekTime ) ) {  
		
		
		$date_1 = date('Y-m-d H:i:s', $time);
		$date_2 = date('Y-m-d H:i:s', $time+$weekTime); 

			$users_users = $wpdb->get_results("

				SELECT count(users.ID) as users, usermeta.meta_value as id_country
				From $wpdb->users as users
				JOIN $wpdb->usermeta as usermeta ON usermeta.user_id = users.ID
				WHERE 	usermeta.meta_key= 'country'  
						AND usermeta.meta_value != '' 
						AND (users.user_registered BETWEEN '$date_1' AND '$date_2')
						GROUP BY usermeta.meta_value
			"); 			
 	

			$weeks[] =  $users_users;  
 
	}

	$output = array();

	foreach ($weeks as $key => $week):
		
		$countries_in_week = $list_countries;

		foreach ($week as $k => $user):
			 
			foreach ($list_countries as $j => $country ):

				if( in_array($user->id_country, $country['ids']) ){
					$country['users'] += $user->users; 
					$countries_in_week[$j] = $country; 
				}

			endforeach; 

		endforeach; 
		$output[$key] = $countries_in_week;

	endforeach; 


	usort($output[count($output) -1], function ($a, $b){ 
		return ($b["users"] < $a["users"]) ? -1 : 1;
	});

	$sorted_country = array(); 
	foreach ($output[count($output) -1] as $key => $country) {
		$sorted_country[] = $country["name"];
	}   


	$currentWeekNumber = sprintf("%02d", count( $output )+1);



	$exporter = new ExportDataCSV('file', 'telechargements_profils_S'.$currentWeekNumber.'_'.date('Y').'.csv');
	$exporter->initialize(); 


	$exporter->addRow( array_merge( array('SEMAINE', 'WORLD'), array_slice($sorted_country, 0, 25) ) );

	
	foreach ($output as $key => $row):

		$CSV_row = array();

		$CSV_row[] = "S ".sprintf("%02d", ($key+1));
		$CSV_row[] = get_sum_registred_users_by_week( $row );

		usort($row, function ($a, $b) use ($sorted_country) {
		    $pos_a = array_search($a['name'], $sorted_country);
		    $pos_b = array_search($b['name'], $sorted_country);
		    return $pos_a - $pos_b;
		});

		foreach (array_slice($row, 0, 25) as $key => $country){
			$CSV_row[] = $country["users"];
		}
		
		$exporter->addRow($CSV_row);
	endforeach; 

	for ($i=intval($currentWeekNumber); $i <= 52 ; $i++) { 
		$exporter->addRow(
			array_merge(array("S ".sprintf("%02d", ($i))), array_fill(0, 26, ""))
		);
	}

	


	$exporter->finalize();
	exit();
	
		

?>