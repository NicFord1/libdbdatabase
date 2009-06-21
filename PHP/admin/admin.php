<?
/**
 * admin.php
 *
 * This is the Admin Center page. Only administrators are allowed to view this page. This page displays the
 * database table of users and banned users. Admins can choose to delete specific users, delete inactive users,
 * ban users, edit user accounts, etc.
 */
include("../include/session.php");

/**
 * displayUsers - Displays the users database table in a nicely formatted html table.
 */
function displayUsers() {
   global $database;

   $q = "SELECT uid,username,userlevel,regtime,lastvisit FROM ".DB_TBL_USERS." ORDER BY userlevel DESC,username";
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
   echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
   echo "<tr><td><b>Username</b></td><td><b>User Type</b></td><td><b>Registration Time</b></td><td><b>Last Active</b></td><td><b>Actions</b></td></tr>\n";

   for($i=0; $i<$num_rows; $i++) {
      $uid    = mysql_result($result,$i,"uid");
      $uname  = mysql_result($result,$i,"username");
      $ulevel = mysql_result($result,$i,"userlevel");
      date_default_timezone_set('EST');
      $rtime   = date("r", mysql_result($result,$i,"regtime"));
      $lvtime = mysql_result($result,$i,"lastvisit");
      if($lvtime) {
         $lvtime   = date("r", $lvtime);
      } else {
         $lvtime = "Never";
      }

      if($ulevel == ADMIN_LEVEL) {
         $usertype = "Administrator";
      } else if($ulevel == TELLER_LEVEL) {
         $usertype = "Teller";
      } else if($ulevel == CUST_LEVEL) {
         $usertype = "Customer";
      }

      echo "<tr><td>$uname</td><td>$usertype</td><td>$rtime</td><td>$lvtime</td><td>"

          ."<a href=\"userinfo.php?uid=$uid\"><img class=\"clearimg\" src=\"".SITE_BASE_URL."/img/user_edit.png\" alt=\"Edit\" /></a> "

          ."<form action=\"adminprocess.php\" method=\"POST\">"
          ."<input type=\"hidden\" name=\"deluser\" value=\"$uname\" />"
          ."<input type=\"hidden\" name=\"subdeluser\" value=\"1\" />"
          ."<input type=\"image\" src=\"".SITE_BASE_URL."/img/user_delete.png\" alt=\"Delete\">"
          ."</form>"

          ."<form action=\"adminprocess.php\" method=\"POST\">"
          ."<input type=\"hidden\" name=\"banuser\" value=\"$uname\" />"
          ."<input type=\"hidden\" name=\"subbanuser\" value=\"1\" />"
          ."<input type=\"image\" src=\"".SITE_BASE_URL."/img/user_ban.png\" alt=\"Ban\" />"
          ."</form>"

          ."</td></tr>\n";
   }

   echo "</table><br>\n";
}

/**
 * displayBannedUsers - Displays the banned users database table in a nicely formatted html table.
 */
function displayBannedUsers() {
   global $database;

   $q = "SELECT username,timestamp FROM ".DB_TBL_BANNED_USERS." ORDER BY username";
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
   echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
   echo "<tr><td><b>Username</b></td><td><b>Time Banned</b></td><td><b>Actions</b></td></tr>\n";

   for($i=0; $i<$num_rows; $i++) {
      $uname = mysql_result($result,$i,"username");
      date_default_timezone_set('EST');
      $time  = date("r", mysql_result($result,$i,"timestamp"));

      echo "<tr><td>$uname</td><td>$time</td><td>"
          ."<form action=\"adminprocess.php\" method=\"POST\">"
          ."<input type=\"hidden\" name=\"delbanuser\" value=\"$uname\" />"
          ."<input type=\"hidden\" name=\"subdelbanned\" value=\"1\" />"
          ."<input type=\"image\" src=\"".SITE_BASE_URL."/img/user_delete.png\" alt=\"Delete\">"
          ."</form>"
          ."</td></tr>\n";
   }

   echo "</table><br />\n";
}
   

if(!$session->isAdmin()) { /* User not an administrator, redirect to front page automatically. */
   header("Location: ".SITE_BASE_URL."/index.php");
} else { /* Administrator is viewing page, so display all forms. */
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
  <?php include_once("../include/userpanel.php"); ?>

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

   <?php include_once("../include/topmenu.php"); ?>
 
   <div id="wrap1">
    <div id="wrap2">

     <div id="topbox">
      <strong>
       <span class="hide">Currently viewing: </span>
       <a href="<?=SITE_BASE_URL?>/index.php">LibDBDatabase</a> &raquo; <a href="<?=$_SERVER['PHP_SELF']?>">Admin Center</a>
      </strong>
     </div>

     <div id="leftside">
      <?php include_once("../include/sidemenu.php"); ?>
     </div>

     <a id="main"></a>
     <div id="contentalt">
      <h1>Admin Center</h1>
      <img src="<?=SITE_BASE_URL?>/img/gravatar-admin.png" height="80" width="80" alt="Admin Gravatar" />

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />
<?
if($form->num_errors > 0) {
   echo "      <font size=\"4\" color=\"#ff0000\">"
       ."!*** Error with request, please fix ***!</font><br /><br />";
}
?>

      <table align="left" border="0" cellspacing="5" cellpadding="5">
       <tr>
        <td>
<?
/**
 * Display Users Table
 */
?>
         <h3>Users Table Contents:</h3>
         <?=$form->error("deluser");?>
         <?=$form->error("banuser");?>
         <?=displayUsers();?>
        </td>
       </tr>
       <tr>
        <td>
         <br />
<?
/**
 * Display Banned Users Table
 */
?>
         <h3>Banned Users Table Contents:</h3>
         <?=displayBannedUsers();?>
        </td>
       </tr>
       <tr>
        <td>
         <hr />
        </td>
       </tr>
      </table>

      <p class="hide"><a href="#top">Back to top</a></p>
     </div>
    </div>

    <?php include_once("../include/footer.php"); ?>

   </div>
  </div>
 </body>
</html>
<? } ?>