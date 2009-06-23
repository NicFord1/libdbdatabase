<?
/**
 * index.php
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

  <!-- stylesheets -->
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

  <script type="text/javascript">
     $.metadata.setType("attr", "validate");

     $(document).ready(function() {
        // validate login form on keyup and submit
        $("#login").validate({
           rules: {
              user: {
                 required: true,
                 minlength: 5
              },
              pass: {
                 required: true,
                 minlength: 5
              }
           },
           messages: {
              user: {
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
  <script src="<?=SITE_BASE_URL?>/js/slide.js" type="text/javascript"></script>
 </head>

 <body>

  <!-- Sliding Panel -->
  <div id="toppanel">

<?php
/**
 * User has already logged in, so display relavent links, including
 * a link to the admin center if the user is an administrator.
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
       We are here to provide a wealth of information and entertainment to our members!  With over 1 million
       titles in our collection, if we don't have what you're looking for, we'll get a copy.
      </p>

<?php
   if($session->form->num_errors > 0) {
      echo "      <font size=\"2\" color=\"#ff0000\">".$session->form->num_errors." error(s) found</font>\n";

      //login errors
      if($session->form->value("sublogin") == "1") {
         echo "      <p>Login Credentials:</p>\n";
         echo "      <label for=\"user\">".$session->form->error("user")."</label>\n";
         echo "      <label for=\"pass\">".$session->form->error("pass")."</label>\n";
      }
   }
?>
     </div>

     <div class="left">
      <!-- Login Form -->
      <form class="clearfix" action="process.php" method="post" id="login">
       <h1>Member Login</h1>

       <label class="grey required" for="user">Username:</label>
       <input class="field" type="text" name="user" id="user" value="<?=$session->form->value("user")?>" size="23" />

       <label class="grey required" for="pass">Password:</label>
       <input class="field" type="password" name="pass" id="pass" value="" size="23" />

       <label>
        <input name="remember" id="remember" type="checkbox" checked="checked" value="forever" />
        &nbsp;Remember me
       </label>

       <div class="clear"></div>
       <input type="hidden" name="sublogin" value="1">
       <input type="submit" name="submit" value="Login" class="bt_login" />
       <a class="lost-pass" href="<?=SITE_BASE_URL?>/forgotpass.php">Lost your password?</a>
      </form>
     </div>

     <div class="left right">
      <h1>Not a member yet?  Sign-up!</h1>
      <p>
       We welcome new members to our library.  All we ask is that you provide some details so that we can
       get you started with a new account.  Sign-up <a href="<?=SITE_BASE_URL?>/newuser.php">here</a>!
      </p>
     </div>
    </div>
   </div>

   <div class="tab">
    <ul class="login">
     <li class="left">&nbsp;</li>
     <li>Hello <?=$session->username?>!</li>
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
     <li>Welcome <?=$session->username?>!</li>
     <li class="sep">|</li>
     <li><a href="<?=SITE_BASE_URL?>/process.php?logout">Logout</a></li>
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
       <a href="<?=SITE_BASE_URL?>/index.php">LibDBDatabase</a> &raquo;
       <a href="<?=$_SERVER['PHP_SELF']?>">Front page</a>
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

     <div id="rightside">
      <h1>Template info:</h1>
      <p>
       andreas06 is built with valid XHTML 1.1 and CSS2. It conforms to section 508 and a WCAG 1.0 AAA rating.
       It has full support for browser-based font-resizing, 100% readable also even in text-based browsers.
      </p>
      <p>/<strong>Andreas</strong></p>

      <h1>Links:</h1>
      <p>
       <a href="http://andreasviklund.com">My website</a><br />
       <a href="http://andreasviklund.com/templates">Free templates</a><br />
       <a href="http://baygroove.com">Baygroove.com</a><br />
       <a href="http://openwebdesign.org">Open Web Design</a><br />
       <a href="http://oswd.org">OSWD.org</a><br />
       <a href="http://www.solucija.com">sNews CMS</a><br />
      </p>
      <p class="smallcaps">andreas06 v1.2 <br /> (Nov 28, 2005)</p>
     </div>

     <a id="main"></a>
     <div id="content">
      <h1>Welcome to "LibDBDatabase"...</h1>
      <img src="<?=SITE_BASE_URL?>/img/gravatar-books.png" height="80" width="80" alt="Gravatar example" />

      <p class="intro">
       ...an open source library database app by a group of UMBC students. This was created for a project
       in CMSC 432 during the Summer of 2009 semester.
      </p>

      <p>
       Like in my other templates, the extra features are all built into the stylesheet. The simple structure
       of the code (all content is separated from the presentation) makes it easy to customize the look and
       feel of the design, and you get several layouts to choose from in the download zip. Click the menu tabs
       to view the examples.
      </p>

      <p>
       The design is inspired by the colors of the fall, since the template was created as an entry in the
       <a href="http://openwebdesign.org">Open Web Design</a> "fall/autumn" competition in October 2005
       (where it as awarded 1st place). The colors are picked from a photo of the local park, taken on Oct 1st.
      </p>

      <h2>Open source design</h2>
      <p>
       This template is released as open source, which means that you are free to use it in any way you may want
       to. If you like this design, you can download my other designs directly from
       <a href="http://andreasviklund.com">my website</a> (where you can also find a WordPress version of this
       theme) or from Open Web Design. Comments, questions and suggestions are always very welcome!
      </p>
      <p class="hide"><a href="#top">Back to top</a></p>
     </div>
    </div>

    <?php include_once("include/footer.php"); ?>

   </div>
  </div>
 </body>
</html>