<?
/**
 * search.php
 *
 * This is the Search Center page.
 */
require_once("include/session.php");
?>
<style type="text/css">

ol
{
	margin:0;
	padding: 0 1.5em;
}

table
{
	color:#FFF;
	background:#737CA1 url(img/blue_background.png) repeat-x top left;
	border:5px solid #737CA1;
	border-collapse:collapse;
	width:44.2em;
	height:20em;
}



thead
{

}

thead th
{
	padding:1em 1em .5em;
 	border-bottom:1px dotted #737CA1;
 	font-size:100%;
 	text-align:left;
}



thead tr
{

}

td, th
{
	background:transparent;
	padding:.5em 1em;
}

tbody tr
{

}


tbody tr.odd td
{
	background:transparent url(img/orange_background.png) repeat top left;
}

tfoot
{

}

tfoot td
{
	padding-bottom:1.5em;
}

tfoot tr
{

}

* html tr.odd td
{
	background:none;
	filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='img/orange_background.png', sizingMethod='scale');
}


</style>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
 <head>
  <title>LibDBDatabase</title>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
  <meta name="description" content="LibDBDatabase: Library Database" />
  <meta name="keywords" content="library,database" />
  <meta name="author" content="LibDBDatabase / Original design: Andreas Viklund - http://andreasviklund.com/" />
  <link rel="stylesheet" href="<?=SITE_BASE_URL?>/css/andreas06.css" type="text/css" media="screen,projection" />
  <link rel="stylesheet" href="<?=SITE_BASE_URL?>/css/slide.css"  type="text/css" media="screen,projection" />
  <link rel="stylesheet" href="<?=SITE_BASE_URL?>/css/validate.css"  type="text/css" media="screen,projection" />
  <link rel="stylesheet" href="<?=SITE_BASE_URL?>/css/thickbox.css" type="text/css" media="screen" />

  <!-- javascripts -->
  <!-- PNG FIX for IE6 -->
  <!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
  <!--[if lte IE 6]>
   <script type="text/javascript" src="<?=SITE_BASE_URL?>/js/pngfix/supersleight-min.js"></script>
  <![endif]-->

  <!-- jQuery -->
  <script src="<?=SITE_BASE_URL?>/js/jquery.min.js" type="text/javascript"></script>
  <script src="<?=SITE_BASE_URL?>/js/jquery.validate.min.js" type="text/javascript"></script>
  <script src="<?=SITE_BASE_URL?>/js/jquery.metadata.min.js" type="text/javascript"></script>

  <!-- Sliding effect -->
  <script src="<?=SITE_BASE_URL?>/js/slide.js" type="text/javascript"></script>
  <script type="text/javascript" src="<?=SITE_BASE_URL?>/js/thickbox.js"></script>

 </head>

 <body>
  <?php include_once("include/userpanel.php"); ?>

  <div id="container">

   <a id="top"></a>
   <p class="hide">
    Skip to:
    <a href="#menu">site menu</a>
    | <a href="#sectionmenu">section menu</a>
    | <a href="#main">main content</a>
   </p>

   <div id="sitename">
    <h1>LibDBDatabase</h1>
    <span>Library Database System</span>
    <a id="menu"></a>
   </div>

   <?php include_once("include/topmenu.php"); ?>

   <div id="wrap1">
    <div id="wrap2">

     <div id="topbox">
      <strong>
       <span class="hide">Currently viewing: </span>
       <a href="<?=SITE_BASE_URL?>/index.php">LibDBDatabase</a> &raquo; <a href="<?=$_SERVER['PHP_SELF']?>">Search Media</a>
      </strong>
     </div>

     <div id="leftside">
      <?php include_once("include/sidemenu.php"); ?>
     </div>

     <a id="main"></a>
     <div id="contentalt">
      <h1>Search Center</h1>
      <img src="<?=SITE_BASE_URL?>/img/gravatar-search.png" height="80" width="80" alt="Search Gravatar" />

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />
      <h1>Search</h1>
      <br />

      <form action="<?php echo $_SERVER['PHP_SELF']?>" method="GET">
       <input type="text" name="q" value="<?php
if(!isset($_GET["q"]) || empty($_GET["q"])) {
   echo "";
} else {
   echo $_GET["q"];
}?>"/>
       <input type="submit" />
      </form>
<?php
$searchQuery = @$_GET['q'] ;
$trimmed = trim($searchQuery); //trim whitespace from the stored variable

if($trimmed == "") {
   echo "<p>Please enter a search...</p>";
} else if(!isset($searchQuery)) { //check for a search parameter
  echo "<p>We dont seem to have a search parameter!</p>";
  exit;
} else {
   // Load media plugins which will perform search for us.
   opendir("plugins");
   while($file = readdir()) {
     if(is_file("plugins/$file")) {
       require_once("plugins/$file");
     }
   }

   // Tell plugins to search for items
   // $searchresults is a list of lists of items. An item is a map from
   // column name to data.
   $searchresults = performFunctionOnAllPlugins("search", $trimmed, $database);

   $numresults = count($searchresults);
   $count = $numresults;

   // Aggregate all column names that we need
   $column_names[] = array();

   foreach($searchresults as $aresultlist) {
     if(!is_array($aresultlist) OR !isset($searchresults)) {
       continue;
     }
     foreach($aresultlist as $aresult) {
       // Copy results to a flat list
       $results[$numresults++] = $aresult;

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
   
   if($numresults == 0)
   		exit;

   echo "<br /><table><tr><tbody>";
   echo "<col id='title'>
		<col id='authors'>
		<col id='description'>";

   // Print first row of table, showing the column names
	echo "<tr>";
	
	if($session->isTeller() or $session->isAdmin())
		echo "<th>Checkout</th>";
	
	echo "<th>MoreInfo</th>";
	
   for($i=0; $i<count($itemFields); $i++) {
   	if($itemFields[$i] == "Title" or $itemFields[$i] == "Author" or $itemFields[$i] == "Description" or $itemFields[$i] == "Artist")
     	echo "<th>$itemFields[$i]</th>";
    else if(empty($itemFields[$i]))
    	echo "";
    //echo "$itemFields[$i]\n"; 	
     
   }
   echo "</tr>";
   echo "</thead><tbody>";
   echo "<tfoot>
		<tr><td colspan='3'><ol>
		<li>The footer</li>
		</ol></td></tr>
		</tfoot>";

   

   $rowCount = 1;
   // Print item rows
   foreach($results as $aresult) {
 
   	  if($rowCount % 2 == 1 )
      	echo "<tr>";
      else
      	echo "<tr class='odd'";
      $rowCount = $rowCount + 1;
   	
    if(count($aresult) == 0)
    	continue;
    	
  	if($session->isTeller() or $session->isAdmin()){
		if(isset($aresult['ISBN'])){
			$argToPass = "isbn=" . $aresult['ISBN'];
		}
		else if(isset($aresult['ISSN'])){
			$argToPass = "issn=" . $aresult['ISSN'];
		}
		else if(isset($aresult['UPC'])){
			$argToPass = "upc=" . $aresult['UPC'];
		}
		else if(isset($aresult['SICI'])){
			$argToPass = "sici=" . $aresult['SICI'];
		}
		else{
			$argToPass = "";
		}
		//echo $argToPass;
  		echo "<td><a href='checkout.php?$argToPass&' title='CheckOut'><font size='2' color='#00FF00'>Checkout</font></a></td>\n";
  		}
  		
   		 echo "<td><a href='popup.php?itemid=" . $aresult['ITEMID']. "&KeepThis=true&TB_iframe=true&height=265&width=645' title=\"MoreInfo\" class=\"thickbox\"><font size='2' color='#FF0000'>MoreInfo</font></a></td>";

    foreach($itemFields as $field) {
      if(array_key_exists($field, $aresult)) {
        $text = $aresult[$field];
      }else {
        $text = "n/a";
      }

      
      if($field == "Title" or $field == "Author" or $field == "Description")
      	echo "<td>".$text."</td>";
    }
 	echo "</tr>";
   }
 $count++ ;


   echo "</tbody></table>";
}
?>
      <p class="hide"><a href="#top">Back to top</a></p>
     </div>
    </div>

    <?php include_once("include/footer.php"); ?>

   </div>
  </div>
 </body>
</html>
