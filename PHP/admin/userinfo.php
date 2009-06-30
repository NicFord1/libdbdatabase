<?php
/**
 * userinfo.php
 *
 * This is the User Center page. Only administrators & tellers are allowed to view this page while logged in.
 * This page allows for the creation of new user accounts.  Only admins can create new admins & tellers.  All
 * other users can only create regular customers.
 */
require_once("../include/session.php");

if(!$session->logged_in) {
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
  <link rel="stylesheet" href="<?php echo SITE_BASE_URL; ?>/css/andreas06.css" type="text/css" media="screen,projection" />
  <link rel="stylesheet" href="<?php echo SITE_BASE_URL; ?>/css/slide.css"  type="text/css" media="screen,projection" />
  <link rel="stylesheet" href="<?php echo SITE_BASE_URL; ?>/css/validate.css"  type="text/css" media="screen,projection" />

  <!-- javascripts -->
  <!-- PNG FIX for IE6 -->
  <!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
  <!--[if lte IE 6]>
   <script type="text/javascript" src="<?php echo SITE_BASE_URL; ?>/js/pngfix/supersleight-min.js"></script>
  <![endif]-->

  <!-- jQuery -->
  <script src="<?php echo SITE_BASE_URL; ?>/js/jquery.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL; ?>/js/jquery.validate.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL; ?>/js/jquery.metadata.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL; ?>/js/jquery.maskedinput.min.js" type="text/javascript"></script>

  <script type="text/javascript">
     $.metadata.setType("attr", "validate");

     $(document).ready(function() {
     	  $("#edzip").mask("99999?-9999");
        $("#edphone").mask("(999) 999-9999");
        // validate edit form on keyup and submit
        $("#edituser").validate({
           rules: {
              edemail: {
                 required: true,
                 email: true
              },
              edbirthmonth: {
                 required: true
              },
              edbirthday: {
                 required: true
              },
              edbirthyear: {
                 required: true
              }
           },
           messages: {
              edemail: "Please enter a valid email address",
              edbirthmonth: "Please select a birth month",
              edbirthday: "Please select a birth day",
              edbirthyear: "Please select a birth year"
           }
        });
     });
</script>


  <!-- Sliding effect -->
  <script src="<?php echo SITE_BASE_URL; ?>/js/slide.js" type="text/javascript"></script>

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
       <a href="<?php echo SITE_BASE_URL; ?>/index.php">LibDBDatabase</a> &raquo;
       <a href="<?php echo SITE_BASE_URL; ?>/admin/admin.php">Admin Center</a> &raquo;
       <a href="<?php echo $_SERVER['PHP_SELF']; ?>?uid=<?php echo $_GET['uid']; ?>">User Center</a>
      </strong>
     </div>

     <div id="leftside">
      <?php include_once("../include/sidemenu.php"); ?>
     </div>

     <a id="main"></a>
     <div id="contentalt">
      <h1>User Center</h1>
      <img src="<?php echo SITE_BASE_URL; ?>/img/gravatar-edituser.png" height="80" width="80" alt="Edit User Gravatar" />

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />
<?php
   if(!isset($_GET['uid']) && !isset($_SESSION['edituser'])) {
      echo "<font size=\"2\" color=\"#ff0000\">Target user not identified.</font>\n";
   } else if($_GET['uid'] && !$database->confirmUID($_GET['uid'], DB_TBL_CUSTOMERS)) {
	  echo "<font size=\"2\" color=\"#ff0000\">Target user does not exist.</font>\n";
   } else {
      if($_GET['uid']) {
         $subuserinfo = $database->getUserInfo($_GET['uid'], DB_TBL_CUSTOMERS);
      } else {
         $subuserinfo = $database->getUserInfo($session->form->value("eduid"), DB_TBL_CUSTOMERS);
      }

      if($database->confirmUID($subuserinfo['uid'], DB_TBL_TELLERS)) {
         $subempinfo = $database->getUserInfo($subuserinfo['uid'], DB_TBL_TELLERS);
         $subuserinfo['usertype'] = TELLER;
      } else if($database->confirmUID($subuserinfo['uid'], DB_TBL_ADMINS)) {
         $subempinfo = $database->getUserInfo($subuserinfo['uid'], DB_TBL_ADMINS);
         $subuserinfo['usertype'] = ADMIN;
	  }

      if($session->form->num_errors > 0) {
         echo "<font size=\"2\" color=\"#ff0000\">".$session->form->num_errors." error(s) found</font>\n";
      }

      /* The user has submitted form without errors and user's account has been updated. */
      if(isset($_SESSION['edituser'])) {
         unset($_SESSION['edituser']);

         echo "<h1>User Account Edit Success!</h1>";
         echo "<p><b>".$subuserinfo['username']."</b>'s account has been successfully updated.</p>";
      }
?>
      <h1>Update <?php echo $subuserinfo['username'];?>'s Account</h1>
      <form action="adminprocess.php" method="POST" id="edituser">
       <input type="hidden" name="uid" value="<?php echo $subuserinfo['uid'];?>" />
       <input type="hidden" name="username" value="<?php echo $subuserinfo['username'];?>" />

<?php
      if(isset($subempinfo)) {
?>
       <input type="hidden" name="edhiredate" value="<?php
         if($session->form->value("edhiredate") == "") {
            echo $subempinfo['hiredate'];
         } else {
            echo $session->form->value("edhiredate");
         }
?>" />
<?php
      }
?>

       <table align="left" border="0" cellspacing="0" cellpadding="3">
        <tr>
         <td>
          <label for="newpass" class="grey">New Password:</label>
         </td>
         <td>
          <input type="password" name="newpass" maxlength="30" />
         </td>
         <td><?php echo $session->form->error("newpass")?></td>
        </tr>

        <tr>
         <td>
          <label for="newpassconf" class="grey">Confirm New Password:</label>
         </td>
         <td>
          <input type="password" name="newpassconf" maxlength="30" />
         </td>
         <td><?php echo $session->form->error("newpassconf")?></td>
        </tr>

        <tr>
         <td>
          <label for="edemail" class="grey required">Email:</label>
         </td>
         <td>
          <input class="field" type="text" name="edemail" maxlength="96" value="<?php
      if($session->form->value("edemail") == "") {
         echo $subuserinfo['email'];
      } else {
         echo $session->form->value("edemail");
      }
?>" />
         </td>
         <td><?php echo $session->form->error("edemail")?></td>
        </tr>

        <tr>
         <td>
          <label for="edfname" class="grey">First Name:</label>
         </td>
         <td>
          <input class="field" type="text" name="edfname" maxlength="50" value="<?php
      if($session->form->value("edfname") == "") {
         echo $subuserinfo['firstname'];
      } else {
         echo $session->form->value("edfname");
      }
?>" />
         </td>
         <td><?php echo $session->form->error("edfname")?></td>
        </tr>

        <tr>
         <td>
          <label for="edlname" class="grey">Last Name:</label>
         </td>
         <td>
          <input class="field" type="text" name="edlname" maxlength="50" value="<?php
      if($session->form->value("edlname") == "") {
         echo $subuserinfo['lastname'];
      } else {
         echo $session->form->value("edlname");
      }
?>" />
         </td>
         <td><?php echo $session->form->error("edlname")?></td>
        </tr>

        <tr>
         <td>
          <label class="grey required">Birthdate:</label>
<?php
      if($session->form->value("edbirthmonth") == "") {
         $subbirthmonth = date("n", $subuserinfo['birthdate']);
      } else {
         $subbirthmonth = $session->form->value("edbirthmonth");
      }
      if($session->form->value("edbirthday") == "") {
         $subbirthday = date("j", $subuserinfo['birthdate']);
      } else {
         $subbirthday = $session->form->value("edbirthday");
      }
      if($session->form->value("edbirthyear") == "") {
         $subbirthyear = date("o", $subuserinfo['birthdate']);
      } else {
         $subbirthyear = $session->form->value("edbirthyear");
      }
?>
         </td>
         <td>
          <table>
           <tr>
            <td>
             <select name="edbirthmonth">
              <option value=""></option>
              <option value="1"<?php echo ($subbirthmonth==1 ? ' selected' : '')?>>January</option>
              <option value="2"<?php echo ($subbirthmonth==2 ? ' selected' : '')?>>February</option>
              <option value="3"<?php echo ($subbirthmonth==3 ? ' selected' : '')?>>March</option>
              <option value="4"<?php echo ($subbirthmonth==4 ? ' selected' : '')?>>April</option>
              <option value="5"<?php echo ($subbirthmonth==5 ? ' selected' : '')?>>May</option>
              <option value="6"<?php echo ($subbirthmonth==6 ? ' selected' : '')?>>June</option>
              <option value="7"<?php echo ($subbirthmonth==7 ? ' selected' : '')?>>July</option>
              <option value="8"<?php echo ($subbirthmonth==8 ? ' selected' : '')?>>August</option>
              <option value="9"<?php echo ($subbirthmonth==9 ? ' selected' : '')?>>September</option>
              <option value="10"<?php echo ($subbirthmonth==10 ? ' selected' : '')?>>October</option>
              <option value="11"<?php echo ($subbirthmonth==11 ? ' selected' : '')?>>November</option>
              <option value="12"<?php echo ($subbirthmonth==12 ? ' selected' : '')?>>December</option>
             </select>

             <select name="edbirthday">
              <option value=""></option>
<?php
         for ($i = 1; $i <= 31; $i++) {
            echo "<option value='$i'".($subbirthday==$i ? ' selected' : '').">$i</option>";
         }
?>
             </select>

             <select name="edbirthyear">
              <option value=""></option>
<?php
         for ($i = date("Y"); $i >= date("Y")-120; $i--) {
            echo "<option value='$i'".($subbirthyear==$i ? ' selected' : '').">$i</option>";
         }
?>
             </select>
            </td>
           </tr>
          </table>
         </td>
         <td><?php echo $session->form->error("edbirth")?></td>
        </tr>

        <tr>
         <td>
          <label for="edgender" class="grey">Gender:</label>
         </td>
         <td>
          <label for="male"><input type="radio" name="edgender" id="male" value="M"
<?php
         if($session->form->value("edgender") == "") {
            if($subuserinfo['gender'] == "M") {
               echo "checked ";
            }
         } else {
            if($session->form->value("edgender") == "M") {
               echo "checked ";
            }
         }
?>/>Male</label>
          <label for="female"><input type="radio" name="edgender" id="female" value="F" <?php
         if($session->form->value("edgender") == "") {
            if($subuserinfo['gender'] == "F") {
               echo "checked ";
            }
         } else {
            if($session->form->value("edgender") == "F") {
               echo "checked ";
            }
         }
?>/>Female</label>
         </td>
         <td><?php echo $session->form->error("edgender")?></td>
        </tr>

        <tr><td colspan="2">Address:</td></tr>
        <tr>
         <td>
          <label for="edaddrline1" class="grey">Line 1:</label>
         </td>
         <td>
          <input class="field" type="text" name="edaddrline1" maxlength="80" value="<?php
         if($session->form->value("edaddrline1") == "") {
            echo $subuserinfo['addrline1'];
         } else {
            echo $session->form->value("edaddrline1");
         }
?>" />
         </td>
         <td><?php echo $session->form->error("edaddrline1")?></td>
        </tr>

        <tr>
         <td>
          <label for="edaddrline2" class="grey">Line 2:</label>
         </td>
         <td>
          <input class="field" type="text" name="edaddrline2" maxlength="80" value="<?php
         if($session->form->value("edaddrline2") == "") {
            echo $subuserinfo['addrline2'];
         } else {
            echo $session->form->value("edaddrline2");
         }
?>" />
         </td>
         <td><?php echo $session->form->error("edaddrline2")?></td>
        </tr>

        <tr>
         <td>
          <label for="edcity" class="grey">City:</label>
         </td>
         <td>
          <input class="field" type="text" name="edcity" maxlength="40" value="<?php
         if($session->form->value("edcity") == "") {
            echo $subuserinfo['city'];
         } else {
            echo $session->form->value("edcity");
         }
?>" />
         </td>
         <td><?php echo $session->form->error("edcity")?></td>
        </tr>

        <tr>
         <td>
          <label for="edstate" class="grey">State:</label>
         </td>
         <td>
          <select name="edstate">
           <option value="">Select a State:</option>
           <option value="AL"<?php if($session->form->value("edstate") == "AL") echo " selected";
                                   else if($subuserinfo['state'] == "AL") echo " selected";?>>Alabama</option>
           <option value="AK"<?php if($session->form->value("edstate") == "AK") echo " selected";
                                   else if($subuserinfo['state'] == "AK") echo " selected";?>>Alaska</option>
           <option value="AZ"<?php if($session->form->value("edstate") == "AZ") echo " selected";
                                   else if($subuserinfo['state'] == "AZ") echo " selected";?>>Arizona</option>
           <option value="AR"<?php if($session->form->value("edstate") == "AR") echo " selected";
                                   else if($subuserinfo['state'] == "AR") echo " selected";?>>Arkansas</option>
           <option value="CA"<?php if($session->form->value("edstate") == "CA") echo " selected";
                                   else if($subuserinfo['state'] == "CA") echo " selected";?>>California</option>
           <option value="CO"<?php if($session->form->value("edstate") == "CO") echo " selected";
                                   else if($subuserinfo['state'] == "CO") echo " selected";?>>Colorado</option>
           <option value="CT"<?php if($session->form->value("edstate") == "CT") echo " selected";
                                   else if($subuserinfo['state'] == "CT") echo " selected";?>>Connecticut</option>
           <option value="DC"<?php if($session->form->value("edstate") == "DC") echo " selected";
                                   else if($subuserinfo['state'] == "DC") echo " selected";?>>District of Columbia</option>
           <option value="DE"<?php if($session->form->value("edstate") == "DE") echo " selected";
                                   else if($subuserinfo['state'] == "DE") echo " selected";?>>Delaware</option>
           <option value="FL"<?php if($session->form->value("edstate") == "FL") echo " selected";
                                   else if($subuserinfo['state'] == "FL") echo " selected";?>>Florida</option>
           <option value="GA"<?php if($session->form->value("edstate") == "GA") echo " selected";
                                   else if($subuserinfo['state'] == "GA") echo " selected";?>>Georgia</option>
           <option value="HI"<?php if($session->form->value("edstate") == "HI") echo " selected";
                                   else if($subuserinfo['state'] == "HI") echo " selected";?>>Hawaii</option>
           <option value="ID"<?php if($session->form->value("edstate") == "ID") echo " selected";
                                   else if($subuserinfo['state'] == "ID") echo " selected";?>>Idaho</option>
           <option value="IL"<?php if($session->form->value("edstate") == "IL") echo " selected";
                                   else if($subuserinfo['state'] == "IL") echo " selected";?>>Illinois</option>
           <option value="IN"<?php if($session->form->value("edstate") == "IN") echo " selected";
                                   else if($subuserinfo['state'] == "IN") echo " selected";?>>Indiana</option>
           <option value="IA"<?php if($session->form->value("edstate") == "IA") echo " selected";
                                   else if($subuserinfo['state'] == "IA") echo " selected";?>>Iowa</option>
           <option value="KS"<?php if($session->form->value("edstate") == "KS") echo " selected";
                                   else if($subuserinfo['state'] == "KS") echo " selected";?>>Kansas</option>
           <option value="KY"<?php if($session->form->value("edstate") == "KY") echo " selected";
                                   else if($subuserinfo['state'] == "KY") echo " selected";?>>Kentucky</option>
           <option value="LA"<?php if($session->form->value("edstate") == "LA") echo " selected";
                                   else if($subuserinfo['state'] == "LA") echo " selected";?>>Louisiana</option>
           <option value="ME"<?php if($session->form->value("edstate") == "ME") echo " selected";
                                   else if($subuserinfo['state'] == "ME") echo " selected";?>>Maine</option>
           <option value="MD"<?php if($session->form->value("edstate") == "MD") echo " selected";
                                   else if($subuserinfo['state'] == "MD") echo " selected";?>>Maryland</option>
           <option value="MA"<?php if($session->form->value("edstate") == "MA") echo " selected";
                                   else if($subuserinfo['state'] == "MA") echo " selected";?>>Massachusetts</option>
           <option value="MI"<?php if($session->form->value("edstate") == "MI") echo " selected";
                                   else if($subuserinfo['state'] == "MI") echo " selected";?>>Michigan</option>
           <option value="MN"<?php if($session->form->value("edstate") == "MN") echo " selected";
                                   else if($subuserinfo['state'] == "MN") echo " selected";?>>Minnesota</option>
           <option value="MS"<?php if($session->form->value("edstate") == "MS") echo " selected";
                                   else if($subuserinfo['state'] == "MS") echo " selected";?>>Mississippi</option>
           <option value="MO"<?php if($session->form->value("edstate") == "MO") echo " selected";
                                   else if($subuserinfo['state'] == "MO") echo " selected";?>>Missouri</option>
           <option value="MT"<?php if($session->form->value("edstate") == "MT") echo " selected";
                                   else if($subuserinfo['state'] == "MT") echo " selected";?>>Montana</option>
           <option value="NE"<?php if($session->form->value("edstate") == "NE") echo " selected";
                                   else if($subuserinfo['state'] == "NE") echo " selected";?>>Nebraska</option>
           <option value="NV"<?php if($session->form->value("edstate") == "NV") echo " selected";
                                   else if($subuserinfo['state'] == "NV") echo " selected";?>>Nevada</option>
           <option value="NH"<?php if($session->form->value("edstate") == "NH") echo " selected";
                                   else if($subuserinfo['state'] == "NH") echo " selected";?>>New Hampshire</option>
           <option value="NJ"<?php if($session->form->value("edstate") == "NJ") echo " selected";
                                   else if($subuserinfo['state'] == "NJ") echo " selected";?>>New Jersey</option>
           <option value="NM"<?php if($session->form->value("edstate") == "NM") echo " selected";
                                   else if($subuserinfo['state'] == "NM") echo " selected";?>>New Mexico</option>
           <option value="NY"<?php if($session->form->value("edstate") == "NY") echo " selected";
                                   else if($subuserinfo['state'] == "NY") echo " selected";?>>New York</option>
           <option value="NC"<?php if($session->form->value("edstate") == "NC") echo " selected";
                                   else if($subuserinfo['state'] == "NC") echo " selected";?>>North Carolina</option>
           <option value="ND"<?php if($session->form->value("edstate") == "ND") echo " selected";
                                   else if($subuserinfo['state'] == "ND") echo " selected";?>>North Dakota</option>
           <option value="OH"<?php if($session->form->value("edstate") == "OH") echo " selected";
                                   else if($subuserinfo['state'] == "OH") echo " selected";?>>Ohio</option>
           <option value="OK"<?php if($session->form->value("edstate") == "OK") echo " selected";
                                   else if($subuserinfo['state'] == "OK") echo " selected";?>>Oklahoma</option>
           <option value="OR"<?php if($session->form->value("edstate") == "OR") echo " selected";
                                   else if($subuserinfo['state'] == "OR") echo " selected";?>>Oregon</option>
           <option value="PA"<?php if($session->form->value("edstate") == "PA") echo " selected";
                                   else if($subuserinfo['state'] == "PA") echo " selected";?>>Pennsylvania</option>
           <option value="PR"<?php if($session->form->value("edstate") == "PR") echo " selected";
                                   else if($subuserinfo['state'] == "PR") echo " selected";?>>Puerto Rico</option>
           <option value="RI"<?php if($session->form->value("edstate") == "RI") echo " selected";
                                   else if($subuserinfo['state'] == "RI") echo " selected";?>>Rhode Island</option>
           <option value="SC"<?php if($session->form->value("edstate") == "SC") echo " selected";
                                   else if($subuserinfo['state'] == "SC") echo " selected";?>>South Carolina</option>
           <option value="SD"<?php if($session->form->value("edstate") == "SD") echo " selected";
                                   else if($subuserinfo['state'] == "SD") echo " selected";?>>South Dakota</option>
           <option value="TN"<?php if($session->form->value("edstate") == "TN") echo " selected";
                                   else if($subuserinfo['state'] == "TN") echo " selected";?>>Tennessee</option>
           <option value="TX"<?php if($session->form->value("edstate") == "TX") echo " selected";
                                   else if($subuserinfo['state'] == "TX") echo " selected";?>>Texas</option>
           <option value="UT"<?php if($session->form->value("edstate") == "UT") echo " selected";
                                   else if($subuserinfo['state'] == "UT") echo " selected";?>>Utah</option>
           <option value="VT"<?php if($session->form->value("edstate") == "VT") echo " selected";
                                   else if($subuserinfo['state'] == "VT") echo " selected";?>>Vermont</option>
           <option value="VA"<?php if($session->form->value("edstate") == "VA") echo " selected";
                                   else if($subuserinfo['state'] == "VA") echo " selected";?>>Virginia</option>
           <option value="WA"<?php if($session->form->value("edstate") == "WA") echo " selected";
                                   else if($subuserinfo['state'] == "WA") echo " selected";?>>Washington</option>
           <option value="WV"<?php if($session->form->value("edstate") == "WV") echo " selected";
                                   else if($subuserinfo['state'] == "WV") echo " selected";?>>West Virginia</option>
           <option value="WI"<?php if($session->form->value("edstate") == "WI") echo " selected";
                                   else if($subuserinfo['state'] == "WI") echo " selected";?>>Wisconsin</option>
           <option value="WY"<?php if($session->form->value("edstate") == "WY") echo " selected";
                                   else if($subuserinfo['state'] == "WY") echo " selected";?>>Wyoming</option>
          </select>
         </td>
         <td><?php echo $session->form->error("edstate")?></td>
        </tr>

        <tr>
         <td>
          <label for="edzip" class="grey">Zip:</label>
         </td>
         <td>
          <input class="field" type="text" id="edzip" name="edzip" maxlength="10" value="<?php
   if($session->form->value("edzip") == "") {
      echo $subuserinfo['zip'];
   } else {
      echo $session->form->value("edzip");
   }
?>" />
         </td>
         <td><?php echo $session->form->error("edzip")?></td>
        </tr>

        <tr>
         <td>
          <label for="edphone" class="grey">Phone:</label>
         </td>
         <td>
          <input class="field" type="text" id="edphone" name="edphone" maxlength="26" value="<?php
   if($session->form->value("edphone") == "") {
      echo $subuserinfo['phone'];
   } else {
      echo $session->form->value("edphone");
   }
?>" />
         </td>
         <td><?php echo $session->form->error("edphone")?></td>
        </tr>

        <tr>
         <td>
          <label class="grey">Usertype:</label>
         </td>
         <td>
          <select name="edutype"<?php echo ($session->isAdmin() ? '' : ' disabled')?>>
           <option value="<?php echo CUST; ?>"<?php echo ($subuserinfo['usertype'] == CUST) ? ' selected' : '' ?>>Customer</option>
           <option value="<?php echo TELLER; ?>"<?php echo ($subuserinfo['usertype'] == TELLER) ? ' selected' : '' ?>>Teller</option>
           <option value="<?php echo ADMIN; ?>"<?php echo ($subuserinfo['usertype'] == ADMIN) ? ' selected' : '' ?>>Administrator</option>
          </select>
         </td>
         <td><?php echo $session->form->error("edutype")?></td>
        </tr>

        <tr>
         <td colspan="2" align="right">
          <input type="hidden" name="subedit" value="1">
          <input type="submit" value="Save!" class="bt_register">
         </td>
        </tr>

       </table>
      </form>
<?php
} //end uid specified
?>
      <p class="hide"><a href="#top">Back to top</a></p>
     </div>
    </div>

    <?php include_once("../include/footer.php"); ?>

   </div>
  </div>
 </body>
</html>