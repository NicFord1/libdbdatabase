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
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.form.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.validate.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.metadata.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.maskedinput.min.js" type="text/javascript"></script>

  <script type="text/javascript">
     $.metadata.setType("attr", "validate");

     $(document).ready(function() {
        $("#regzip").mask("99999?-9999");
        $("#regphone").mask("(999) 999-9999");
        // validate registration form on keyup and submit
        $("#registration").validate({
           rules: {
              reguname: {
                 required: true,
                 minlength: 5
              },
              regpass: {
                 required: true,
                 minlength: 5
              },
              regpassconf: {
                 required: true,
                 minlength: 5,
                 equalTo: "#regpass"
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
              reguname: {
                 required: "Please provide a username",
                 minlength: "Your username must be at least 5 characters long"
              },
              regpass: {
                 required: "Please provide a password",
                 minlength: "Your password must be at least 5 characters long"
              },
              regpassconf: {
                 required: "Please confirm your password",
                 minlength: "Your password must be at least 5 characters long",
                 equalTo: "Enter the same password as above"
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
            echo "<p>Thank you <b>".$_SESSION['reguname']."</b>, your information "
                ."has been registered, you may now log in on the "
                ."<a href=\"".SITE_BASE_URL."/index.php\">front page</a>.</p>";
         }
      } else { /* Registration failed */
         echo "<h1>Registration Failed</h1>";
         echo "<p>We're sorry, but an error has occurred and your registration "
             ."for the username <b>".$_SESSION['reguname']."</b>, could not be "
             ."completed.<br />Please try again at a later time.</p>";
      }
      unset($_SESSION['regsuccess']);
      unset($_SESSION['reguname']);
   } else {
   /**
    * The user has not filled out the registration form yet. Below is the page
    * with the sign-up form, the names of the input fields are important and
    * should not be changed.
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
          <label for="reguname" class="grey required">Username:</label>
         </td>
         <td>
          <input class="field" type="text" name="reguname" maxlength="30" value="<?php echo $session->form->value("reguname")?>" />
         </td>
         <td>&nbsp;<?php echo $session->form->error("reguname");?></td>
        </tr>

        <tr>
         <td>
          <label for="regpass" class="grey required">Password:</label>
         </td>
         <td>
          <input class="field" type="password" id="regpass" name="regpass" maxlength="30"<?php
   if($session->isAdmin()) {
      $randpass = $session->generateRandStr(8);
      echo " value=\"".$randpass."\" readonly";
   } else {
      echo " value=\"".$session->form->value("regpass")."\"";
   }
?> />
         </td>
         <td>&nbsp;<?php echo $session->form->error("regpass");?></td>
        </tr>

        <tr>
         <td>
          <label for="regpassconf" class="grey required">Confirm Password:</label>
         </td>
         <td>
          <input class="field" type="password" id="regpassconf" name="regpassconf" maxlength="30"<?php
   if($session->isAdmin()) {
   	echo " value=\"".$randpass."\" readonly";
   }
?> />
         </td>
         <td>&nbsp;<?php echo $session->form->error("regpassconf");?></td>
        </tr>

        <tr>
         <td>
          <label for="regemail" class="grey required">Email:</label>
         </td>
         <td>
          <input class="field" type="text" name="regemail" maxlength="96" value="<?php echo $session->form->value("regemail")?>" />
         </td>
         <td>&nbsp;<?php echo $session->form->error("regemail");?></td>
        </tr>

        <tr>
         <td>
          <label for="regfname" class="grey">Firstname:</label>
         </td>
         <td>
          <input class="field" type="text" name="regfname" maxlength="50" value="<?php echo $session->form->value("regfname")?>" />
         </td>
         <td>&nbsp;<?php echo $session->form->error("regfname");?></td>
        </tr>

        <tr>
         <td>
          <label for="reglname" class="grey">Lastname:</label>
         </td>
         <td>
          <input class="field" type="text" name="reglname" maxlength="50" value="<?php echo $session->form->value("reglname")?>" />
         </td>
         <td>&nbsp;<?php echo $session->form->error("reglname");?></td>
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
              <option value="">Month:</option>
              <option value="1"<?php if($session->form->value("regbirthmonth") == 1) echo " selected"; ?>>January</option>
              <option value="2"<?php if($session->form->value("regbirthmonth") == 2) echo " selected"; ?>>February</option>
              <option value="3"<?php if($session->form->value("regbirthmonth") == 3) echo " selected"; ?>>March</option>
              <option value="4"<?php if($session->form->value("regbirthmonth") == 4) echo " selected"; ?>>April</option>
              <option value="5"<?php if($session->form->value("regbirthmonth") == 5) echo " selected"; ?>>May</option>
              <option value="6"<?php if($session->form->value("regbirthmonth") == 6) echo " selected"; ?>>June</option>
              <option value="7"<?php if($session->form->value("regbirthmonth") == 7) echo " selected"; ?>>July</option>
              <option value="8"<?php if($session->form->value("regbirthmonth") == 8) echo " selected"; ?>>August</option>
              <option value="9"<?php if($session->form->value("regbirthmonth") == 9) echo " selected"; ?>>September</option>
              <option value="10"<?php if($session->form->value("regbirthmonth") == 10) echo " selected"; ?>>October</option>
              <option value="11"<?php if($session->form->value("regbirthmonth") == 11) echo " selected"; ?>>November</option>
              <option value="12"<?php if($session->form->value("regbirthmonth") == 12) echo " selected"; ?>>December</option>
             </select>

             <select name="regbirthday">
              <option value="">Day:</option>
<?php
      for ($i = 1; $i <= 31; $i++) {
         echo "              <option value='$i'";
         if($session->form->value("regbirthday") == $i) {
            echo " selected";
         }
         echo ">$i</option>";
      }
?>
             </select>

             <select name="regbirthyear">
              <option value="">Year:</option>
<?php
      for ($i = date("Y"); $i >= date("Y")-120; $i--) {
         echo "              <option value='$i'";
         if($session->form->value("regbirthyear") == $i) {
            echo " selected";
         }
         echo ">$i</option>";
      }
?>
             </select>
            </td>
           </tr>
          </table>
         </td>
         <td>&nbsp;<?php echo $session->form->error("regbirth");?></td>
        </tr>

        <tr>
         <td>
          <label for="reggender" class="grey">Gender:</label>
         </td>
         <td>
          <label for="male"><input type="radio" name="reggender" id="male" value="M"<?php
   if($session->form->value("reggender") == 'M') {
   	echo " checked";
   }
?> />Male</label>
          <label for="female"><input type="radio" name="reggender" id="female" value="F"<?php
   if($session->form->value("reggender") == 'F') {
   	echo " checked";
   }
?> />Female</label>
         </td>
         <td>&nbsp;<?php echo $session->form->error("reggender");?></td>
        </tr>

        <tr><td colspan="2">Address:</td></tr>
        <tr>
         <td>
          <label for="regaddrline1" class="grey">Line1:</label>
         </td>
         <td>
          <input class="field" type="text" name="regaddrline1" maxlength="80" value="<?php echo $session->form->value("regaddrline1");?>" />
         </td>
         <td>&nbsp;<?php echo $session->form->error("regaddrline1");?></td>
        </tr>

        <tr>
         <td>
          <label for="regaddrline2" class="grey">Line 2:</label>
         </td>
         <td>
          <input class="field" type="text" name="regaddrline2" maxlength="80" value="<?php echo $session->form->value("regaddrline2");?>" />
         </td>
         <td>&nbsp;<?php echo $session->form->error("regaddrline2");?></td>
        </tr>

        <tr>
         <td>
          <label for="regcity" class="grey">City:</label>
         </td>
         <td>
          <input class="field" type="text" name="regcity" maxlength="40" value="<?php echo $session->form->value("regcity");?>" />
         </td>
         <td>&nbsp;<?php echo $session->form->error("regcity");?></td>
        </tr>

        <tr>
         <td>
          <label for="regstate" class="grey">State:</label>
         </td>
         <td>
          <select name="regstate">
           <option value="">Select a State:</option>
           <option value="AL"<?php if($session->form->value("regstate") == "AL") echo " selected";?>>Alabama</option>
           <option value="AK"<?php if($session->form->value("regstate") == "AK") echo " selected";?>>Alaska</option>
           <option value="AZ"<?php if($session->form->value("regstate") == "AZ") echo " selected";?>>Arizona</option>
           <option value="AR"<?php if($session->form->value("regstate") == "AR") echo " selected";?>>Arkansas</option>
           <option value="CA"<?php if($session->form->value("regstate") == "CA") echo " selected";?>>California</option>
           <option value="CO"<?php if($session->form->value("regstate") == "CO") echo " selected";?>>Colorado</option>
           <option value="CT"<?php if($session->form->value("regstate") == "CT") echo " selected";?>>Connecticut</option>
           <option value="DC"<?php if($session->form->value("regstate") == "DC") echo " selected";?>>District of Columbia</option>
           <option value="DE"<?php if($session->form->value("regstate") == "DE") echo " selected";?>>Delaware</option>
           <option value="FL"<?php if($session->form->value("regstate") == "FL") echo " selected";?>>Florida</option>
           <option value="GA"<?php if($session->form->value("regstate") == "GA") echo " selected";?>>Georgia</option>
           <option value="HI"<?php if($session->form->value("regstate") == "HI") echo " selected";?>>Hawaii</option>
           <option value="ID"<?php if($session->form->value("regstate") == "ID") echo " selected";?>>Idaho</option>
           <option value="IL"<?php if($session->form->value("regstate") == "IL") echo " selected";?>>Illinois</option>
           <option value="IN"<?php if($session->form->value("regstate") == "IN") echo " selected";?>>Indiana</option>
           <option value="IA"<?php if($session->form->value("regstate") == "IA") echo " selected";?>>Iowa</option>
           <option value="KS"<?php if($session->form->value("regstate") == "KS") echo " selected";?>>Kansas</option>
           <option value="KY"<?php if($session->form->value("regstate") == "KY") echo " selected";?>>Kentucky</option>
           <option value="LA"<?php if($session->form->value("regstate") == "LA") echo " selected";?>>Louisiana</option>
           <option value="ME"<?php if($session->form->value("regstate") == "ME") echo " selected";?>>Maine</option>
           <option value="MD"<?php if($session->form->value("regstate") == "MD") echo " selected";?>>Maryland</option>
           <option value="MA"<?php if($session->form->value("regstate") == "MA") echo " selected";?>>Massachusetts</option>
           <option value="MI"<?php if($session->form->value("regstate") == "MI") echo " selected";?>>Michigan</option>
           <option value="MN"<?php if($session->form->value("regstate") == "MN") echo " selected";?>>Minnesota</option>
           <option value="MS"<?php if($session->form->value("regstate") == "MS") echo " selected";?>>Mississippi</option>
           <option value="MO"<?php if($session->form->value("regstate") == "MO") echo " selected";?>>Missouri</option>
           <option value="MT"<?php if($session->form->value("regstate") == "MT") echo " selected";?>>Montana</option>
           <option value="NE"<?php if($session->form->value("regstate") == "NE") echo " selected";?>>Nebraska</option>
           <option value="NV"<?php if($session->form->value("regstate") == "NV") echo " selected";?>>Nevada</option>
           <option value="NH"<?php if($session->form->value("regstate") == "NH") echo " selected";?>>New Hampshire</option>
           <option value="NJ"<?php if($session->form->value("regstate") == "NJ") echo " selected";?>>New Jersey</option>
           <option value="NM"<?php if($session->form->value("regstate") == "NM") echo " selected";?>>New Mexico</option>
           <option value="NY"<?php if($session->form->value("regstate") == "NY") echo " selected";?>>New York</option>
           <option value="NC"<?php if($session->form->value("regstate") == "NC") echo " selected";?>>North Carolina</option>
           <option value="ND"<?php if($session->form->value("regstate") == "ND") echo " selected";?>>North Dakota</option>
           <option value="OH"<?php if($session->form->value("regstate") == "OH") echo " selected";?>>Ohio</option>
           <option value="OK"<?php if($session->form->value("regstate") == "OK") echo " selected";?>>Oklahoma</option>
           <option value="OR"<?php if($session->form->value("regstate") == "OR") echo " selected";?>>Oregon</option>
           <option value="PA"<?php if($session->form->value("regstate") == "PA") echo " selected";?>>Pennsylvania</option>
           <option value="PR"<?php if($session->form->value("regstate") == "PR") echo " selected";?>>Puerto Rico</option>
           <option value="RI"<?php if($session->form->value("regstate") == "RI") echo " selected";?>>Rhode Island</option>
           <option value="SC"<?php if($session->form->value("regstate") == "SC") echo " selected";?>>South Carolina</option>
           <option value="SD"<?php if($session->form->value("regstate") == "SD") echo " selected";?>>South Dakota</option>
           <option value="TN"<?php if($session->form->value("regstate") == "TN") echo " selected";?>>Tennessee</option>
           <option value="TX"<?php if($session->form->value("regstate") == "TX") echo " selected";?>>Texas</option>
           <option value="UT"<?php if($session->form->value("regstate") == "UT") echo " selected";?>>Utah</option>
           <option value="VT"<?php if($session->form->value("regstate") == "VT") echo " selected";?>>Vermont</option>
           <option value="VA"<?php if($session->form->value("regstate") == "VA") echo " selected";?>>Virginia</option>
           <option value="WA"<?php if($session->form->value("regstate") == "WA") echo " selected";?>>Washington</option>
           <option value="WV"<?php if($session->form->value("regstate") == "WV") echo " selected";?>>West Virginia</option>
           <option value="WI"<?php if($session->form->value("regstate") == "WI") echo " selected";?>>Wisconsin</option>
           <option value="WY"<?php if($session->form->value("regstate") == "WY") echo " selected";?>>Wyoming</option>
          </select>
         </td>
         <td>&nbsp;<?php echo $session->form->error("regstate");?></td>
        </tr>

        <tr>
         <td>
          <label for="regzip" class="grey">Zip<noscript> xxxxx-oooo</noscript>:</label>
         </td>
         <td>
          <input class="field" type="text" id="regzip" name="regzip" maxlength="10" value="<?php echo $session->form->value("regzip");?>" />
         </td>
         <td>&nbsp;<?php echo $session->form->error("regzip");?></td>
        </tr>

        <tr>
         <td>
          <label for="regphone" class="grey">Phone<noscript> (xxx) xxx-xxxx</noscript>:</label>
         </td>
         <td>
          <input class="field" type="text" id="regphone" name="regphone" maxlength="26" value="<?php echo $session->form->value("regphone");?>" />
         </td>
         <td>&nbsp;<?php echo $session->form->error("regphone");?></td>
        </tr>

<?php
      if($session->isAdmin()) { /* Allow Admins to create different types of users. */
?>
        <tr>
         <td>
          <label class="grey">Usertype:</label>
         </td>
         <td>
          <select name="regutype">
           <option value="<?php echo CUST; ?>"<?php
         if($session->form->value("regutype") == CUST) {
            echo " selected";
         }
?>>Customer</option>
           <option value="<?php echo TELLER; ?>"<?php
         if($session->form->value("regutype") == TELLER) {
            echo " selected";
         }
?>>Teller</option>
           <option value="<?php echo ADMIN; ?>"<?php
         if($session->form->value("regutype") == ADMIN) {
            echo " selected";
         }
?>>Administrator</option>
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