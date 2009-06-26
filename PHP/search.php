<<<<<<< .mine
<?
/**
 * search.php
 *
 * This is the Search Center page.
 */
include("include/session.php");
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
  <script src="<?=SITE_BASE_URL?>/js/jquery-1.3.2.min.js" type="text/javascript"></script>
  <script src="<?=SITE_BASE_URL?>/js/jquery.validate.min.js" type="text/javascript"></script>
  <script src="<?=SITE_BASE_URL?>/js/jquery.metadata.js" type="text/javascript"></script>

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
      <img src="<?=SITE_BASE_URL?>/img/gravatar-history.png" height="80" width="80" alt="History Gravatar" />

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
   $q = "SELECT * FROM ldb_books WHERE author LIKE \"%$trimmed%\" OR "
       ."description LIKE \"%$trimmed%\" OR title LIKE \"%$trimmed%\""
       ." ORDER BY author";

   $numresults = mysql_query($q);
   if($numresults) {
 	   $numrows = mysql_num_rows($numresults);
   }

   if ($numrows == 0) {
      echo "<p>Sorry, your search: &quot;".$trimmed."&quot; returned zero results</p>";
   } else {
	   if(empty($s)) {
	      $s = 0;
	   }

	   // get results
	   $q .= " limit $s,$limit";
	   $result = $database->query($q);

	   // begin to show results set
	   $count = 1 + $s ;
		echo "<br /><table border='1'><tr>";

		$itemFields = array("ISBN", "Title", "Author", "Publisher", "ReleaseDate", "Rating", "Description");
		for($i=0; $i<count($itemFields); $i++)
		{
		    //$field = mysql_fetch_field($result);
		    echo "<td>$itemFields[$i]</td>";
		}

	   while ($row = mysql_fetch_array($result)) {
			echo "<tr>";
			echo "<td>".$row["isbn"]."</td>";
			echo "<td>".$row["title"]."</td>";
			echo "<td>".$row["author"]."</td>";
			echo "<td>".$row["publisher"]."</td>";
			echo "<td>".$row["releasedate"]."</td>";
			echo "<td>".$row["rating"]."</td>";
			echo "<td>".$row["description"]."</td>";
			echo "</tr>";
	      $count++ ;
	   }
	   echo "</table><br />";

	   $currPage = (($s/$limit) + 1);

	   // create links for more results
	   if ($s>=1) { // bypass PREV link if s is 0
	      $prevs=($s-$limit);
	      print "&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?s=$prevs&q=$searchQuery\">&lt;&lt;Prev 10</a>&nbsp&nbsp;";
	   }

	   $pages = intval($numrows / $limit); // calculate number of pages needing links
	   // $pages now contains int of pages needed unless there is a remainder from division

	   if ($numrows%$limit) { // has remainder so add one page
	      $pages++;
	   }

	   if (!((($s+$limit)/$limit)==$pages) && $pages!=1) {// check to see if last page
	      $news = $s + $limit; // not last page so give NEXT link

	      echo "&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?s=$news&q=$searchQuery\">Next 10 &gt;&gt;</a>";
	   }

	   $a = $s + ($limit) ;
	   if ($a > $numrows) {
	      $a = $numrows ;
	   }
	   $b = $s + 1 ;
	   echo "<p>Showing results $b to $a of $numrows</p>";
   }
}
?>

      <p class="hide"><a href="#top">Back to top</a></p>
     </div>
    </div>

    <?php include_once("include/footer.php"); ?>

   </div>
  </div>
 </body>
</html>=======
<?
/**
 * userhistory.php
 *
 * This is the User Center page. Only administrators & tellers are allowed to view this page while logged in.
 * This page allows for the creation of new user accounts.  Only admins can create new admins & tellers.  All other users
 * can only create regular customers.
 */
include("include/session.php");

if(!$session->logged_in) {
   header("Location: ".SITE_BASE_URL."/index.php");
}

function isValid($tempString){
	$x = $tempString[0];
	
	return $tempString[0];
}
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
  <script src="<?=SITE_BASE_URL?>/js/jquery-1.3.2.min.js" type="text/javascript"></script>
  <script src="<?=SITE_BASE_URL?>/js/jquery.validate.min.js" type="text/javascript"></script>
  <script src="<?=SITE_BASE_URL?>/js/jquery.metadata.js" type="text/javascript"></script>

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
      <img src="<?=SITE_BASE_URL?>/img/gravatar-history.png" height="80" width="80" alt="History Gravatar" />

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />
      <h1>Search</h1>
      <br />
      
	  <form action="search.php" method="post">
	  <input type="text" name="searchQuery" value="<?php
  	 	if(!isset($_POST["searchQuery"]) || empty($_POST["searchQuery"])) {
      		echo "";
  	 	} else 
  	 		echo $_POST["searchQuery"];
	?>"/>
	  <input type="submit" />
	  </form>
	  <?php 
	  $result = 0;
	  
	  
	  if (isset($_POST["searchQuery"]) && !empty($_POST["searchQuery"])) {
	  	$searchQuery = $_POST["searchQuery"];
	  	//$searchQuery = strtolower($_POST["searchQuery"]);
        //$searchQuery = mysql_escape_string($searchQuery);
  	 	//$searchQuery = str_replace(';', '', $searchQuery);
	  	
	  	echo "Search results for \"". $searchQuery . "\" are shown below: <br />"; 
	  	$result = $database->query("SELECT * FROM ldb_books WHERE" .
	  	 						   " title LIKE '%$searchQuery%' or" . 
	  							   " author EQUALS '$searchQuery' " .
	  							   " description LIKE '%$searchQuery%'");
	   echo "SELECT * FROM ldb_books WHERE "
	  	          . "title LIKE '%$searchQuery%' || "
	  	          . "author LIKE '%$searchQuery%' || "
	  	          . "description LIKE '%$searchQuery%'<BR>"; 
	  	if($result != 0){
	  		echo "found some matches<br />";
	  		//echo $result;
	  		$dbarray = mysql_fetch_array($result);
	  		//$dbarray['title'];  		
		  	echo "<h1>Search results:</h1>";
			echo "<table border='0'><tr>";
			
			//echo count($dbarray);
			$itemFields = array("itemNum", "ISBN", "Title", "Author", "Genre", "Publisher", "ReleaseDate", "Rating", "Description", "Cost", "LateFee");
			
			for($i=0; $i<count($itemFields); $i++)
			{
			    //$field = mysql_fetch_field($result);
			    echo "<td>{$itemFields[$i]}</td>";
			}
			// printing table rows
			while($row = mysql_fetch_row($result))
			{
			    echo "<tr>";
			
			    // $row is array... foreach( .. ) puts every element
			    // of $row to $cell variable
			    foreach($row as $cell)
			        echo "<td>$cell</td>";

			    echo "</tr>\n";
			}
			mysql_free_result($result);
			echo "</tr>\n";
			
	  	}
	  	else
	  		echo "no matches found<br />";
	  	//while($usersinfo = mysql_fetch_array($result, MYSQL_ASSOC)) {}
	  }
	  ?><br />

      <p class="hide"><a href="#top">Back to top</a></p>
     </div>
    </div>

    <?php include_once("include/footer.php"); ?>

   </div>
  </div>
 </body>
</html>>>>>>>> .r28
