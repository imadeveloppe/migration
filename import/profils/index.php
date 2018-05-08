<?php  
require_once("../../../wp-load.php");
require_once("functions.php");

$CSV =  new csvParse();
$profils = $CSV->parseFile('profils.csv');

$nbr_profild_insered = 0;
foreach ($profils as $key => $profil) {
	$profil = (object)$profil;

	$profil_data = array(
        'post_title'    => $profil->profil_name,
        'post_content'  => $profil->description,        
        'post_type'     => 'profils',
        'post_status'   => 'publish',
    );

    global $sitepress;
    if( !empty( $profil->language ) ){
		$sitepress->switch_lang( substr($profil->language, 0, 2) );
    }else{
    	$sitepress->switch_lang( 'en' );
    }
		 

	$profil_id = wp_insert_post($profil_data); 

	if( $profil_id > 0 ){

		add_post_meta($post_id, "jeu"			,  $_POST['select-game']);
		add_post_meta($post_id, "produit"		,  set_product( $profil->device ) );
    	add_post_meta($post_id, "creationdate"	,  $profil->datetime);
    	// add_post_meta($post_id, "thumbs"		, 0);
    	add_post_meta($post_id, "profil_valide"	,  set_profile_state( $profil->status ) ); 
		add_post_meta($post_id, "file"			,  $profil->file);

		$nbr_profild_insered++;
	}
}

echo $nbr_profild_insered." user(s) insered";

?>