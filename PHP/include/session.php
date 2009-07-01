<?php
/**
 * Session.php
 *
 * The Session class is meant to simplify the task of keeping track of logged in
 * users and also guests.
 */

if(strpos(strtolower($_SERVER['PHP_SELF']), 'session.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}

require_once("users.php");
require_once("database.php");
require_once("mailer.php");
require_once("form.php");

class Session {
   public $database;  //Connection to database
   public $user;      //User object for current user
   public $time;      //Time user was last active (page loaded)
   public $logged_in; //True if user is logged in, false otherwise
   public $url;       //The page url current being viewed
   public $referrer;  //Last recorded site page viewed
   public $form;
   /**
    * Note: referrer should really only be considered the actual page referrer
    * in process.php, any other time it may be inaccurate.
    */

   /* Class constructor */
   function __construct() {
   	$this->database = new MySQLDB;
   	date_default_timezone_set('America/New_York');
      $this->time = time();
      $this->startSession();
      $this->form = new Form;
   }

   /**
    * startSession - Performs all the actions necessary to initialize this
    * session object. Tries to determine if the user has logged in already, and
    * sets the variables accordingly. Also takes advantage of this page load to
    * update the active visitors tables.
    */
   function startSession() {
      session_start();   //Tell PHP to start the session

      /* Determine if user is logged in */
      $this->logged_in = $this->checkLogin();

      /* Set guest value to users not logged in, and update active guests table accordingly. */
      if(!$this->logged_in) {
         $this->user = new Guest($_SERVER['REMOTE_ADDR'], $this->time);
      } else { /* Update users last active timestamp */
         $this->database->addActiveUser($this->user->getUID(), $this->time);
      }

      /* Remove inactive visitors from database */
      $this->database->removeInactiveUsers();
      $this->database->removeInactiveGuests();


      if(isset($_SESSION['url'])) { /* Set referrer page */
         $this->referrer = $_SESSION['url'];
      } else {
         $this->referrer = "/";
      }

      /* Set current url */
      $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
   }

   /**
    * checkLogin - Checks if the user has already previously logged in, and a
    * session with the user has already been established. Also checks to see if
    * user has been remembered. If so, the database is queried to make sure of
    * the user's authenticity.  Returns true if the user has logged in.
    */
   function checkLogin() {
      /* Check if user has been remembered */
      if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
         $_SESSION['username'] = $_COOKIE['cookname'];
         $_SESSION['uid'] = $_COOKIE['cookid'];
      }

      /* Username and uid have been set and not guest */
      if(isset($_SESSION['username']) && isset($_SESSION['uid']) &&
         $_SESSION['username'] != GUEST) {
         /* Confirm that username and uid are valid */
         if($this->database->confirmUserUID($_SESSION['username'], $_SESSION['uid']) != 0) {
            /* Variables are incorrect, user not logged in */
            unset($_SESSION['username']);
            unset($_SESSION['uid']);
            return false;
         } else {/* User is logged in, set class variables */
            $info = array("uid"=>$_SESSION['uid'], "username"=>$_SESSION['username']);
            if($this->database->confirmUID($info['uid'], DB_TBL_ADMINS)) {
            	$this->user = new Administrator($info);
            } else if($this->database->confirmUID($info['uid'], DB_TBL_TELLERS)) {
            	$this->user = new Teller($info);
            } else {
            	$this->user = new Customer($info);
            }
            return true;
         }
      } else { /* User not logged in */
         return false;
      }
   }

   /**
    * hashPassword - Hash's the user's password for secure storage. Returns a
    * sha1 salted hash for a password.
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
    * form, this function checks the authenticity of that information in the
    * database and creates the session. Effectively logging in the user if all
    * goes well.
    */
   function login($subuname, $subpass, $subremember) {
      /* Username error checking */
      $field = "uname";  //Use field name for username

      if(!$subuname || strlen($subuname = trim($subuname)) == 0) {
         $this->form->setError($field, "* Username not entered");
      } else {
         /* Check if username is not alphanumeric */
         if(!eregi("^([0-9a-z])*$", $subuname)) {
            $this->form->setError($field, "* Username not alphanumeric");
         }
      }

      /* Password error checking */
      $field = "pass";  //Use field name for password
      if(!$subpass) {
         $this->form->setError($field, "* Password not entered");
      }

      /* Return if form errors exist */
      if($this->form->num_errors > 0) {
         return false;
      }

      /* Checks that username is in database and password is correct */
      $subuname = stripslashes($subuname);
      $userinfo = $this->database->getUserInfo($this->database->getUID($subuname), DB_TBL_CUSTOMERS);

      if($userinfo != null) {
         $salt = substr($userinfo['password'], 0, SALT_LENGTH);
         $salted = $this->hashPassword($subpass, $salt);
         $result = $this->database->confirmUserPass($subuname, $salted, DB_TBL_CUSTOMERS);
      } else {
         $result = 1;
      }

      /* Check error codes */
      if($result == 1) {
         $field = "uname";
         $this->form->setError($field, "* Username not found");
      } else if($result == 2) {
         $field = "pass";
         $this->form->setError($field, "* Invalid password");
      }

      if($this->form->num_errors > 0) { /* Return if form errors exist */
         return false;
      }

      /* Username and password correct, register session variables */
      $_SESSION['username'] = $userinfo['username'];
      $_SESSION['uid']   =  $userinfo['uid'];

      if($this->database->confirmUID($userinfo['uid'], DB_TBL_ADMINS)) {
      	$this->user = new Administrator($userinfo);
      } else if($this->database->confirmUID($userinfo['uid'], DB_TBL_TELLERS)) {
      	$this->user = new Teller($userinfo);
      } else {
      	$this->user = new Customer($userinfo);
      }

      $this->database->addActiveUser($this->uid, $this->time);
      $this->database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

      /**
       * The user has requested to be remembered, so we set two cookies. One to
       * hold their username, and one to hold their uid. It expires by the time
       * specified in config.php. Now, next time they come to the site, they
       * will be logged in automatically, but only if they didn't logout.
       */
      if($subremember) {
      	date_default_timezone_set('America/New_York');
         setcookie("cookname", $this->user->username, time()+COOKIE_EXPIRE, COOKIE_PATH);
         setcookie("cookid",   $this->user->getUID(),   time()+COOKIE_EXPIRE, COOKIE_PATH);
      }
      return true; /* Login completed successfully */
   }

   /**
    * logout - Gets called when the user wants to be logged out of the website.
    * It deletes any cookies that were stored on the users computer as a result
    * of him wanting to be remembered, and also unsets session variables and
    * demotes his usertype to guest.
    */
   function logout() {
      /**
       * Delete cookies - the time must be in the past, so just negate what you
       * added when creating the cookie.
       */
      if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
      	date_default_timezone_set('America/New_York');
         setcookie("cookname", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
         setcookie("cookid",   "", time()-COOKIE_EXPIRE, COOKIE_PATH);
      }

      /* Unset PHP session variables */
      unset($_SESSION['username']);
      unset($_SESSION['uid']);

      /* Reflect fact that user has logged out */
      $this->logged_in = false;

      /* Remove from active users table and add to active guests tables. */
      $this->database->removeActiveUser($this->user->username);
      $this->database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);

      /* Set user guest */
      $this->user = new Guest($_SERVER['REMOTE_ADDR'], $this->time);
   }

   /**
    * register - Gets called when the user has just submitted the registration
    * form. Determines if there were any errors with the entry fields, if so, it
    * records the errors and returns 1. If no errors were found, it registers
    * the new user and returns 0. Returns 2 if registration failed.
    */
   function register($subInfo) {
      global $mailer;

      /* Username error checking */
      $field = "reguname";
      $subInfo['username'] = stripslashes($subInfo['username']);

      /* Check if username is not alphanumeric */
      if(!eregi("^([0-9a-z])+$", $subInfo['username'])) {
         $this->form->setError($field, "* Username not alphanumeric");
      } else if(strcasecmp($subInfo['username'], GUEST) == 0) {
      /* Check if username is reserved */
         $this->form->setError($field, "* Username reserved word");
      } else if($this->database->usernameTaken($subInfo['username'])) {
      /* Check if username is already in use */
         $this->form->setError($field, "* Username already in use");
      } else if($this->database->usernameBanned($subInfo['username'])) {
      /* Check if username is banned */
         $this->form->setError($field, "* Username banned");
      }

      /* Password error checking */
      $field = "regpass";  //Use field name for password

      $subInfo['passconf'] = trim($subInfo['passconf']);
      if(strcasecmp($subInfo['password'], $subInfo['passconf']) != 0) {
         $this->form->setError("regpassconf", "* Enter the same password as above");
      }

      /* Check if password is not alphanumeric */
      if(!eregi("^([0-9a-z])+$", ($subInfo['password'] = trim($subInfo['password'])))) {
         $this->form->setError($field, "* Password not alphanumeric");
      } else { //valid password, encrypt it now (save copy for welcome email)
         $subpass = $subInfo['password'];
      	$subInfo['password'] = $this->hashPassword($subInfo['password']);
      }

      /* Email error checking */
      $field = "regemail";
      $subInfo['email'] = stripslashes($subInfo['email']);

      if(!eregi("^([0-9a-zA-Z]+[-._+])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}$", $subInfo['email'])) {
      	$this->form->setError($field, "* Email not valid");
      }

      /* Errors exist, have user correct them */
      if($this->form->num_errors > 0) {
         return 1;  //Errors with form
      } else { /* No errors, add the new account to the */
         /* The first user to register will default to an administrator. */
         if($this->database->getNumCustomers()) {
         	if($this->isAdmin()){
               $usertype = $subInfo['usertype']; //administrator registering new user
         	} else {
         		$usertype = CUST; //user registering isn't an admin, restrict to creating customer only
         	}
         } else {
            $usertype = ADMIN; // no users in database yet, create administrator
         }

         date_default_timezone_set('America/New_York');
         $subInfo['regtime'] = time();
         if($usertype == CUST) {
         	$this->user = new Customer($subInfo);
         } else if($usertype == TELLER) {
         	$this->user = new Teller($subInfo, array("hiredate"=>time()));
         } else if($usertype == ADMIN) {
            $this->user = new Administrator($subInfo, array("hiredate"=>time()));
         }

         if($this->user->register()) {
            if(EMAIL_WELCOME) {
               $mailer->sendWelcome($subInfo['username'],$subInfo['email'],$subpass);
            }
            return 0;  //New user added succesfully
         } else {
            return 2;  //Registration attempt failed
         }
      }
   }

   /**
    * isAdmin - Returns true if currently logged in user is an administrator,
    * false otherwise.
    */
   function isAdmin() {
      return (get_class($this->user) == ADMIN);
   }

   /**
    * isTeller - Returns true if currently logged in user is a teller,
    * false otherwise.
    */
   function isTeller() {
      return (get_class($this->user) == TELLER);
   }

   /**
    * isCustomer - Returns true if currently logged in user is a customer, false
    * otherwise. NOTE: All users are considered customers except guests.
    */
   function isCustomer() {
      return ((get_class($this->user) == CUST) ||
              (get_class($this->user) == TELLER) ||
              (get_class($this->user) == ADMIN));
   }

   /**
    * generateRandStr - Generates a string made up of randomized letters (lower
    * and upper case) and digits, the length is a specified parameter.
    */
   function generateRandStr($length) {
      $randstr = "";
      for($i=0; $i<$length; $i++) {
         $randnum = mt_rand(0,61);
         if($randnum < 10) { //0-9
            $randstr .= chr($randnum+48);
         } else if($randnum < 36) { //uppercase
            $randstr .= chr($randnum+55);
         } else { //lowercase
            $randstr .= chr($randnum+61);
         }
      }
      return $randstr;
   }
};

/**
 * Initialize session object - This must be initialized before the form object
 * because the form uses session variables, which cannot be accessed unless the
 * session has started.
 */
$session = new Session;
?>