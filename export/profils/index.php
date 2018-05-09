<?php 

function printr($array)
{
	echo "<pre>";
		print_r($array);
	echo "</pre>";  
	die();
}

require_once("../../../wp-load.php");
require '../php-export-data.class.php';   

global $wpdb; 

$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."device_profile");

if( !empty($results) ){

	$exporter = new ExportDataCSV('browser', 'profils.csv');
	$exporter->initialize();


	$heading = array(
			'ID',
			'user_email',
			'device',
			'status',
			'datetime',
			'profil_name', 
			'game_type',
			'other_game_type',
			'game_name',
			'description',
			'language',
			'file',
			'like',
			'votes'
	); 
	$exporter->addRow( $heading );

	$profils = array();
	foreach ($results as $key => $row) :
		
		$profil = array(
			"ID"			=> $row->ID,
			"user_email" 	=> get_userdata( $row->ID )->user_email,
			"device" 		=> $row->device,
			"status" 		=> $row->status,
			"datetime" 		=> $row->datetime, 
		);

		$profils_meta = $wpdb->get_results("SELECT meta_key, meta_value FROM ".$wpdb->prefix."device_profilemeta WHERE device_profile_id = $row->ID ");

		foreach ($profils_meta as $key => $meta) {
			$profil[$meta->meta_key] = $meta->meta_value;
		}
 	
 		$profil["votes"] = '';
		$votes = $wpdb->get_results("SELECT U.user_email, V.vote, V.vote_date FROM ".$wpdb->prefix."votes as V JOIN $wpdb->users as U ON V.user_id = U.ID WHERE V.device_profile_id = $row->ID "); 
		
		if( $votes ){
			$arrayVotes = array();
			foreach ($votes as $k => $vote) {

				$arrayVotes[] = str_replace('"', "'", json_encode(array(
															"user" 		=> $vote->user_email,
															"vote_nb" 	=> $vote->vote,
															"vote_date" => $vote->vote_date,
													  )));
			}


			$profil['votes'] = implode('|', $arrayVotes);
		} 
		 
	 
		$profils[] = $profil;
		$exporter->addRow( $profil ); 

	endforeach;

	$exporter->finalize();
	exit();
} 

?>