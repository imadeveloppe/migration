<?php 

require_once("../../../wp-load.php");
require_once("functions.php");

$list = ( isset($_GET['list']) ) ? $_GET['list'] : 1;
 


$CSV =  new csvParse();
$users = $CSV->parseFile('users_'.$list.'.csv');

 
$nbr_users_insered = 0;
$nbr_users_updated = 0;

foreach ($users as $key => $user) {

	$user = (object)$user;

	$exist_user =  email_exists( $user->user_email );
	
	if(!$exist_user){

		$userdata = array(
            'user_login' 		=> $user->user_login, 
            'user_nicename' 	=> $user->user_nicename,
            'user_email' 		=> $user->user_email,
            'user_url' 			=> $user->user_url,
            'user_registered' 	=> $user->user_registered,
            'user_status' 		=> $user->user_status,
            'display_name' 		=> $user->display_name,
            'role' 				=>  set_role( $user->role )
        );

		$user_id =  wp_insert_user( $userdata );  
		
		if( $user_id ){

			global $wpdb;
			$wpdb->update( $wpdb->users, array ('user_pass' => $user->user_pass ), array( 'ID' => $user_id ) );

			add_user_meta( $user_id, 'gender'		, set_gender( $user->gender ) );
			add_user_meta( $user_id, 'first_name'	, $user->first_name );
			add_user_meta( $user_id, 'last_name'	, $user->last_name );
			add_user_meta( $user_id, 'description'	, $user->description );
			add_user_meta( $user_id, 'wpml'			, set_language( $user->locale ) );
			add_user_meta( $user_id, 'birthDate'	, $user->birthdate );
			add_user_meta( $user_id, 'country'		, set_country( $user->country ) );
			add_user_meta( $user_id, 'produits'		, selected_product( $user->device ) );
			add_user_meta( $user_id, 'etre_informe'	, 1 );
			add_user_meta( $user_id, 'compte_valide', $user->state );

			$nbr_users_insered++;
		}
			
	}else { 

		$userdata = array(
			'ID'				=> $exist_user,
            'user_login' 		=> $user->user_login, 
            'user_nicename' 	=> $user->user_nicename,
            'user_email' 		=> $user->user_email,
            'user_url' 			=> $user->user_url,
            'user_registered' 	=> $user->user_registered,
            'user_status' 		=> $user->user_status,
            'display_name' 		=> $user->display_name,
            'role' 				=>  set_role( $user->role )
        );

		$update_user_id =  wp_update_user( $userdata ); 
		
		if( $update_user_id ){

			global $wpdb;
			$wpdb->update( $wpdb->users, array ('user_pass' => $user->user_pass ), array( 'ID' => $update_user_id ) );

			// update_user_meta( $exist_user, 'gender'		, set_gender( $user->gender ) );
			// update_user_meta( $exist_user, 'first_name'	, $user->first_name );
			// update_user_meta( $exist_user, 'last_name'	, $user->last_name );
			// update_user_meta( $exist_user, 'description'	, $user->description );
			// update_user_meta( $exist_user, 'wpml'			, set_language( $user->locale ) );
			// update_user_meta( $exist_user, 'birthDate'	, $user->birthdate );
			// update_user_meta( $exist_user, 'country'		, set_country( $user->country ) );
			// update_user_meta( $exist_user, 'produits'		, selected_product( $user->device ) );
			// update_user_meta( $exist_user, 'etre_informe'	, 1 );
			// update_user_meta( $exist_user, 'compte_valide', $user->state );

			$nbr_users_updated++;
		}
	
	}

}
echo "<strong>List: ".$list."</strong><br>";
echo $nbr_users_insered." user(s) insered<br>";
echo $nbr_users_updated." user(s) updated";

?>