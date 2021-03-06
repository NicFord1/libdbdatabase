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

/**
 * displayHistory - Displays the borrowed items database table in a nicely
 * formatted html table filtered for user of course.
 */
function displayHistory() {
   global $session, $database;

   echo "<h1>My History</h1>\n";

   $q = "SELECT * FROM ".DB_TBL_PRFX."borroweditems WHERE uid='".$session->user->getUID()."' ORDER BY histnum ASC";
   $result = $database->query($q);

   /* Error occurred, return given name by default */
   $num_rows = mysql_numrows($result);

   if(!$result || ($num_rows < 0)) {
      echo "Error displaying info";
      return;
   }

   if($num_rows == 0) {
      echo "Database table empty";
      return;
   }

   /* Display table contents */
   echo "<table id=\"history\" align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
   echo "<tr>\n";
   echo "<th>History Number</th>\n";
   echo "<th>Item ID</th>\n";
   echo "<th>Title</th>\n";
   echo "<th>Due Date</th>";
   echo "<th>Returned</th>\n";
   echo "</tr>\n";

   while($item = mysql_fetch_array($result, MYSQL_ASSOC)) {
      $itemqry = "SELECT * FROM ".DB_TBL_PRFX."items WHERE itemid='".$item['itemid']."'";
      $itemqresult = $database->query($itemqry);
      $iteminfo = mysql_fetch_array($itemqresult, MYSQL_ASSOC);

      if($iteminfo['itemtype'] == "BOOK") {
         $qry = "SELECT * FROM ".DB_TBL_PRFX."books WHERE itemid='".$item['itemid']."'";
         $qresult = $database->query($qry);
         $itemtypeinfo = mysql_fetch_array($qresult, MYSQL_ASSOC);
      } else if ($iteminfo['itemtype'] == "PERIODICAL") {
         $qry = "SELECT * FROM ".DB_TBL_PRFX."periodicals WHERE itemid='".$item['itemid']."'";
         $qresult = $database->query($qry);
         $itemtypeinfo = mysql_fetch_array($qresult, MYSQL_ASSOC);
      } else if ($iteminfo['itemtype'] == "DVD") {
         $qry = "SELECT * FROM ".DB_TBL_PRFX."dvds WHERE itemid='".$item['itemid']."'";
         $qresult = $database->query($qry);
         $itemtypeinfo = mysql_fetch_array($qresult, MYSQL_ASSOC);
      } else if ($iteminfo['itemtype'] == "CD") {
         $qry = "SELECT * FROM ".DB_TBL_PRFX."cds WHERE itemid='".$item['itemid']."'";
         $qresult = $database->query($qry);
         $itemtypeinfo = mysql_fetch_array($qresult, MYSQL_ASSOC);
      } else {
         $itemtypeinfo = NULL;
      }

      echo "<tr>";
      echo "<td>".$item['histnum']."</td>\n";
      echo "<td>".$item['itemid']."</td>\n";
      echo "<td>".$itemtypeinfo['title']."</td>\n";
      date_default_timezone_set('America/New_York');
      echo "<td>".date('l, F jS Y', $item['duedate'])."</td>\n";
      if($item['returned']) {
      	echo "<td>Yes</td>\n";
      } else {
      	echo "<td>No</td>\n";
      }
      echo "</tr>\n";
   }
   echo "</table><br /><br />\n";
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
  <link rel="stylesheet" href="<?php echo SITE_BASE_URL?>/css/andreas06.css" type="text/css" media="screen,projection" />
  <link rel="stylesheet" href="<?php echo SITE_BASE_URL?>/css/slide.css"  type="text/css" media="screen,projection" />
  <link rel="stylesheet" href="<?php echo SITE_BASE_URL?>/css/validate.css"  type="text/css" media="screen,projection" />

  <!-- javascripts -->
  <!-- PNG FIX for IE6 -->
  <!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
  <!--[if lte IE 6]>
   <script type="text/javascript" src="<?php echo SITE_BASE_URL?>/js/pngfix/supersleight-min.js"></script>
  <![endif]-->

  <!-- jQuery -->
  <script src="<?php echo SITE_BASE_URL?>/js/jquery-1.3.2.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.validate.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.metadata.js" type="text/javascript"></script>

  <!-- Sliding effect -->
  <script src="<?php echo SITE_BASE_URL?>/js/slide.js" type="text/javascript"></script>

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
       <a href="<?php echo SITE_BASE_URL?>/index.php">LibDBDatabase</a> &raquo; <a href="<?php echo $_SERVER['PHP_SELF']?>">History Center</a>
      </strong>
     </div>

     <div id="leftside">
      <?php include_once("include/sidemenu.php"); ?>
     </div>

     <a id="main"></a>
     <div id="contentalt">
      <h1>History Center</h1>
      <img src="<?php echo SITE_BASE_URL?>/img/gravatar-history.png" height="80" width="80" alt="History Gravatar" />

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />

      <?php displayHistory(); ?>

      <p class="hide"><a href="#top">Back to top</a></p>
     </div>
    </div>

    <?php include_once("include/footer.php"); ?>

   </div>
  </div>
 </body>
</html>