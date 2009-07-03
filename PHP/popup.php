<script type="text/javascript" src="<?=SITE_BASE_URL?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=SITE_BASE_URL?>/js/thickbox.min.js"></script>
<style type="text/css" media="all">@import "<?=SITE_BASE_URL?>/css/thickbox.css";</style>
<style type="text/css">

ol{
	margin:0;
	padding: 1em 1em;
}

table{
	color:#FFF;
	background:#737CA1 url(img/blue_background.png) repeat-x top left;
	border:5px solid #737CA1;
	border-collapse:collapse;
	width:50em;
	height:20em;
	text-align:center;
	font-size:80%;
}

thead{
}

thead th{
	padding:1em 1em 1em;
 	border-bottom:1px dotted #737CA1;
 	font-size:100%;
 	text-align:center;
}

thead tr{
}

td, th{
	background:transparent;
	padding:.2em .1em;
}

tbody tr{
}


tbody tr.odd td{
	background:transparent url(img/orange_background.png) repeat top left;
}

* html tr.odd td{
	background:none;
	filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='img/blue_background.png', sizingMethod='scale');
}
</style>


<?php
require_once("include/database.php");
require_once("include/pluginUtils.php");

if(isset($_GET['itemid'])) {
	$itemid = trim($_GET['itemid']);
	//print $itemid;

   opendir("plugins");
   while($file = readdir()) {
     if(is_file("plugins/$file")) {
       require_once("plugins/$file");
     }
   }
	$numresults = 0;
	$searchresults = performFunctionOnAllPlugins("get", $itemid, $database);

	//echo $searchresults;

	   // Aggregate all column names that we need
   $column_names[] = array();

   foreach($searchresults as $aresultlist) {
     if(!is_array($aresultlist) OR !isset($searchresults)) {
       continue;
     }
     foreach($aresultlist as $aresult) {
       // Copy results to a flat list
       $results[$numresults++] = $aresult;
       // echo "$aresult\n";

       // Now aggregate the columns
       $columnsForThisItem = array_keys($aresult);

       foreach($columnsForThisItem as $newcolumn) {
         if(!array_key_exists($newcolumn, $column_names)) {
           $column_names[$newcolumn] = 1;
         }
       }
     }
   }
   $itemFields = array_keys($column_names);

   echo "<br /><table><tr><tbody>";
   echo "<col id='title'>
		<col id='authors'>
		<col id='description'>";

   // Print first row of table, showing the column names
	echo "<tr>";

   for($i=0; $i<count($itemFields); $i++) {
   	if($itemFields[$i] == "ITEMID")
   		continue;
	  echo "<th>$itemFields[$i]</th>";
   }
   echo "</tr>";
   echo "</thead><tbody>";

   // Print item rows
   foreach($results as $aresult) {
    echo "<tr>";

    if(count($aresult) == 0)
    	continue;

    foreach($itemFields as $field) {
      if(array_key_exists($field, $aresult)) {
        $text = $aresult[$field];
        if($field != "ITEMID")
        	echo "<td>".nl2br($text)."</td>";
      }
    }
 	echo "</tr>";
   }
	echo"<tr><td colspan=7>";
	include_once("include/recommend.php");
	$itemidrec = Recommend::recommend($itemid);
	if($itemidrec > 0){
	$qrecommend = "SELECT title
		FROM ((SELECT itemid, title FROM `ldb_books`)
		UNION(SELECT itemid, title FROM `ldb_cds`)
		UNION(SELECT itemid, title FROM `ldb_dvds`)
		UNION(SELECT itemid, title FROM `ldb_periodicals`)) as T
		WHERE itemid = '$itemidrec'";

		//run the itemidrec query
		$resultsrecommend = mysql_query($qrecommend);
		if($resultsrecommend) {
			$numresultsrecommend = mysql_num_rows($resultsrecommend);
		}
		if ($numresultsrecommend > 0){
			$rowtitlerec = mysql_fetch_array($database->query($qrecommend));
			$titlerec = $rowtitlerec["title"];

			echo "<p>Users who borrowed this item also borrowed \"$titlerec\".</p>";
		}
	}
	echo"</td></tr>";
   echo "</tbody></table>";
}
?>