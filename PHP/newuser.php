<?php
/**
 * newuser.php
 *
 * This is the Registration Center page. Only administrators & tellers are allowed to view this page while logged in.
 * This page allows for the creation of new user accounts.  Only admins can create new admins & tellers.  All other users
 * can only create regular customers.
 */
include("include/session.php");


/**
 * User logged in and not an administrator or teller, redirect to main page automatically.
 */
if($session->logged_in && !($session->isAdmin() || $session->isTeller())) {
   header("Location: ".SITE_BASE_URL."/index.php");
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

  <script type="text/javascript">
     $.metadata.setType("attr", "validate");

     $(document).ready(function() {
        // validate registration form on keyup and submit
        $("#registration").validate({
           rules: {
              reguser: {
                 required: true,
                 minlength: 5
              },
              regpass: {
                 required: true,
                 minlength: 5
              },
              regemail: {
                 required: true,
                 email: true
              },
              regbirthmonth: {
                 required: true
              },
              regbirthday: {
                 required: true
              },
              regbirthyear: {
                 required: true
              }
           },
           messages: {
              reguser: {
                 required: "Please provide a username",
                 minlength: "Your username must be at least 5 characters long"
              },
              regpass: {
                 required: "Please provide a password",
                 minlength: "Your password must be at least 5 characters long"
              },
              regemail: "Please enter a valid email address",
              regbirthmonth: "Please select your birth month",
              regbirthday: "Please select your birth day",
              regbirthyear: "Please select your birth year"
           }
        });
     });
</script>


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
       <a href="<?php echo $_SERVER['PHP_SELF']?>">Registration Center</a>
      </strong>
     </div>

     <div id="leftside">
      <?php include_once("include/sidemenu.php"); ?>
     </div>

     <a id="main"></a>
     <div id="contentalt">
      <h1>Registration Center</h1>
      <img src="<?php echo SITE_BASE_URL?>/img/gravatar-newuser.png" height="80" width="80" alt="New User Gravatar" />

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />
<?php
if($session->form->num_errors > 0) {
   echo "      <font size=\"2\" color=\"#ff0000\">".$session->form->num_errors." error(s) found</font>\n";

   //registration errors
   if($session->form->value("subreg") == "1") {
      echo "      <p>Registration:</p>\n";
      echo "      <label for=\"reguser\">".$session->form->error("reguser")."</label>\n";
      echo "      <label for=\"regpass\">".$session->form->error("regpass")."</label>\n";
      echo "      <label for=\"regemail\">".$session->form->error("regemail")."</label>\n";
      echo "      <label for=\"regname\">".$session->form->error("regname")."</label>\n";
      echo "      <label for=\"regbirth\">".$session->form->error("regbirth")."</label>\n";
      echo "      <label for=\"regsex\">".$session->form->error("regsex")."</label>\n";
      echo "      <label for=\"regaddr\">".$session->form->error("regaddr")."</label>\n";
      echo "      <label for=\"regphone\">".$session->form->error("regphone")."</label>\n";
   }
}
?>

<?php
   /**
    * The user has submitted the registration form and the
    * results have been processed.
    */
   if(isset($_SESSION['regsuccess'])) {
      /* Registration was successful */
      if($_SESSION['regsuccess']) {
         echo "<h1>Registered!</h1>";
         if($session->logged_in) {
            echo "<p>".$_SESSION['reguname']."</b> has been added to the database, "
                ."they may now log in.</p>";
         } else {
            echo "<p>Thank you <b>".$_SESSION['reguname']."</b>, your information has been added to the database, "
                ."you may now log in on the <a href=\"".SITE_BASE_URL."/index.php\">front page</a>.</p>";
         }
      } else { /* Registration failed */
         echo "<h1>Registration Failed</h1>";
         echo "<p>We're sorry, but an error has occurred and your registration for the username <b>".$_SESSION['reguname']."</b>, "
             ."could not be completed.<br>Please try again at a later time.</p>";
      }
      unset($_SESSION['regsuccess']);
      unset($_SESSION['reguname']);
   } else {
   /**
    * The user has not filled out the registration form yet. Below is the page with the sign-up
    * form, the names of the input fields are important and should not be changed.
    */
      if($session->logged_in) {
         echo "<h1>Add a New Member to Our Library</h1>";
      } else {
         echo "<h1>Not a member yet? Sign Up!</h1>";
      }
?>

      <form action="process.php" method="POST" id="registration">
       <table align="left" border="0" cellspacing="0" cellpadding="3">
        <tr>
         <td>
          <label for="reguser" class="grey required">Username:</label>
         </td>
         <td>
          <input class="field" type="text" name="reguser" maxlength="30" value="<?php echo $session->form->value("reguser")?>" />
         </td>
        </tr>

        <tr>
         <td>
          <label for="regpass" class="grey required">Password:</label>
         </td>
         <td>
          <input class="field" type="password" name="regpass" maxlength="30" value="<?php echo $session->form->value("regpass")?>" />
         </td>
        </tr>

        <tr>
         <td>
          <label for="regemail" class="grey required">Email:</label>
         </td>
         <td>
          <input class="field" type="text" name="regemail" maxlength="96" value="<?php echo $session->form->value("regemail")?>" />
         </td>
        </tr>

        <tr>
         <td>
          <label for="regname" class="grey">Fullname:</label>
         </td>
         <td>
          <input class="field" type="text" name="regname" maxlength="50" value="<?php echo $session->form->value("regname")?>" />
         </td>
        </tr>

        <tr>
         <td>
          <label class="grey required">Birthdate:</label>
         </td>
         <td>
          <table>
           <tr>
            <td>
             <select name="regbirthmonth">
              <option value=""></option>
              <option value="1">January</option>
              <option value="2">February</option>
              <option value="3">March</option>
              <option value="4">April</option>
              <option value="5">May</option>
              <option value="6">June</option>
              <option value="7">July</option>
              <option value="8">August</option>
              <option value="9">September</option>
              <option value="10">October</option>
              <option value="11">November</option>
              <option value="12">December</option>
             </select>

             <select name="regbirthday">
              <option value=""></option>
<?php
      for ($i = 1; $i <= 31; $i++) {
         echo "              <option value='$i'>$i</option>";
      }
?>
             </select>

             <select name="regbirthyear">
              <option value=""></option>
<?php
      for ($i = date("Y"); $i >= date("Y")-120; $i--) {
         echo "              <option value='$i'>$i</option>";
      }
?>
             </select>
            </td>
           </tr>
          </table>
         </td>
        </tr>

        <tr>
         <td>
          <label for="regsex" class="grey">Sex:</label>
         </td>
         <td>
          <label for="male"><input type="radio" name="regsex" id="male" value="M" />Male</label>
          <label for="female"><input type="radio" name="regsex" id="female" value="F" />Female</label>
         </td>
        </tr>

        <tr>
         <td>
          <label for="regaddr" class="grey">Address:</label>
         </td>
         <td>
          <input class="field" type="text" name="regaddr" maxlength="160" value="<?php echo $session->form->value("regaddr");?>" />
         </td>
        </tr>

        <tr>
         <td>
          <label for="regphone" class="grey">Phone:</label>
         </td>
         <td>
          <input class="field" type="text" name="regphone" maxlength="26" value="<?php echo $session->form->value("regphone");?>" />
         </td>
        </tr>

<?php
      if($session->isAdmin()) { /* Allow Admins to create different types of users. */
?>
        <tr>
         <td>
          <label class="grey">User Level:</label>
         </td>
         <td>
          <select name="regulevel">
           <option value="<?php echo CUST_LEVEL?>">Customer</option>
           <option value="<?php echo TELLER_LEVEL?>">Teller</option>
           <option value="<?php echo ADMIN_LEVEL?>">Administrator</option>
          </select>
         </td>
        </tr>
<?php
      }
?>

        <tr>
         <td colspan="2" align="right">
          <input type="hidden" name="subreg" value="1">
          <input type="submit" value="Register!" class="bt_register">
         </td>
        </tr>

       </table>
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