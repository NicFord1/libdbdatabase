<?
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
   function AdminProcess() {
      global $session;
    
      if(!$session->isAdmin()) { /* Make sure administrator is accessing page */
         header("Location: ".SITE_BASE_URL."/index.php");
         return;
      }
    
      if(isset($_POST['subedit'])) { /* Admin submitted edit user form */
         $this->procEditAccount();
      } else if(isset($_POST['subupdlevel'])) { /* Admin submitted update user level form */
         $this->procUpdateLevel();
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
   function procEditAccount() {
      global $session, $form;

      /* Convert birthdate into timestamp */
      $edbirth = mktime(0, 0, 0, $_POST['edbirthmonth'], $_POST['edbirthday'], $_POST['edbirthyear']);

      /* Account edit attempt */
      $retval = $this->editAccount($_POST['eduid'], $_POST['eduser'], $_POST['ednewpass'], $_POST['edemail'], $_POST['edname'], $edbirth, $_POST['edaddr'], $_POST['edsex'], $_POST['edphone'], $_POST['edulevel']);

      /* Account edit successful */
      if($retval) {
         $_SESSION['edituser'] = true;
      } else { /* Error found with form */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
      }
      header("Location: ".$session->referrer."?uid=".$_POST['eduid']);
   }

   /**
    * procUpdateLevel - If the submitted username is correct, their user level is updated
    * according to the admin's request.
    */
   function procUpdateLevel() {
      global $session, $database, $form;

      /* Username error checking */
      $field = "upduser";
      $subuid = $database->getUID($this->checkUsername($field));

      if(!$subuid) {
         $form->setError($field, "* User doesn't exist!<br />");
      }

      //if admin, check for other admins
      if(($session->uid == $subuid) && $session->isAdmin() && ($database->getNumAdmins() < 2)) {
         $form->setError($field, "* You are the only admin in the system.<br />");
      }

      if($form->num_errors > 0) { /* Errors exist, have user correct them */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
      } else { /* Update user level */
         $database->updateUserField($subuid, "userlevel", (int)$_POST['updlevel']);
      }
      header("Location: ".$session->referrer);
   }
   
   /**
    * procDeleteUser - If submitted username is correct, user is deleted from the database.
    */
   function procDeleteUser() {
      global $session, $database, $form;

      /* Username error checking */
      $field = "deluser"; //Use the field for username
      $subuser = $this->checkUsername($field);
      if($subuser == $session->username) { /* Make sure no one tries and delete themselves */
         $form->setError($field, "* You can't delete yourself!<br />");
      }

      if($form->num_errors > 0) { /* Errors exist, have user correct them */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
      } else { /* Delete user from database */
         $q = "DELETE FROM ".DB_TBL_USERS." WHERE username = '$subuser'";
         $database->query($q);
      }
      header("Location: ".$session->referrer);
   }
   
   /**
    * procBanUser - If the submitted username is correct, the user is banned from the member
    * system, which entails removing the username from the users table and adding it to the
    * banned users table.
    */
   function procBanUser() {
      global $session, $database, $form;

      /* Username error checking */
      $field = "banuser"; //Use the field for username
      $subuser = $this->checkUsername($field);

      if($subuser == $session->username) { /* Make sure no one tries and ban themselves */
         $form->setError($field, "* You can't ban yourself!<br />");
      }

      if($form->num_errors > 0) { /* Errors exist, have user correct them */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
      } else { /* Ban user from member system */
         $q = "DELETE FROM ".DB_TBL_USERS." WHERE username = '$subuser'";
         $database->query($q);

         $q = "INSERT INTO ".DB_TBL_BANNED_USERS." VALUES ('$subuser', $session->time)";
         $database->query($q);
      }
      header("Location: ".$session->referrer);
   }
   
   /**
    * procDeleteBannedUser - If the submitted username is correct, the user is deleted from
    * the banned users table, which enables someone to register with that username again.
    */
   function procDeleteBannedUser() {
      global $session, $database, $form;

      /* Username error checking */
      $field = "delbanuser";  //Use field name for username
      $subuser = $this->checkUsername($field, true);
   
      if($form->num_errors > 0) { /* Errors exist, have user correct them */
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
      } else { /* Delete user from database */
         $q = "DELETE FROM ".DB_TBL_BANNED_USERS." WHERE username = '$subuser'";
         $database->query($q);
      }
      header("Location: ".$session->referrer);
   }

   /**
    * editAccount - Attempts to edit the user's account information including the
    * password, which it first makes sure is correct if entered, if so and the new
    * password is in the right format, the change is made. All other fields are changed
    * automatically.
    */
   function editAccount($subuid, $subuser, $subnewpass, $subemail, $subname, $subbirth, $subaddr, $subsex, $subphone, $subulevel) {
      global $database, $form, $session;  //The database and form object

      /* New Password error checking */
      if($subnewpass) {
         $field = "ednewpass";  //Use field name for new password

         /* Spruce up password*/
         $subnewpass = stripslashes($subnewpass);

         /* Check if password is not alphanumeric */
         if(!eregi("^([0-9a-z])+$", ($subnewpass = trim($subnewpass)))) {
            $form->setError($field, "* New Password not alphanumeric");
         }
      }
      
      /* Email error checking */
      if($subemail && strlen($subemail = trim($subemail)) > 0) {
         $field = "edemail";  //Use field name for email

         $subemail = stripslashes($subemail);
      }

      if($form->num_errors > 0) { /* Errors exist, have user correct them */
         return false;
      }

      /* Update password since there were no errors */
      if($subnewpass) {
         $database->updateUserField($subuid,"password",$session->hashPassword($subnewpass));
      }

      if($subemail) { /* Change Email */
         $database->updateUserField($subuid,"email",$subemail);
      }

      if($subname) { /* Change Name */
         $database->updateUserField($subuid,"fullname",$subname);
      }

      if($subbirth) { /* Change Birthdate */
         $database->updateUserField($subuid,"birthdate",$subbirth);
      }

      if($subaddr) { /* Change Address */
         $database->updateUserField($subuid,"address",$subaddr);
      }

      if($subsex) { /* Change Sex */
         $database->updateUserField($subuid,"sex",$subsex);
      }

      if($subphone) { /* Change Phone */
         $database->updateUserField($subuid,"phone",$subphone);
      }

      if($subulevel && $session->isAdmin()) { /* Change User Level */
         $database->updateUserField($subuid,"userlevel",(int)$subulevel);
      }
      return true; /* Success! */
   }

   /**
    * checkUsername - Helper function for the above processing, it makes sure the
    * submitted username is valid, if not, it adds the appropritate error to the form.
    */
   function checkUsername($uname, $ban=false) {
      global $database, $form;

      /* Username error checking */
      $subuser = $_POST[$uname];
      $field = $uname;  //Use field name for username

      if(!$subuser || strlen($subuser = trim($subuser)) == 0) {
         $form->setError($field, "* Username not entered<br>");
      } else {
         /* Make sure username is in database */
         $subuser = stripslashes($subuser);

         if(strlen($subuser) < 5 || strlen($subuser) > 30 ||
            !eregi("^([0-9a-z])+$", $subuser) ||
            (!$ban && !$database->usernameTaken($subuser))) {
            $form->setError($field, "* Username does not exist<br />");
         }
      }
      return $subuser;
   }
};

/* Initialize process */
$adminprocess = new AdminProcess;
?>