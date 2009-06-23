<?
/**
 * process.php
 * 
 * The Process class is meant to simplify the task of processing user submitted forms,
 * redirecting the user to the correct pages if errors are found, or if form is
 * successful, either way. Also handles the logout procedure.
 */
include("include/session.php");

class Process {
   /* Class constructor */
   function Process() {
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
      $retval = $session->login($_POST['user'], $_POST['pass'], isset($_POST['remember']));

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
         $_POST['reguser'] = strtolower($_POST['reguser']);
      }

      /* Convert birthdate into timestamp */
      $regbirthdate = mktime(0, 0, 0, $_POST['regbirthmonth'], $_POST['regbirthday'], $_POST['regbirthyear']);

      $ulevel = CUST_LEVEL;
      if($session->isAdmin()) {
         $ulevel = $_POST['regulevel'];
      }

      /* Registration attempt */
      $retval = $session->register($_POST['reguser'], $_POST['regpass'], $_POST['regemail'], $_POST['regname'], $regbirthdate, $_POST['regaddr'], $_POST['regsex'], $_POST['regphone'], $ulevel);
      
      if($retval == 0) { /* Registration Successful */
         $_SESSION['reguname'] = $_POST['reguser'];
         $_SESSION['regsuccess'] = true;
         header("Location: ".$session->referrer);
      } else if($retval == 1) { /* Error found with form */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $session->form->getErrorArray();
         header("Location: ".$session->referrer);
      } else if($retval == 2) { /* Registration attempt failed */
         $_SESSION['reguname'] = $_POST['reguser'];
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
      $subuser = $_POST['user'];
      $field = "user";  //Use field name for username

      if(!$subuser || strlen($subuser = trim($subuser)) == 0) {
         $session->form->setError($field, "* Username not entered<br>");
      } else { /* Make sure username is in database */
         $subuser = stripslashes($subuser);
         if(strlen($subuser) < 5 || strlen($subuser) > 30 ||
            !eregi("^([0-9a-z])+$", $subuser) ||
            (!$database->usernameTaken($subuser))) {
            $session->form->setError($field, "* Username does not exist<br />");
         }
      }

      if($session->form->num_errors > 0) { /* Errors exist, have user correct them */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $session->form->getErrorArray();
      } else { /* Generate new password and email it to user */
         $newpass = $session->generateRandStr(8);

         $userinfo = $database->getUserInfo($database->getUID($subuser), DB_TBL_USERS);
         $email  = $userinfo['email'];

         if($mailer->sendNewPass($subuser,$email,$newpass)) { /* Attempt to send the email with new password */
            /* Email sent, update database */
            $database->updateUserField($database->getUID($subuser),DB_TBL_USERS,"password",$session->hashPassword($newpass));
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
   function procEditAccount() {
      global $session;

      /* Convert birthdate into timestamp */
      $edbirth = mktime(0, 0, 0, $_POST['edbirthmonth'], $_POST['edbirthday'], $_POST['edbirthyear']);

      /* Account edit attempt */
      $retval = $session->editAccount($_POST['eduser'], $_POST['edcurpass'], $_POST['ednewpass'], $_POST['edemail'], $_POST['edname'], $edbirth, $_POST['edaddr'], $_POST['edsex'], $_POST['edphone'], $_POST['edulevel']);

      /* Account edit successful */
      if($retval) {
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