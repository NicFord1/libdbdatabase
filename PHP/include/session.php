<?
/**
 * Session.php
 * 
 * The Session class is meant to simplify the task of keeping track of logged in
 * users and also guests.
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'session.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}

include("database.php");
include("mailer.php");
include("form.php");

class Session {
   var $username;            //Username given on sign-up
   var $uid;                 //Unique Identifier for current user
   var $userlevel;           //The level to which the user pertains
   var $time;                //Time user was last active (page loaded)
   var $logged_in;           //True if user is logged in, false otherwise
   var $userinfo = array();  //The array holding all user info
   var $url;                 //The page url current being viewed
   var $referrer;            //Last recorded site page viewed
   /**
    * Note: referrer should really only be considered the actual page
    * referrer in process.php, any other time it may be inaccurate.
    */

   /* Class constructor */
   function Session() {
      $this->time = time();
      $this->startSession();
   }

   /**
    * startSession - Performs all the actions necessary to initialize this session object.
    * Tries to determine if the user has logged in already, and sets the variables accordingly.
    * Also takes advantage of this page load to update the active visitors tables.
    */
   function startSession() {
      global $database;  //The database connection
      session_start();   //Tell PHP to start the session

      /* Determine if user is logged in */
      $this->logged_in = $this->checkLogin();

      /* Set guest value to users not logged in, and update active guests table accordingly. */
      if(!$this->logged_in) {
         $this->username = $_SESSION['username'] = GUEST_NAME;
         $this->userlevel = GUEST_LEVEL;
         $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
      } else { /* Update users last active timestamp */
         $database->addActiveUser($this->uid, $this->time);
      }
      
      /* Remove inactive visitors from database */
      $database->removeInactiveUsers();
      $database->removeInactiveGuests();
      
    
      if(isset($_SESSION['url'])) { /* Set referrer page */
         $this->referrer = $_SESSION['url'];
      } else {
         $this->referrer = "/";
      }

      /* Set current url */
      $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
   }

   /**
    * checkLogin - Checks if the user has already previously logged in, and a session with
    * the user has already been established. Also checks to see if user has been remembered.
    * If so, the database is queried to make sure of the user's authenticity.  Returns true
    * if the user has logged in.
    */
   function checkLogin() {
      global $database;  //The database connection

      /* Check if user has been remembered */
      if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
         $this->username = $_SESSION['username'] = $_COOKIE['cookname'];
         $this->uid = $_SESSION['uid'] = $_COOKIE['cookid'];
      }

      /* Username and uid have been set and not guest */
      if(isset($_SESSION['username']) && isset($_SESSION['uid']) &&
         $_SESSION['username'] != GUEST_NAME) {
         /* Confirm that username and uid are valid */
         if($database->confirmUID($_SESSION['username'], $_SESSION['uid']) != 0) {
            /* Variables are incorrect, user not logged in */
            unset($_SESSION['username']);
            unset($_SESSION['uid']);
            return false;
         }

         /* User is logged in, set class variables */
         $this->userinfo = $database->getUserInfo($_SESSION['uid']);
         $this->username = $this->userinfo['username'];
         $this->uid = $this->userinfo['uid'];
         $this->userlevel = $this->userinfo['userlevel'];
         return true;
      } else { /* User not logged in */
         return false;
      }
   }

   /**
    * hashPassword - Hash's the user's password for secure storage.
    * Returns a sha1 salted hash for a password.
    */
   function hashPassword($plainText, $salt = null) {
      if ($salt == null) {
         $salt = substr(md5(time() * rand()), 0, SALT_LENGTH);
      } else {
         $salt = substr($salt, 0, SALT_LENGTH);
      }

      // return a sha1 salted hash password
      return $salt . sha1($salt . $plainText);
   }

   /**
    * login - The user has submitted his username and password through the login
    * form, this function checks the authenticity of that information in the database
    * and creates the session. Effectively logging in the user if all goes well.
    */
   function login($subuser, $subpass, $subremember) {
      global $database, $form;  //The database and form object

      /* Username error checking */
      $field = "user";  //Use field name for username

      if(!$subuser || strlen($subuser = trim($subuser)) == 0) {
         $form->setError($field, "* Username not entered");
      } else {
         /* Check if username is not alphanumeric */
         if(!eregi("^([0-9a-z])*$", $subuser)) {
            $form->setError($field, "* Username not alphanumeric");
         }
      }

      /* Password error checking */
      $field = "pass";  //Use field name for password
      if(!$subpass) {
         $form->setError($field, "* Password not entered");
      }
      
      /* Return if form errors exist */
      if($form->num_errors > 0) {
         return false;
      }

      /* Checks that username is in database and password is correct */
      $subuser = stripslashes($subuser);
      $this->userinfo  = $database->getUserInfo($database->getUID($subuser));

      if($this->userinfo != null) {
         $salt = substr($this->userinfo['password'], 0, SALT_LENGTH);
         $salted = $this->hashPassword($subpass, $salt);
         $result = $database->confirmUserPass($subuser, $salted);
      } else {
         $result = 1;
      }

      /* Check error codes */
      if($result == 1) {
         $field = "user";
         $form->setError($field, "* Username not found");
      } else if($result == 2) {
         $field = "pass";
         $form->setError($field, "* Invalid password");
      }
      
    
      if($form->num_errors > 0) { /* Return if form errors exist */
         return false;
      }

      /* Username and password correct, register session variables */
      $this->username  = $_SESSION['username'] = $this->userinfo['username'];
      $this->uid    = $_SESSION['uid']   = $this->userinfo['uid'];
      $this->userlevel = $this->userinfo['userlevel'];
      
      $database->addActiveUser($this->uid, $this->time);
      $database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

      /**
       * This is the cool part: the user has requested that we remember that
       * they are logged in, so we set two cookies. One to hold their username,
       * and one to hold their uid. It expires by the time specified in config.php.
       * Now, next time they come to our site, we will log them in automatically,
       * but only if they didn't log out before they left.
       */
      if($subremember) {
         setcookie("cookname", $this->username, time()+COOKIE_EXPIRE, COOKIE_PATH);
         setcookie("cookid",   $this->uid,   time()+COOKIE_EXPIRE, COOKIE_PATH);
      }
      return true; /* Login completed successfully */
   }

   /**
    * logout - Gets called when the user wants to be logged out of the
    * website. It deletes any cookies that were stored on the users
    * computer as a result of him wanting to be remembered, and also
    * unsets session variables and demotes his user level to guest.
    */
   function logout() {
      global $database;  //The database connection

      /**
       * Delete cookies - the time must be in the past, so just negate what you added
       * when creating the cookie.
       */
      if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
         setcookie("cookname", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
         setcookie("cookid",   "", time()-COOKIE_EXPIRE, COOKIE_PATH);
      }

      /* Unset PHP session variables */
      unset($_SESSION['username']);
      unset($_SESSION['uid']);

      /* Reflect fact that user has logged out */
      $this->logged_in = false;
      
      /* Remove from active users table and add to active guests tables. */
      $database->removeActiveUser($this->username);
      $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
      
      /* Set user level to guest */
      $this->username  = GUEST_NAME;
      $this->userlevel = GUEST_LEVEL;
   }

   /**
    * register - Gets called when the user has just submitted the registration form.
    * Determines if there were any errors with the entry fields, if so, it records
    * the errors and returns 1. If no errors were found, it registers the new user
    * and returns 0. Returns 2 if registration failed.
    */
   function register($subuser, $subpass, $subemail, $subname, $subbirth, $subaddr, $subsex, $subphone, $subulevel) {
      global $database, $form, $mailer;  //The database, form and mailer object
      
      /* Username error checking */
      $field = "reguser";  //Use field name for username

      $subuser = stripslashes($subuser);

      /* Check if username is not alphanumeric */
      if(!eregi("^([0-9a-z])+$", $subuser)) {
         $form->setError($field, "* Username not alphanumeric");
      } else if(strcasecmp($subuser, GUEST_NAME) == 0) {
      /* Check if username is reserved */
         $form->setError($field, "* Username reserved word");
      } else if($database->usernameTaken($subuser)) {
      /* Check if username is already in use */
         $form->setError($field, "* Username already in use");
      } else if($database->usernameBanned($subuser)) {
      /* Check if username is banned */
         $form->setError($field, "* Username banned");
      }

      /* Password error checking */
      $field = "regpass";  //Use field name for password

      /* Spruce up password and check length*/
      $subpass = stripslashes($subpass);

      /* Check if password is not alphanumeric */
      if(!eregi("^([0-9a-z])+$", ($subpass = trim($subpass)))) {
         $form->setError($field, "* Password not alphanumeric");
      }
      
      $subemail = stripslashes($subemail);

      /* Errors exist, have user correct them */
      if($form->num_errors > 0) {
         return 1;  //Errors with form
      } else { /* No errors, add the new account to the */
         /* The first user to register will default to an administrator. */
         if($database->getNumMembers()) {
            $ulevel = $subulevel;
         } else {
            $ulevel = ADMIN_LEVEL; // no users in database yet, create admin
         }

         if($database->addNewUser($subuser, $this->hashPassword($subpass), $subemail, $subname, $subbirth, $subaddr, $subsex, $subphone, $ulevel)) {
            if(EMAIL_WELCOME) {
               $mailer->sendWelcome($subuser,$subemail,$subpass);
            }
            return 0;  //New user added succesfully
         } else {
            return 2;  //Registration attempt failed
         }
      }
   }
   /**
    * editAccount - Attempts to edit the user's account information including the
    * password, which it first makes sure is correct if entered, if so and the new
    * password is in the right format, the change is made. All other fields are changed
    * automatically.
    */
   function editAccount($subuser, $subcurpass, $subnewpass, $subemail, $subname, $subbirth, $subaddr, $subsex, $subphone, $subulevel) {
      global $database, $form;  //The database and form object

      /* Current Password error checking */
      $field = "edcurpass";  //Use field name for current password

      /* Current Password entered */
      if(!$subcurpass) {
         $form->setError($field, "* Current Password not entered");
      } else {
         $subcurpass = stripslashes($subcurpass);

         $salt = substr($this->userinfo['password'], 0, SALT_LENGTH);
         $result = $database->confirmUserPass($this->username, $this->hashPassword($subcurpass, $salt));

         if($result != 0) { /* Password entered is incorrect */
            $form->setError($field, "* Current Password incorrect");
         }
      }

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
      if($subcurpass && $subnewpass) {
         $database->updateUserField($this->uid,"password",$this->hashPassword($subnewpass));
      }

      if($subemail) { /* Change Email */
         $database->updateUserField($this->uid,"email",$subemail);
      }

      if($subname) { /* Change Name */
         $database->updateUserField($this->uid,"fullname",$subname);
      }

      if($subbirth) { /* Change Birthdate */
         $database->updateUserField($this->uid,"birthdate",$subbirth);
      }

      if($subaddr) { /* Change Address */
         $database->updateUserField($this->uid,"address",$subaddr);
      }

      if($subsex) { /* Change Sex */
         $database->updateUserField($this->uid,"sex",$subsex);
      }

      /* Change Phone */
      if($subphone) {
         $database->updateUserField($this->uid,"phone",$subphone);
      }

      if($subulevel) { /* Change Name */
         $database->updateUserField($this->uid,"userlevel",$subulevel);
      }
      return true; /* Success! */
   }
   
   /**
    * isAdmin - Returns true if currently logged in user is an administrator, false otherwise.
    */
   function isAdmin() {
      return ($this->userlevel == ADMIN_LEVEL);
   }
   
   /**
    * isTeller - Returns true if currently logged in user is a teller, false otherwise.
    */
   function isTeller() {
      return ($this->userlevel == TELLER_LEVEL);
   }

   /**
    * isCustomer - Returns true if currently logged in user is a customer, false otherwise.
    * NOTE: All users are considered customers except guests.
    */
   function isCustomer() {
      return ($this->userlevel >= CUST_LEVEL);
   }

   /**
    * generateRandID - Generates a string made up of randomized letters (lower and upper case)
    * and digits and returns the md5 hash of it to be used as a uid.
    */
   function generateRandID() {
      return md5($this->generateRandStr(16));
   }
   
   /**
    * generateRandStr - Generates a string made up of randomized
    * letters (lower and upper case) and digits, the length
    * is a specified parameter.
    */
   function generateRandStr($length) {
      $randstr = "";
      for($i=0; $i<$length; $i++) {
         $randnum = mt_rand(0,61);
         if($randnum < 10) {
            $randstr .= chr($randnum+48);
         } else if($randnum < 36) {
            $randstr .= chr($randnum+55);
         } else {
            $randstr .= chr($randnum+61);
         }
      }
      return $randstr;
   }
};

/**
 * Initialize session object - This must be initialized before the form object because the
 * form uses session variables, which cannot be accessed unless the session has started.
 */
$session = new Session;

/* Initialize form object */
$form = new Form;
?>