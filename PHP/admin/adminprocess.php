<?php
/**
 * adminprocess.php
 *
 * The adminprocess class is meant to simplify the task of processing
 * admin submitted forms from the admin center, these deal with
 * member system adjustments.
 */
include("../include/session.php");

class AdminProcess
{
   /* Class constructor */
   function __construct() {
      global $session;

      if(!$session->isAdmin()) { /* Make sure administrator is accessing page */
         header("Location: ".SITE_BASE_URL."/index.php");
         return;
      }

      if(isset($_POST['subedit'])) { /* Admin submitted edit user form */
         $this->procEditAccount();
      } else if(isset($_POST['subdeluser'])) { /* Admin submitted delete user form */
         $this->procDeleteUser();
      } else if(isset($_POST['subdelinact'])) { /* Admin submitted delete inactive users form */
         $this->procDeleteInactive();
      } else if(isset($_POST['subbanuser'])) { /* Admin submitted ban user form */
         $this->procBanUser();
      } else if(isset($_POST['subdelbanned'])) { /* Admin submitted delete banned user form */
         $this->procDeleteBannedUser();
      } else { /* Should not get here, redirect to home page */
         header("Location: ".SITE_BASE_URL."/index.php");
      }
   }

   /**
    * procEditAccount - Attempts to edit the user's account information, including the
    * the password, which must be verified before a change is made.
    */
   public function procEditAccount() {//IF time permits, re-implement using new User classes
      global $session;

      $edUserInfo = array("uid"=>$_POST['uid'], "username"=>$_POST['username']);

      /* Account edit attempt */
      /* New Password error checking */
      if(isset($_POST['newpass']) && !empty($_POST['newpass'])) {
         if($_POST['newpass'] == $_POST['newpassconf']) {
            $edUserInfo['newpass'] = $session->hashPassword($_POST['newpass']);
         } else {
            $session->form->setError("newpassconf", "* Must match new password entered above");
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

      /* Usertype error checking */
      $field = "edutype"; //Use field name for usertype
      if(($edUserInfo['uid'] == $session->uid) &&
         ($edUserInfo['edutype'] != ADMIN) && $session->database->getNumAdmins < 2) {
         $session->form->setError($field, "* You are the only Administrator in the system<br />");
      }

      if($session->form->num_errors == 0) { /* No errors exist */
         if($session->database->confirmUID($edUserInfo['uid'], DB_TBL_ADMINS)) {
            $user = new Administrator($edUserInfo);
            $retval = $user->update($edUserInfo);
            if(($retval == 0) && ($edUserInfo['edutype'] != ADMIN)) {
               if(!$user->updateUserType($edUserInfo['edutype'])) {
                  $session->form->setError("edutype", "* Usertype has remained the same");
                  $retval = 1;
               }
            }
         } else if($session->database->confirmUID($edUserInfo['uid'], DB_TBL_TELLERS)) {
            $user = new Teller($edUserInfo);
            $retval = $user->update($edUserInfo);
            if(($retval == 0) && ($edUserInfo['edutype'] != TELLER)) {
               if(!$user->updateUserType($edUserInfo['edutype'])) {
                  $session->form->setError("edutype", "* Usertype has remained the same");
                  $retval = 1;
               }
            }
         } else if($session->database->confirmUID($edUserInfo['uid'], DB_TBL_CUSTOMERS)) {
            $user = new Customer($edUserInfo);
            $retval = $user->update($edUserInfo);
            if(($retval == 0) && ($edUserInfo['edutype'] != CUSTOMER)) {
               if(!$user->updateUserType($edUserInfo['edutype'])) {
                  $session->form->setError("edutype", "* Usertype has remained the same");
               	$retval = 1;
               }
            }
         } else {
            $session->form->setError("edutype", "* Invalid Usertype");
            $retval = 1;
         }
      } else {
      	$retval = 1;
      }

      /* Account edit successful */
      if(isset($retval) && ($retval == 0)) {
         $_SESSION['edituser'] = true;
      } else { /* Error found with form */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $session->form->getErrorArray();
      }
      header("Location: ".$session->referrer."?uid=".$_POST['uid']);
   }

   /**
    * procDeleteUser - If submitted username is correct, user is deleted from the database.
    */
   public function procDeleteUser() {//IF time permits, re-implement using new User classes
      global $session;

      /* uid error checking */
      $field = "deluser"; //Use the field for uid
      $subuid = $_POST[$field];
      if($subuid == $session->user->getUID()) { /* Make sure no one tries and delete themselves */
         $session->form->setError($field, "* You can't delete yourself!<br />");
      }

      if($session->form->num_errors > 0) { /* Errors exist, have user correct them */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $session->form->getErrorArray();
      } else { /* Delete user from database */
         $session->database->removeUser($subuid, DB_TBL_ADMINS);
         $session->database->removeUser($subuid, DB_TBL_TELLERS);
         $session->database->removeUser($subuid, DB_TBL_CUSTOMERS);
      }
      header("Location: ".$session->referrer);
   }

   /**
    * procBanUser - If the submitted username is correct, the user is banned from the member
    * system, which entails removing the username from the users table and adding it to the
    * banned users table.
    */
   public function procBanUser() {//IF time permits, re-implement using new User classes
      global $session;

      /* Username error checking */
      $field = "banuser"; //Use the field for username
      $subuser = $this->_checkUsername($field);

      if($subuser == $session->user->username) { /* Make sure no one tries and ban themselves */
         $session->form->setError($field, "* You can't ban yourself!<br />");
      }

      if($session->form->num_errors > 0) { /* Errors exist, have user correct them */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $session->form->getErrorArray();
      } else { /* Ban user from member system */
      	$uid = $session->database->getUID($subuser);
         $session->database->removeUser($uid, DB_TBL_ADMINS);
         $session->database->removeUser($uid, DB_TBL_TELLERS);
         $session->database->removeUser($uid, DB_TBL_CUSTOMERS);

         $q = "INSERT INTO ".DB_TBL_BANNED_USERS." VALUES ('$subuser', $session->time)";
         $session->database->query($q);
      }
      header("Location: ".$session->referrer);
   }

   /**
    * procDeleteBannedUser - If the submitted username is correct, the user is deleted from
    * the banned users table, which enables someone to register with that username again.
    */
   public function procDeleteBannedUser() {
      global $session;

      /* Username error checking */
      $field = "delbanuser";  //Use field name for username
      $subuser = $this->_checkUsername($field, true);

      if($session->form->num_errors > 0) { /* Errors exist, have user correct them */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $session->form->getErrorArray();
      } else { /* Delete user from database */
         $q = "DELETE FROM ".DB_TBL_BANNED_USERS." WHERE username = '$subuser'";
         $session->database->query($q);
      }
      header("Location: ".$session->referrer);
   }

   /**
    * _checkUsername - Helper function for the above processing, it makes sure the
    * submitted username is valid, if not, it adds the appropritate error to the form.
    */
   private function _checkUsername($uname, $ban=false) {
      global $session;

      /* Username error checking */
      $subuser = $_POST[$uname];
      $field = $uname;  //Use field name for username

      if(!$subuser || strlen($subuser = trim($subuser)) == 0) {
         $session->form->setError($field, "* Username not entered<br>");
      } else {
         /* Make sure username is in database */
         $subuser = stripslashes($subuser);

         if(strlen($subuser) < 5 || strlen($subuser) > 30 ||
            !eregi("^([0-9a-z])+$", $subuser) ||
            (!$ban && !$session->database->usernameTaken($subuser))) {
            $session->form->setError($field, "* Username does not exist<br />");
         }
      }
      return $subuser;
   }
};

/* Initialize process */
$adminprocess = new AdminProcess;
?>