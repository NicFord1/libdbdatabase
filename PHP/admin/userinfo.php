<?
/**
 * userinfo.php
 *
 * This is the User Center page. Only administrators & tellers are allowed to view this page while logged in.
 * This page allows for the creation of new user accounts.  Only admins can create new admins & tellers.  All
 * other users can only create regular customers.
 */
include("../include/session.php");

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
       <a href="<?=SITE_BASE_URL?>/index.php">LibDBDatabase</a> &raquo;
       <a href="<?=SITE_BASE_URL?>/admin/admin.php">Admin Center</a> &raquo;
       <a href="<?=$_SERVER['PHP_SELF']?>?uid=<?=$_GET['uid']?>">User Center</a>
      </strong>
     </div>

     <div id="leftside">
      <?php include_once("../include/sidemenu.php"); ?>
     </div>

     <a id="main"></a>
     <div id="contentalt">
      <h1>User Center</h1>
      <img src="<?=SITE_BASE_URL?>/img/gravatar-edituser.png" height="80" width="80" alt="Edit User Gravatar" />

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />
<?php
if(!isset($_GET['uid']) && !isset($_SESSION['edituser'])) {
   echo "<font size=\"2\" color=\"#ff0000\">Target user not identified.</font>\n";
} else {
   if($_GET['uid']) {
      $subuserinfo = $database->getUserInfo($_GET['uid'], DB_TBL_USERS);
   } else {
      $subuserinfo = $database->getUserInfo($session->form->value("eduid"), DB_TBL_USERS);
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
      <h1>Update Account</h1>
      <form action="adminprocess.php" method="POST" id="edituser">
       <input type="hidden" name="eduid" value="<?php
   if($session->form->value("eduid") == "") {
      echo $subuserinfo['uid'];
   } else {
      echo $session->form->value("eduid");
   }
?>" />
       <table align="left" border="0" cellspacing="0" cellpadding="3">
        <tr>
         <td>
          <label for="eduser" class="grey required">Username:</label>
         </td>
         <td>
          <input class="field" type="text" name="eduser" maxlength="30" value="<?php
   if($session->form->value("eduser") == "") {
      echo $subuserinfo['username'];
   } else {
      echo $session->form->value("eduser");
   }
?>" readonly />
         </td>
         <td><?=$session->form->error("eduser")?></td>
        </tr>

        <tr>
         <td>
          <label for="ednewpass" class="grey">New Password:</label>
         </td>
         <td>
          <input type="password" name="ednewpass" maxlength="30" value="<?=$session->form->value("ednewpass")?>" />
         </td>
         <td><?=$session->form->error("ednewpass")?></td>
        </tr>

        <tr>
         <td>
          <label for="edemail" class="grey required">Email:</label>
         </td>
         <td>
          <input class="field" type="text" name="edemail" maxlength="96" value="
<?php
   if($session->form->value("edemail") == "") {
      echo $subuserinfo['email'];
   } else {
      echo $session->form->value("edemail");
   }
?>" />
         </td>
         <td><?=$session->form->error("edemail")?></td>
        </tr>

        <tr>
         <td>
          <label for="edname" class="grey">Fullname:</label>
         </td>
         <td>
          <input class="field" type="text" name="edname" maxlength="50" value="<?php
   if($session->form->value("edname") == "") {
      echo $subuserinfo['fullname'];
   } else {
      echo $session->form->value("edname");
   }
?>" />
         </td>
         <td><?=$session->form->error("edname")?></td>
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
              <option value="1"<?=($subbirthmonth==1 ? ' selected' : '')?>>January</option>
              <option value="2"<?=($subbirthmonth==2 ? ' selected' : '')?>>February</option>
              <option value="3"<?=($subbirthmonth==3 ? ' selected' : '')?>>March</option>
              <option value="4"<?=($subbirthmonth==4 ? ' selected' : '')?>>April</option>
              <option value="5"<?=($subbirthmonth==5 ? ' selected' : '')?>>May</option>
              <option value="6"<?=($subbirthmonth==6 ? ' selected' : '')?>>June</option>
              <option value="7"<?=($subbirthmonth==7 ? ' selected' : '')?>>July</option>
              <option value="8"<?=($subbirthmonth==8 ? ' selected' : '')?>>August</option>
              <option value="9"<?=($subbirthmonth==9 ? ' selected' : '')?>>September</option>
              <option value="10"<?=($subbirthmonth==10 ? ' selected' : '')?>>October</option>
              <option value="11"<?=($subbirthmonth==11 ? ' selected' : '')?>>November</option>
              <option value="12"<?=($subbirthmonth==12 ? ' selected' : '')?>>December</option>
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
         <td><?=$session->form->error("edbirth")?></td>
        </tr>

        <tr>
         <td>
          <label for="edsex" class="grey">Sex:</label>
         </td>
         <td>
          <label for="male"><input type="radio" name="edsex" id="male" value="M"
<?php
   if($session->form->value("edsex") == "") {
      if($subuserinfo['sex'] == "M") {
         echo "checked ";
      }
   } else {
      if($session->form->value("edsex") == "M") {
         echo "checked ";
      }
   }
?>/>Male</label>
          <label for="female"><input type="radio" name="edsex" id="female" value="F" <?php
   if($session->form->value("edsex") == "") {
      if($subuserinfo['sex'] == "F") {
         echo "checked ";
      }
   } else {
      if($session->form->value("edsex") == "F") {
         echo "checked ";
      }
   }
?>/>Female</label>
         </td>
         <td><?=$session->form->error("edsex")?></td>
        </tr>

        <tr>
         <td>
          <label for="edaddr" class="grey">Address:</label>
         </td>
         <td>
          <input class="field" type="text" name="edaddr" maxlength="160" value="<?php
   if($session->form->value("edaddr") == "") {
      echo $subuserinfo['address'];
   } else {
      echo $session->form->value("edaddr");
   }
?>" />
         </td>
         <td><?=$session->form->error("edaddr")?></td>
        </tr>

        <tr>
         <td>
          <label for="edphone" class="grey">Phone:</label>
         </td>
         <td>
          <input class="field" type="text" name="edphone" maxlength="26" value="<?php
   if($session->form->value("edphone") == "") {
      echo $subuserinfo['phone'];
   } else {
      echo $session->form->value("edphone");
   }
?>" />
         </td>
         <td><?=$session->form->error("edphone")?></td>
        </tr>

        <tr>
         <td>
          <label class="grey">User Level:</label>
         </td>
         <td>
          <select name="edulevel"<?=($session->isAdmin() ? '' : ' disabled')?>>
           <option value="<?=CUST_LEVEL?>"<?=($subuserinfo['userlevel'] == CUST_LEVEL) ? ' selected' : '' ?>>Customer</option>
           <option value="<?=TELLER_LEVEL?>"<?=($subuserinfo['userlevel'] == TELLER_LEVEL) ? ' selected' : '' ?>>Teller</option>
           <option value="<?=ADMIN_LEVEL?>"<?=($subuserinfo['userlevel'] == ADMIN_LEVEL) ? ' selected' : '' ?>>Administrator</option>
          </select>
         </td>
         <td><?=$session->form->error("edulevel")?></td>
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