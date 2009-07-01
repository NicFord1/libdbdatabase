<?php
/**
 * index.php
 */
require_once("include/session.php");

/**
 * displayItems - Displays the items database table in a nicely formatted html
 * table.
 */
function displayItems() {
   global $database;

   echo "<h1>Items in Our Library</h1>\n";

   $q = "SELECT * FROM ldb_items ORDER BY itemtype ASC,itemid";
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
   echo "<tr><td><b>itemID</b></td><td><b>Item Type</b></td><td><b>Qty on Hand</b></td></tr>\n";

   while($item = mysql_fetch_array($result, MYSQL_ASSOC)) {
      if($item['itemtype'] == "BOOK") {
         $qry = "SELECT * FROM ".DB_TBL_PRFX."books WHERE itemid='".$item['itemid']."'";
         $qresult = $database->query($qry);
         $iteminfo = mysql_fetch_array($qresult, MYSQL_ASSOC);
      } else if ($item['itemtype'] == "PERIODICAL") {
         $qry = "SELECT * FROM ".DB_TBL_PRFX."periodicals WHERE itemid='".$item['itemid']."'";
         $qresult = $database->query($qry);
         $iteminfo = mysql_fetch_array($qresult, MYSQL_ASSOC);
      } else if ($item['itemtype'] == "DVD") {
         $qry = "SELECT * FROM ".DB_TBL_PRFX."dvds WHERE itemid='".$item['itemid']."'";
         $qresult = $database->query($qry);
         $iteminfo = mysql_fetch_array($qresult, MYSQL_ASSOC);
      } else if ($item['itemtype'] == "CD") {
         $qry = "SELECT * FROM ".DB_TBL_PRFX."cds WHERE itemid='".$item['itemid']."'";
         $qresult = $database->query($qry);
         $iteminfo = mysql_fetch_array($qresult, MYSQL_ASSOC);
      } else {
         $iteminfo = NULL;
      }

      echo "<tr><td>".$item['itemid']."</td><td>".$iteminfo['title']."</td>"
          ."<td>".$item['quantity']."</td></tr>\n";
   }
   echo "</table><br />\n";
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

  <!-- stylesheets -->
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
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.form.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.validate.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.metadata.js" type="text/javascript"></script>

  <script type="text/javascript">
     $.metadata.setType("attr", "validate");

     $(document).ready(function() {
        // validate login form on keyup and submit
        $("#login").validate({
           rules: {
              uname: {
                 required: true,
                 minlength: 5
              },
              pass: {
                 required: true,
                 minlength: 5
              }
           },
           messages: {
              uname: {
                 required: "Please provide a username",
                 minlength: "Your username must be at least 5 characters long"
              },
              pass: {
                 required: "Please provide a password",
                 minlength: "Your password must be at least 5 characters long"
              }
           }
        });
     });
  </script>


  <!-- Sliding effect -->
  <script src="<?php echo SITE_BASE_URL?>/js/slide.js" type="text/javascript"></script>
 </head>

 <body>

  <!-- Sliding Panel -->
  <div id="toppanel">

<?php
/**
 * User has already logged in, so display relavent links, including a link to
 * the admin center if the user is an administrator.
 */
if(!$session->logged_in) {
?>
   <div id="panel">
    <div class="content clearfix">

     <div class="left">
      <a name="toppanel"></a>
      <h1>Welcome to LibDBDatabase</h1>
      <h2>Library Database System</h2>
      <p class="grey">
       We are here to provide a wealth of information and entertainment to our
       members!  With over 1 million titles in our collection, if we don't have
       what you're looking for, we'll get a copy.
      </p>
      <div id="result"></div>
<?php
   if($session->form->num_errors > 0) {
      echo "<font size=\"2\" color=\"#ff0000\">".$session->form->num_errors." error(s) found</font>\n";

      //login errors
      if($session->form->value("sublogin") == "1") {
         echo "<p>Login Credentials:</p>\n";
         echo "<label for=\"uname\">".$session->form->error("uname")."</label>\n";
         echo "<label for=\"pass\">".$session->form->error("pass")."</label>\n";
      }
   }
?>
     </div>

     <div class="left">
      <!-- Login Form -->
      <form class="clearfix" action="<?php echo SITE_BASE_URL; ?>/process.php" method="post" id="login">
       <h1>Member Login</h1>

       <label class="grey required" for="uname">Username:</label>
       <input class="field" type="text" name="uname" id="uname" value="<?php echo $session->form->value("uname")?>" size="23" />

       <label class="grey required" for="pass">Password:</label>
       <input class="field" type="password" name="pass" id="pass" value="" size="23" />

       <label>
        <input name="remember" id="remember" type="checkbox" checked="checked" value="forever" />
        &nbsp;Remember me
       </label>

       <div class="clear"></div>
       <input type="hidden" name="sublogin" value="1">
       <input type="submit" name="submit" value="Login" class="bt_login" />
       <a class="lost-pass" href="<?php echo SITE_BASE_URL; ?>/forgotpass.php">Lost your password?</a>
      </form>
     </div>

     <div class="left right">
      <h1>Not a member yet?  Sign-up!</h1>
      <p>
       We welcome new members to our library.  All we ask is that you provide
       some details so that we can get you started with a new account.  Sign-up
       <a href="<?php echo SITE_BASE_URL; ?>/newuser.php">here</a>!
      </p>
     </div>
    </div>
   </div>

   <div class="tab">
    <ul class="login">
     <li class="left">&nbsp;</li>
     <li>Hello <?php echo $session->user->username; ?>!</li>
     <li class="sep">|</li>
     <li id="toggle">
      <a id="open" class="open" href="#">Log In | Register</a>
      <a id="close" style="display: none;" class="close" href="#">Close Panel</a>
     </li>
     <li class="right">&nbsp;</li>
    </ul>
   </div>
<?php
} else {
/**
 * User has already logged in, so display relavent links, including
 * a link to the admin center if the user is an administrator.
 */
?>
   <div class="tab">
    <ul class="login">
     <li class="left">&nbsp;</li>
     <li>Welcome <?php echo $session->user->username; ?>!</li>
     <li class="sep">|</li>
     <li><a href="<?php echo SITE_BASE_URL; ?>/process.php?logout">Logout</a></li>
     <li class="right">&nbsp;</li>
    </ul>
   </div>
<?php
}
?>
  </div>
  <!-- /Sliding Panel -->

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
       <a href="<?php echo SITE_BASE_URL?>/index.php">LibDBDatabase</a> &raquo;
       <a href="<?php echo $_SERVER['PHP_SELF']?>">Front page</a>
      </strong>
     </div>

     <div id="leftside">
<?php
if($session->form->num_errors > 0) {
   echo "      <strong>";
   echo "       <font size=\"2\" color=\"#ff0000\">Login failed.</font>\n";
   echo "      </strong>";
   echo "      <br />";
}
?>
      <?php include_once("include/sidemenu.php"); ?>
     </div>

     <a id="main"></a>
     <div id="contentalt">
      <h1>Welcome to "LibDBDatabase"...</h1>
      <img src="<?php echo SITE_BASE_URL?>/img/gravatar-books.png" height="80" width="80" alt="Gravatar example" />

      <p class="intro">
       ...an open source library database app by a group of UMBC students. This
       was created for a project in CMSC 432 during the Summer of 2009 semester.
      </p>

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />

      <?php displayItems(); ?>
      <p class="hide"><a href="#top">Back to top</a></p>
     </div>
    </div>

    <?php include_once("include/footer.php"); ?>

   </div>
  </div>
 </body>
</html>