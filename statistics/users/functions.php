<?php  
	function get_sum_registred_users_by_week( $week ){
		$total = 0;
		foreach ($week as $key => $country) {
			$total += $country["users"];
		} 
		return $total;
	}
?>