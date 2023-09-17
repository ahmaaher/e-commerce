<?php

function lang($phrase) {
	static $lang = array(
		"home" 			=> "Home",
		"categories" 	=> "Categories",
		"items" 		=> "Items",
		"members" 		=> "Members",
		"comments" 		=> "Comments",
		"stat" 			=> "Statistics",
		"logs" 			=> "Logs"
	);

	return $lang[$phrase];
}

?>