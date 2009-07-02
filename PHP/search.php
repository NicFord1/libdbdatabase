<?
/**
 * search.php
 *
 * This is the Search Center page.
 */
require_once("include/session.php");
?>

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


   // Aggregate all column names that we need
   $column_names[] = array();

   foreach($searchresults as $aresultlist) {
     if(!is_array($aresultlist) OR !isset($aresultlist)) {
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
   $itemFields = array_keys($column_names, 1);

   echo "<br /><table border='1'><tr>";

   // Print first row of table, showing the column names
   for($i=0; $i<count($itemFields); $i++) {
     echo "<td>$itemFields[$i]</td>";
   }

   echo "</tr>";

   // Print item rows
   foreach($results as $aresult) {
    echo "<tr>";

    if(count($aresult) == 0)
    	continue;

    foreach($itemFields as $field) {
      if(array_key_exists($field, $aresult)) {
        $text = $aresult[$field];
      } else {
        $text = "n/a";
      }
      echo "<td>".$text."</td>";
    }

    echo "</tr>";
   }
 $count++ ;


   echo "</table><br />";
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
