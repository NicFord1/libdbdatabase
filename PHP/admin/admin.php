<?php
/**
 * admin.php
 *
 * This is the Admin Center page. Only administrators are allowed to view this page. This page displays the
 * database table of users and banned users. Admins can choose to delete specific users, delete inactive users,
 * ban users, edit user accounts, etc.
 */
require_once("../include/session.php");
require_once("../include/users.php");

/**
 * displayUsers - Displays the users database table in a nicely formatted html table.
 */
function displayUsers() {
   global $database;

   $q = "SELECT uid,username,regtime,lastvisit FROM ".DB_TBL_CUSTOMERS." ORDER BY regtime ASC,username";
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
   echo "<table id=\"users\" align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">\n";
   echo "<tr>\n";
   echo "<th>Username</th>\n";
   echo "<th width=\"85px\">User Type</th>\n";
   echo "<th width=\"200px\">Registration Time</th>\n";
   echo "<th width=\"200px\">Last Active</th>\n";
   echo "<th width=\"50px\">Actions</th>\n";
   echo "</tr>\n";

   while($usersinfo = mysql_fetch_array($result, MYSQL_ASSOC)) {
   	$user = NULL;

      if($database->confirmUID($usersinfo['uid'], DB_TBL_ADMINS)) {
         $qry = "SELECT * FROM ".DB_TBL_ADMINS." WHERE uid='".$usersinfo['uid']."'";
         $qresult = $database->query($qry);
         $empsinfo = mysql_fetch_array($qresult, MYSQL_ASSOC);
      	$user = new Administrator($usersinfo);
      } else if ($database->confirmUID($usersinfo['uid'], DB_TBL_TELLERS)) {
         $qry = "SELECT * FROM ".DB_TBL_TELLERS." WHERE uid='".$usersinfo['uid']."'";
         $qresult = $database->query($qry);
         $empsinfo = mysql_fetch_array($qresult, MYSQL_ASSOC);
      	$user = new Teller($usersinfo);
      } else {
      	$user = new Customer($usersinfo);
      }

      date_default_timezone_set('EST');
      $rtime = date("r", $user->getRegistrationTime());
      $lvtime = $user->getLastVisit();
      if($lvtime) {
         $lvtime = date("r", $lvtime);
      } else {
         $lvtime = "Never";
      }

		echo "<tr><td align=\"center\">".$user->username."</td><td align=\"center\">".get_class($user)."</td>"
		    ."<td align=\"center\">".$rtime."</td><td align=\"center\">".$lvtime."</td>"
		    ."<td><a href=\"userinfo.php?uid=".$user->getUID()."\">"
          ."<img class=\"clearimg\" src=\"".SITE_BASE_URL."/img/user_edit.png\" alt=\"Edit\" /></a> "
          ."<a href=\"userhistory.php?uid=".$user->getUID()."\">"
          ."<img class=\"clearimg\" src=\"".SITE_BASE_URL."/img/user_history.png\" alt=\"History\" /></a> "

		    ."<form class=\"actions\" action=\"adminprocess.php\" method=\"POST\">"
			 ."<input type=\"hidden\" name=\"deluser\" value=\"".$user->getUID()."\" />"
			 ."<input type=\"hidden\" name=\"subdeluser\" value=\"1\" />"
			 ."<input type=\"image\" src=\"".SITE_BASE_URL."/img/user_delete.png\" alt=\"Delete\">"
			 ."</form>"

			 ."<form class=\"actions\" action=\"adminprocess.php\" method=\"POST\">"
			 ."<input type=\"hidden\" name=\"banuser\" value=\"".$user->username."\" />"
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
   echo "<table id=\"banned\" align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
   echo "<tr>\n";
   echo "<th>Username</th>\n";
   echo "<th>Time Banned</th>\n";
   echo "<th>Actions</th>\n";
   echo "</tr>\n";

   while($usersinfo = mysql_fetch_array($result, MYSQL_ASSOC)) {
      $uname = $usersinfo['username'];
      date_default_timezone_set('EST');
      $time  = date("r", $usersinfo['timestamp']);

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
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.validate.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.metadata.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.maskedinput.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.tablehover.min.js" type="text/javascript"></script>

  <script type="text/javascript">
     $(document).ready(function() {
        $('#users').tableHover();
        $('#banned').tableHover();
     });
  </script>


  <!-- Sliding effect -->
  <script src="<?php echo SITE_BASE_URL?>/js/slide.js" type="text/javascript"></script>
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
       <a href="<?php echo SITE_BASE_URL?>/index.php">LibDBDatabase</a> &raquo; <a href="<?php echo $_SERVER['PHP_SELF']?>">Admin Center</a>
      </strong>
     </div>

     <div id="leftside">
      <?php include_once("../include/sidemenu.php"); ?>
     </div>

     <a id="main"></a>
     <div id="contentalt">
      <h1>Admin Center</h1>
      <img src="<?php echo SITE_BASE_URL?>/img/gravatar-admin.png" height="80" width="80" alt="Admin Gravatar" />

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />
<?php
if($session->form->num_errors > 0) {
   echo "      <font size=\"4\" color=\"#ff0000\">"
       ."!*** Error with request, please fix ***!</font><br /><br />";
}
?>

      <table align="center" border="0" cellspacing="5" cellpadding="5" width="100%">
       <tr>
        <td>
<?php
/**
 * Display Users Table
 */
?>
         <h3>Users Table Contents:</h3>
         <?php echo $session->form->error("deluser");?>
         <?php echo $session->form->error("banuser");?>
         <?php echo displayUsers();?>
        </td>
       </tr>
       <tr>
        <td>
         <br />
<?php
/**
 * Display Banned Users Table
 */
?>
         <h3>Banned Users Table Contents:</h3>
         <?php echo displayBannedUsers();?>
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
<?php } ?>