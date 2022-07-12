<?php

	function is_member()
	{
		$temp = session()->get('user');
		
		$return = false;
		if (isset($temp) && $temp != '') $return = true;
		return $return;
	}

?>