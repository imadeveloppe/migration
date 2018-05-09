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
		$sitepress->switch_lang( substr($profil->user_email, 0, 2) );
    }else{
    	$sitepress->switch_lang( 'en' );
    }
 


	$profil_id = wp_insert_post($profil_data); 

	if( $profil_id > 0 ){ 

        add_post_meta($profil_id, "userid", get_user_by_email( $profil->user_email )->ID );
		add_post_meta($profil_id, "jeu"			,  '');
		add_post_meta($profil_id, "produit"		,  set_product( $profil->device ) );
    	add_post_meta($profil_id, "creationdate"	,  $profil->datetime);
    	// add_post_meta($post_id, "thumbs"		, 0);
    	add_post_meta($profil_id, "profil_valide"	,  set_profile_state( $profil->status ) ); 
		add_post_meta($profil_id, "file"			,  get_site_url().$profil->file);

		$nbr_profild_insered++;
	}
}

echo $nbr_profild_insered." profils(s) insered";

?>