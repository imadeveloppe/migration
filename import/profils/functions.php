<?php  

  	function printr($array)
	{
		echo "<pre>";
			print_r($array);
		echo "</pre>";  
		die(); 
	}
	
	class csvParse
	{
		var $mappings = array();

		function parseFile($filename) { 
			$id = fopen($filename, "r");  
			$data = fgetcsv($id, filesize($filename)); 

			if(!$this->mappings)
		   		$this->mappings = $data;

			while($data = fgetcsv($id, filesize($filename))){
			     if($data[0])
			       {
			        foreach($data as $key => $value)
			           $converted_data[$this->mappings[$key]] = addslashes($value); 
			        $table[] = $converted_data; 
			         }
			     }     
			fclose($id); 
			return $table;

		}

  	}

  	function get_post_id_by_title($post_title, $type = "post", $output = OBJECT) {
		global $wpdb;
		$post = $wpdb->get_var ( $wpdb->prepare ( "SELECT ID FROM $wpdb->posts WHERE LOWER(post_title) = %s AND post_type='$type'", strtolower($post_title )) );
		if ($post)
			return get_post ( $post, $output )->ID;
		return 0;
	}

	function get_product_id_by_ref($ref) {
		global $wpdb;
		$post = $wpdb->get_var ( $wpdb->prepare ( "SELECT P.ID FROM $wpdb->posts as P JOIN $wpdb->postmeta as PM on P.ID = PM.post_id WHERE meta_key = 'ref' AND meta_value = %s AND P.post_type='produits'", $ref ) );
		if ($post)
			return get_post ( $post, OBJECT )->ID;
		return '';
	}

	function set_game($game_name)
	{	
		$game_id = get_post_id_by_title( $game_name, 'jeux' );
		if( $game_id == 0 ){

			$game_data = array(
		        'post_title'    => $game_name,
		        'post_type'     => 'jeux',
        		'post_status'   => 'publish',
		    );  
			$game_id = wp_insert_post($game_data); 

		}

		return $game_id;
	}

	function set_product($product)
	{ 
		$product_id = '';

		if( !empty($product) ){

			switch ( $product ) {
				case 'gc400es':
					$product = "PCGC-400ES";
					break; 

				case 'rpc':
					$product = "PS4OFPADREVFRNL";
					break; 

				case 'rpc2':
					$product = "PS4OFPADREV2";
					break; 
			}

			$product_id = get_product_id_by_ref($product);

		}

		return $product_id;
			
	}

	function set_profile_state($state)
	{
		return ( $state == 'activated' ) ? 1 : 2;
	}

	function get_votes( $votes_string, $delimiter = "|" ){
		$output = array();   

		if( strpos($votes_string, $delimiter) === false ){  
			$votes = array($votes_string); 
		}else{ 
			$votes = explode($delimiter, $votes_string);
		}

		foreach ($votes as $key => $vote_json_string) {
			$vote =  (array)json_decode( stripslashes(str_replace("'", '"', $vote_json_string)) );
			$vote['user'] = get_user_by_email( $vote['user'] )->ID;
			$output[] = $vote;
		} 

		return $output;
	} 

?>