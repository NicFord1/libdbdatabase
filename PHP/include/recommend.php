<?php
/**
 * recommend.php
 *
 * This class has function(s) that recommend items to users based on
 * database history.
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'recommend.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}

require_once("database.php");

class Recommend{
	
	/* Given an itemid that identifies an item, the function will return
	 * the item most borrowed by users who borrowed the input item
	 * (other than the input item itself) */
	public static function recommend($itemid){
		
	}
};
?>