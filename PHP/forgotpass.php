<?php
/**
 * forgotpass.php
 *
 * This page is for those users who have forgotten their password and want to have
 * a new password generated for them and sent to the email address attached to their
 * account in the database. The new password is not displayed on the website for
 * security purposes.
 *
 * Note: If your server is not properly setup to send mail, then this page is
 * essentially useless and it would be better to not even link to this page from
 * your website.
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
       <a href="<?php echo SITE_BASE_URL?>/index.php">LibDBDatabase</a> &raquo;
       <a href="<?php echo $_SERVER['PHP_SELF']?>">Forgotten Password Center</a>
      </strong>
     </div>

     <div id="leftside">
      <?php include_once("include/sidemenu.php"); ?>
     </div>

     <a id="main"></a>
     <div id="contentalt">
      <h1>Forgotten Password Center</h1>
      <img src="<?php echo SITE_BASE_URL?>/img/gravatar-help.png" height="80" width="80" alt="Help Gravatar" />

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />
<?php
/**
 * Forgot Password form has been submitted and no errors were found with the
 * form (the username is in the database)
 */
if(isset($_SESSION['forgotpass'])) {
   if($_SESSION['forgotpass']) { /* New password generated for user and sent via email */
      echo "<h1>New Password Generated</h1>";
      echo "<p>Your new password has been generated and sent to the email <br />"
          ."associated with your account.</p>";
   } else { /* Email couldn't be sent, thus password not edited in the database. */
      echo "<h1>New Password Failure</h1>";
      echo "<p>There was an error sending you the email with the new password,<br />"
          ."so your password has not been changed.</p>";
   }
   unset($_SESSION['forgotpass']);
} else { /* Forgot password form is displayed, if error found it is displayed. */
?>

      <h1>Forgot Password</h1>
      A new password will be generated for you and sent to the email address<br />
      associated with your account, all you have to do is enter your username.<br /><br />
      <?php echo $session->form->error("user")?>
      <form action="process.php" method="POST">
       <b>Username:</b> <input type="text" name="user" maxlength="30" value="<?php echo $session->form->value("user")?>">
       <input type="hidden" name="subforgot" value="1">
       <input type="submit" value="Get New Password">
      </form>
<?php
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