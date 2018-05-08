<?php   
 
require_once("../../../wp-load.php");  
require '../php-export-data.class.php';  

$list = ( isset($_GET['list']) ) ? $_GET['list'] : 0;

$user_query = new WP_User_Query(array(
	'number' => 10000, 'offset' => 10000*($list-1)
));
 
$users = $user_query->get_results(); 

if ( !empty( $users ) ):

	$exporter = new ExportDataCSV('file', '../../import/users/users_'.$list.'.csv');
	$exporter->initialize(); 

	$usersdata = array();
	$heading = array(
			'ID',
			'user_login',
			'user_pass',
			'user_nicename',
			'user_email',
			'user_url', 
			'user_registered',
			'user_status',
			'display_name',
			'role',
			'gender',
			'first_name',
			'last_name',
			'description',
			'locale',
			'birthdate',
			'country',
			'device',
			'gamer_type',
			'state'
	);

	$exporter->addRow( $heading );
 
	foreach ($users  as $user ): 

		$userdata = array(
			'ID' 				=> $user->data->ID,
			'user_login' 		=> $user->data->user_login,
			'user_pass' 		=> $user->data->user_pass,
			'user_nicename' 	=> $user->data->user_nicename,
	        'user_email' 		=> $user->data->user_email, 
	        'user_url' 			=> $user->data->user_url, 
	        'user_registered'	=> $user->data->user_registered, 
	        'user_status' 		=> $user->data->user_status, 
	        'display_name' 		=> $user->data->display_name,  
	        'role' 				=> $user->roles[0],   

			'gender' 		=> get_user_meta($user->data->ID,'gender', true),
			'first_name'	=> get_user_meta($user->data->ID,'first_name', true),
	        'last_name' 	=> get_user_meta($user->data->ID,'last_name', true),  
			'description' 	=> get_user_meta($user->data->ID,'description', true),  
			'locale' 		=> get_user_meta($user->data->ID,'locale', true),  
			'birthdate' 	=> get_user_meta($user->data->ID,'birthdate', true),  
			'country' 		=> get_user_meta($user->data->ID,'country', true),  
			'device' 		=> implode('|', get_user_meta($user->data->ID,'device', true)),  
			'gamer_type' 	=> get_user_meta($user->data->ID,'gamer_type', true),  
			'state' 		=> ( get_user_meta($user->data->ID,'nacondb_tify_membership_status', true) == 'activated' ) ? 1 : 0
		); 
 
		$exporter->addRow( $userdata ); 

	endforeach; 

	$exporter->finalize(); // writes the footer, flushes remaining data to browser.

	exit(); // all done 

endif; 




?> 