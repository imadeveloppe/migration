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

	function set_country( $country ){
		return ( !empty( $country ) ) ? get_post_id_by_title($country, 'countries') : '';
	}

	function selected_product($product_strings, $delimiter = '|')
	{
		$products = array();
		if( strpos($product_strings, $delimiter) === false ){

			$products[] = array(
				"prod_id" => $product_strings
			);

		}else{

			foreach (explode($delimiter, $product_strings) as $key => $product) {
				$products[] = array(
					"prod_id" => $product
				);
			}

		}

		return $products;
			
	}

?>