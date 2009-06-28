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
$limit = 10; // rows to return


if($trimmed == "") {
   echo "<p>Please enter a search...</p>";
} else if(!isset($searchQuery)) { //check for a search parameter
  echo "<p>We dont seem to have a search parameter!</p>";
  exit;
} else {
   // Load media plugins which will perform search for us.
   require_once("plugins/book.php");
   require_once("plugins/cd.php");

   // Tell plugins to search for items
   $searchresults = performFunctionOnAllPlugins("search", $trimmed, $database);

   // begin to show results set
   $count = 1 + $s ;
   echo "<br /><table border='1'><tr>";

   // Aggregate all column names that we need
   foreach($searchresults as $aresultlist)
   {
     if(!is_array($aresultlist))
     {
       continue;
     }
     foreach($aresultlist as $aresult)
     {
       $columnsForThisItem = array_keys($aresult);

       foreach($columnsForThisItem as $newcolumn)
       {
         if(!array_key_exists($newcolumn, $results))
         {
           $results[$newcolumn][0] = $newcolumn;
           echo "Added $newcolumn<br>";
         }
       }
     }
   }
   $itemFields = array_keys($results);//array("ISBN", "Title", "Author", "Publisher", "ReleaseDate", "Rating", "Description");
		for($i=0; $i<count($itemFields); $i++)
		{
		    //$field = mysql_fetch_field($result);
		    echo "<td>$itemFields[$i]</td>";
		}

   foreach($searchresults as $aresultlist)
   {
     if(!is_array($aresultlist))
     {
       continue;
     }

     foreach($aresultlist as $aresult)
     {
       echo "<tr>";

       foreach($itemFields as $field)
       {
         if(array_key_exists($field, $aresult))
         {
           $text = $aresult[$field];
         } else {
           $text = "n/a";
         }
         echo "<td>".$text."</td>";
       }
     }
   }
   echo "</tr>";
   $count++ ;


   echo "</table><br />";

   $currPage = (($s/$limit) + 1);

	   // create links for more results
	   if ($s>=1) { // bypass PREV link if s is 0
	      $prevs=($s-$limit);
	      print "&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?s=$prevs&q=$searchQuery\">&lt;&lt;Prev 10</a>&nbsp&nbsp;";
	   }

	   $pages = intval($numresults / $limit); // calculate number of pages needing links
	   // $pages now contains int of pages needed unless there is a remainder from division

	   if ($numresults%$limit) { // has remainder so add one page
	      $pages++;
	   }

	   if (!((($s + $limit) / $limit) == $pages) && $pages != 1) {// check to see if last page
	      $news = $s + $limit; // not last page so give NEXT link
	      echo "&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?s=$news&q=$searchQuery\">Next 10 &gt;&gt;</a>";
	   }

	   $a = $s + ($limit) ;
	   if ($a > $numresults) {
	      $a = $numresults ;
	   }
	   $b = $s + 1 ;
	   echo "<p>Showing results $b to $a of $numresults</p>";

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
