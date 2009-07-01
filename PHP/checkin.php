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
       <a href="<?=SITE_BASE_URL?>/index.php">LibDBDatabase</a> &raquo; <a href="<?=$_SERVER['PHP_SELF']?>">Check In Media</a>
      </strong>
     </div>

     <div id="leftside">
      <?php include_once("include/sidemenu.php"); ?>
     </div>

     <a id="main"></a>
     <div id="contentalt">
      <h1>Check In Center</h1>
      <img src="<?=SITE_BASE_URL?>/img/gravatar-checkin.png" height="80" width="80" alt="Checkin Gravatar" />

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />
      <h1>Check In</h1>
      <br />

	    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
	    <table border=0>
		<tr>
		<td>Media ID number: </td>
		 <td><input type="text" name="q" value="<?php
		 	if(isset($_GET['isbn']) && !empty($_GET['isbn'])){ 
				echo $_GET['isbn'];
			} else if(isset($_GET['issn']) && !empty($_GET['issn'])){ 
				echo $_GET['issn'];
			} else if(isset($_GET['upc']) && !empty($_GET['upc'])){ 
				echo $_GET['upc'];
			} if(!isset($_POST["q"]) || empty($_POST["q"])) {
				echo "";
			} else {
				echo $_POST["q"];
			}?>"/></td>
		 <td>
		  <select name="idtype">
			<?php
				$selected;
				if((isset($_GET['isbn']) && !empty($_GET['isbn'])) || 
					$_POST["idtype"] == "ISBN"){ 
					$selected = 1;
				} else if((isset($_GET['issn']) && !empty($_GET['issn'])) || 
					$_POST["idtype"] == "ISSN"){ 
					$selected = 2;
				} else if((isset($_GET['upc']) && !empty($_GET['upc'])) || 
					$_POST["idtype"] == "UPC"){ 
					$selected = 3;
				} else if(!isset($_POST["idtype"]) || empty($_POST["idtype"])){
					$selected = 0;
				} else if($_POST["idtype"] == "SICI"){
					$selected = 4;
				}
			?>
		  <option value="ISBN" <?php if($selected == 1){ echo "selected"; } ?>>ISBN</option>
		  <option value="ISSN" <?php if($selected == 2){ echo "selected"; } ?>>ISSN</option>
		  <option value="UPC" <?php if($selected == 3){ echo "selected"; } ?>>UPC</option>
		  <option value="SICI" <?php if($selected == 4){ echo "selected"; } ?>>SICI</option>
		  </select>
		  </td>
        </tr>
        <tr>
		 <td>Username: </td>
		 <td><input type="text" name="quid" value="<?php
		  if(!isset($_POST["quid"]) || empty($_POST["quid"])) {
		   echo "";
		  } else {
		   echo $_POST["quid"];
		  }?>"/></td>
        </tr>
		<tr><td>
	     <input type="submit" value="Check In" />
		</td></tr>
	   </form>

	  </table>

<?php
$searchQuery = @$_POST['q'] ;
$searchQueryUID = @$_POST['quid'] ;
$idtype = @$_POST['idtype'] ;
$trimmed = trim($searchQuery); //trim whitespace from the stored variable
$username = trim($searchQueryUID);


if($trimmed == "") {
   echo "<p>Please enter the ISBN, ISSN, or SICI of the item and the username of the borrower.</p>";
} else if(!isset($searchQuery)) { //check for a search parameter
  echo "<p>We dont seem to have an item ID number.</p>";
  exit;
} else if(!isset($searchQueryUID)) { //check for a search parameter
  echo "<p>We dont seem to have a username.</p>";
  exit;
} else {
	//Build the itemid query
	$qitemid = "SELECT itemid ";
	if($idtype == 'ISBN'){
		$qitemid .= "FROM ((SELECT itemid, isbn FROM `ldb_books`)
				UNION(SELECT itemid, isbn FROM `ldb_periodicals`)) as T ";
	} else if($idtype == 'UPC'){
		$qitemid .= "FROM ((SELECT itemid, upc FROM `ldb_cds`)
				UNION(SELECT itemid, upc FROM `ldb_dvds`)) as T ";
	}else if($idtype == 'ISSN' || $idtype == 'SICI'){
		$qitemid .= "FROM `ldb_periodicals` ";
	}
	$qitemid .= "WHERE $idtype = '$trimmed'";

	//run the itemid query
	$resultsitemid = mysql_query($qitemid);
	if($resultsitemid) {
		$numresultsitemid = mysql_num_rows($resultsitemid);
	}

	if ($numresultsitemid == 0){
		echo "<p>ERROR: \"$idtype: $trimmed\" not found.</p>";
	}

   $uid = $session->database->getUID($username);
   if (!$uid) {
		echo "<p>ERROR: Username \"$username\" not found.</p>";
	}

	if($numresultsitemid != 0 && $uid) {
		$rowitemid = mysql_fetch_array($database->query($qitemid));
		$itemid = $rowitemid["itemid"];

		$qborrowed = "SELECT * FROM ldb_borroweditems WHERE itemid = '$itemid' AND uid = '$uid' AND returned = 0";
		$resultsborrowed = mysql_query($qborrowed);
		if($resultsborrowed) {
			$numresultsborrowed = mysql_num_rows($resultsborrowed);
		}
		if ($numresultsborrowed == 0) {
			echo "<p>ERROR: \"$username\" does not have \"$idtype: $trimmed\" checked out.</p>";
		} else {
			$q1 = "UPDATE ldb_items SET quantity = quantity + 1 WHERE itemid = '$itemid'";
			$q2 = 
			"UPDATE ldb_borroweditems 
			SET returned = 1 
			WHERE uid = '$uid' AND itemid = '$itemid' AND returned = 0 AND histnum = (
				SELECT MIN(histnum) 
				FROM ((
					SELECT * 
					FROM ldb_borroweditems 
					WHERE returned = 0) as J) 
				WHERE duedate = (
					SELECT MIN(duedate) 
					FROM ((
						SELECT * 
						FROM ldb_borroweditems
						WHERE returned = 0) as T) 
					WHERE uid = '$uid'))";
			mysql_query($q1);
			mysql_query($q2);
			echo "<p>\"$username\" has checked in \"$idtype: $trimmed\" successfully.</p>";
		}
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
</html>