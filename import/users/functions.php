<?php  
	
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
	
	function printr($array)
	{
		echo "<pre>";
			print_r($array);
		echo "</pre>";   
	}

	function set_role($old_role)
	{
		return ($old_role == 'administrator') ? 'administrator' : 'subscriber';
	}

	function set_gender( $old_gender )
	{
		return ( $old_gender == 'woman' ) ? 0 : 1;
	} 

	function set_language( $user_lang )
	{
		return ( !empty($user_lang) ) ? substr($user_lang, 0,2) : 'en';
	}

	function get_post_id_by_title($post_title, $type = "post", $output = OBJECT) {
		global $wpdb;
		$post = $wpdb->get_var ( $wpdb->prepare ( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='$type'", $post_title ) );
		if ($post)
			return get_post ( $post, $output )->ID;
		return '';
	}

	function get_product_id_by_ref($ref) {
		global $wpdb;
		$post = $wpdb->get_var ( $wpdb->prepare ( "SELECT P.ID FROM $wpdb->posts as P JOIN $wpdb->postmeta as PM on P.ID = PM.post_id WHERE meta_key = 'ref' AND meta_value = %s AND P.post_type='produits'", $ref ) );
		if ($post)
			return get_post ( $post, OBJECT )->ID;
		return '';
	}

	function set_country( $country ){
		return ( !empty( $country ) ) ? get_post_id_by_title($country, 'countries') : '';
	}

	function selected_product($product_strings, $delimiter = '|')
	{
		$output = array(); 



		if( strpos($product_strings, $delimiter) === false ){  
			$products = array($product_strings); 
		}else{ 
			$products = explode($delimiter, $product_strings);
		}

		foreach ($products as $key => $product) {
			
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

			$output[] = array(
				"prod_id" => get_product_id_by_ref($product)
			);
		} 

		return $output;
			
	}

?>