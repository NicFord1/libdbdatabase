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

class Recommend{

	/* Empty constructor to make the compiler shut up */
	function __construct() {}

	/* Given an itemid that identifies an item, the function will return
	 * the item most borrowed by users who borrowed the input item
	 * (other than the input item itself)
	 *	Returns:
	 *  -2   -> if $itemid wasn't set
		-1   -> if there is no item with the input $itemid
		null -> if there is no recommendation to make
		        (maybe the only user that borrowed $itemid
				never borrowed anything else, so there is
				no data to work with)
		positive integer -> the itemid of the recommended item
	 */
	public static function recommend($itemid){
		if(!isset($itemid)){
			return -2;
		}

		//see if this itemid is for an existing item
		$qexists = "SELECT * FROM ".DB_TBL_PRFX."items WHERE itemid = '$itemid'";
		$resultsexists = mysql_query($qexists);
		if($resultsexists) {
			$numresultsexists = mysql_num_rows($resultsexists);
		}
		if ($numresultsexists == 0){
			return -1;
		}

		//recommend an item
		$qrec =
		"SELECT itemid
		FROM
			((SELECT itemid, COUNT(itemid) as count
			FROM
				((SELECT DISTINCT itemid, uid
				FROM ".DB_TBL_PRFX."borroweditems
				WHERE itemid != '$itemid' AND uid IN (
					SELECT uid
					FROM ".DB_TBL_PRFX."borroweditems
					WHERE itemid = '$itemid')) as T)
			GROUP BY itemid) as U)
		WHERE count =
			(SELECT MAX(count)
			FROM
				((SELECT itemid, COUNT(itemid) as count
				FROM
					((SELECT DISTINCT itemid, uid
					FROM ".DB_TBL_PRFX."borroweditems
					WHERE itemid != '$itemid' AND uid IN (
						SELECT uid
						FROM ".DB_TBL_PRFX."borroweditems
						WHERE itemid = '$itemid')) as T)
				GROUP BY itemid) as U))";

		$rowrec = mysql_fetch_array(mysql_query($qrec));
		$rec = $rowrec["itemid"];

		return $rec;
	}
}
?>