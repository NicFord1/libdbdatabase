<?
/**
 * process.php
 *
 * The Process class is meant to simplify the task of processing user submitted forms,
 * redirecting the user to the correct pages if errors are found, or if form is
 * successful, either way. Also handles the logout procedure.
 */
require_once("include/users.php");
require_once("include/session.php");

class Process {
   /* Class constructor */
   function __construct() {
      global $session;

      if(isset($_POST['sublogin'])) { /* User submitted login form */
         $this->procLogin();
      } else if(isset($_POST['subreg'])) { /* User submitted registration form */
         $this->procRegister();
      } else if(isset($_POST['subforgot'])) { /* User submitted forgot password form */
         $this->procForgotPass();
      } else if(isset($_POST['subedit'])) { /* User submitted edit account form */
         $this->procEditAccount();
      } else if(isset($_GET['logout'])) { /* User clicked the logout link */
         $this->procLogout();
      } else { /* Nothing to process, redirect to front page */
          header("Location: ".SITE_BASE_URL."/index.php");
       }
   }

   /**
    * procLogin - Processes the user submitted login form, if errors are found, the user is
    * redirected to correct the information, if not, the user is effectively logged in to the system.
    */
   function procLogin() {
      global $session;

      /* Login attempt */
      $retval = $session->login($_POST['uname'], $_POST['pass'], isset($_POST['remember']));

      if($retval) { /* Login successful */
         header("Location: ".$session->referrer);
      } else { /* Login failed */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $session->form->getErrorArray();
         header("Location: ".$session->referrer);
      }
   }

   /**
    * procLogout - Simply attempts to log the user out of the system given that there
    * is no logout form to process.
    */
   function procLogout() {
      global $session;
      $retval = $session->logout();
      header("Location: ".SITE_BASE_URL."/index.php");
   }

   /**
    * procRegister - Processes the user submitted registration form, if errors are found, the
    * user is redirected to correct the information, if not, the user is effectively registered
    * with the system and an email is (optionally) sent to the newly created user.
    */
   function procRegister() {
      global $session;

      if(ALL_LOWERCASE) { /* Convert username to all lowercase (by option) */
         $_POST['reguname'] = strtolower($_POST['reguname']);
      }

      /* Convert birthdate into timestamp */
      if(isset($_POST['regbirthmonth']) && !empty($_POST['regbirthmonth']) &&
         isset($_POST['regbirthday']) && !empty($_POST['regbirthday']) &&
         isset($_POST['regbirthyear']) && !empty($_POST['regbirthyear'])) {
         date_default_timezone_set('America/New_York');
         $regbirthdate = mktime(0, 0, 0, $_POST['regbirthmonth'], $_POST['regbirthday'], $_POST['regbirthyear']);
      } else {
      	$session->form->setError("regbirth", "* You must provide your birthdate");
      }

      $utype = CUST;
      if($session->isAdmin()) {
         $utype = $_POST['regutype'];
      }

      $regInfo = array("username"=>$_POST['reguname'], "password"=>$_POST['regpass'],
                       "passconf"=>$_POST['regpassconf'], "email"=>$_POST['regemail'],
                       "firstname"=>$_POST['regfname'], "lastname"=>$_POST['reglname'],
                       "birthdate"=>$regbirthdate, "addrline1"=> $_POST['regaddrline1'],
                       "addrline2"=> $_POST['regaddrline2'], "city"=> $_POST['regcity'],
                       "state"=> $_POST['regstate'], "zip"=> $_POST['regzip'],
                       "gender"=>$_POST['reggender'], "phone"=>$_POST['regphone'],
                       "usertype"=>$utype);

      $retval = $session->register($regInfo);

      if($retval == 0) { /* Registration Successful */
         $_SESSION['reguname'] = $_POST['reguname'];
         $_SESSION['regsuccess'] = true;
         header("Location: ".$session->referrer);
      } else if($retval == 1) { /* Error found with form */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $session->form->getErrorArray();
         header("Location: ".$session->referrer);
      } else if($retval == 2) { /* Registration attempt failed */
         $_SESSION['reguname'] = $_POST['reguname'];
         $_SESSION['regsuccess'] = false;
         header("Location: ".$session->referrer);
      }
   }

   /**
    * procForgotPass - Validates the given username then if everything is fine, a new
    * password is generated and emailed to the address the user gave on sign up.
    */
   function procForgotPass() {
      global $database, $session, $mailer;

      /* Username error checking */
      $subuname = $_POST['uname'];
      $field = "uname";  //Use field name for username

      if(!$subuname || strlen($subuname = trim($subuname)) == 0) {
         $session->form->setError($field, "* Username not entered<br>");
      } else { /* Make sure username is in database */
         $subuname = stripslashes($subuname);
         if(strlen($subuname) < 5 || strlen($subuname) > 30 ||
            !eregi("^([0-9a-z])+$", $subuname) ||
            (!$database->usernameTaken($subuname))) {
            $session->form->setError($field, "* Username does not exist<br />");
         }
      }

      if($session->form->num_errors > 0) { /* Errors exist, have user correct them */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $session->form->getErrorArray();
      } else { /* Generate new password and email it to user */
         $newpass = $session->generateRandStr(8);

         $userinfo = $database->getUserInfo($database->getUID($subuname));
         $email  = $userinfo['email'];

         if($mailer->sendNewPass($subuname,$email,$newpass)) { /* Attempt to send the email with new password */
            /* Email sent, update database */
            $database->updateUserField($database->getUID($subuname),DB_TBL_CUSTOMERS,"password",$session->hashPassword($newpass));
            $_SESSION['forgotpass'] = true;
         } else { /* Email failure, do not change password */
            $_SESSION['forgotpass'] = false;
         }
      }
      header("Location: ".$session->referrer);
   }

   /**
    * procEditAccount - Attempts to edit the user's account information, including the
    * the password, which must be verified before a change is made.
    */
   public function procEditAccount() {
      global $session;

      $edUserInfo = array("uid"=>$_POST['uid'], "username"=>$_POST['username'],
                          "password"=>$_POST['curpass']);
      if(isset($_POST['curpass']) && !empty($_POST['curpass'])) {
         $salt = substr($session->user->password, 0, SALT_LENGTH);
         $password = $session->hashPassword($edUserInfo['password'], $salt);

         if($session->user->password == $password) { // Account edit attempt
            /* New Password error checking */
            if(isset($_POST['ednewpass']) && !empty($_POST['ednewpass'])) {
               if($_POST['ednewpass'] == $_POST['ednewpassconf']) {
                  $edUserInfo['newpass'] = $session->hashPassword($_POST['ednewpass']);
               } else {
                  $session->form->setError("ednewpassconf", "* Must match new password entered above");
               }

               /* Check if password is not alphanumeric */
               if(!eregi("^([0-9a-z])+$", ($edUserInfo['newpass'] = trim($edUserInfo['newpass'])))) {
                  $session->form->setError("ednewpass", "* New Password not alphanumeric");
               }
            }

            /* Email error checking */
            if(isset($_POST['edemail']) && !empty($_POST['edemail'])) {
               $edUserInfo['edemail'] = stripslashes(trim($_POST['edemail']));
            } else {
            	$session->form->setError("edemail", "* An email is required");
            }

            $edUserInfo['edfname'] = $_POST['edfname'];
            $edUserInfo['edlname'] = $_POST['edlname'];

            if(isset($_POST['edbirthmonth']) && !empty($_POST['edbirthmonth']) &&
               isset($_POST['edbirthday']) && !empty($_POST['edbirthday']) &&
               isset($_POST['edbirthyear']) && !empty($_POST['edbirthyear'])) {
               /* Convert birthdate into timestamp */
               date_default_timezone_set('America/New_York');
               $edbirth = mktime(0, 0, 0, $_POST['edbirthmonth'], $_POST['edbirthday'],
                              $_POST['edbirthyear']);
               $edUserInfo['edbirthdate'] = $edbirth;
            } else {
            	$session->form->setError("edbirth", "* Must specifiy birthdate");
            }

            $edUserInfo['edgender'] = $_POST['edgender'];
            $edUserInfo['edaddrline1'] = $_POST['edaddrline1'];
            $edUserInfo['edaddrline2'] = $_POST['edaddrline2'];
            $edUserInfo['edcity'] = $_POST['edcity'];
            $edUserInfo['edstate'] = $_POST['edstate'];
            $edUserInfo['edzip'] = $_POST['edzip'];
            $edUserInfo['edphone'] = $_POST['edphone'];
            $edUserInfo['edutype'] = $_POST['edutype'];

            if($session->form->num_errors == 0) { // Account edit attempt
               if($session->isCustomer()) {
                  $account = new Customer($edUserInfo);
               } else if($session->isTeller()) {
                  $account = new Teller($edUserInfo);
               } else if($session->isAdmin()) {
                  $account = new Administrator($edUserInfo);
               } else { //invalid usertype
                  $session->form->setError("edutype", "* Invalid Usertype");
               }

               if(isset($account)) {
                  $retval = $account->update($edUserInfo);

                  if($session->isAdmin()) {
                     $account->updateUserType($edUserInfo['edutype']);
                  }
               }
            }
         } else {
            $session->form->setError("curpass", "* Incorrect password");
         }
      } else {
      	$session->form->setError("curpass", "* You must confirm your current password");
      }

      /* Account edit successful */
      if(isset($retval) && ($retval == 0)) {
         $_SESSION['edituser'] = true;
      } else { /* Error found with form */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $session->form->getErrorArray();
      }
      header("Location: ".$session->referrer);
   }
};

/* Initialize process */
$process = new Process;
?>