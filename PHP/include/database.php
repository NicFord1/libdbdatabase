<?
/**
 * database.php
 * 
 * The Database class is meant to simplify the task of accessing
 * information from the website's database.
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'database.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}

require_once("db-config.php");
require_once("config.php");
      
class MySQLDB {
   var $connection;         //The MySQL database connection
   var $num_active_users;   //Number of active users viewing site
   var $num_active_guests;  //Number of active guests viewing site
   var $num_admins;         //Number of administrators
   var $num_members;        //Number of signed-up users
   /* Note: call getNumAdmins() or getNumMembers() to access $num_admins or $num_members! */

   /* Class constructor */
   function MySQLDB() {
      /* Make connection to database */
      $this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
      mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());
      
      /**
       * Only query database to find out number of members when getNumMembers() is
       * called for the first time, until then, default value set.
       */
      $this->num_admins = -1;
      $this->num_members = -1;
      
      if(TRACK_VISITORS) { /* Calculate number of users & guests on site. */
         $this->calcNumActiveUsers();
         $this->calcNumActiveGuests();
      }
   }

   /**
    * confirmUserPass - Checks whether or not the given username is in the database, if
    * so it checks if the given password is the same password in the database for that
    * user. If the user doesn't exist or if the passwords don't match up, it returns an
    * error code (1 or 2). On success it returns 0.
    */
   function confirmUserPass($username, $password, $table) {
      if(!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT password FROM $table WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      if(!$result || (mysql_numrows($result) < 1)) {
         return 1; //Indicates username failure
      }

      /* Retrieve password from result, strip slashes */
      $dbarray = mysql_fetch_array($result);
      $dbarray['password'] = stripslashes($dbarray['password']);
      $password = stripslashes($password);

      if($password == $dbarray['password']) { /* Validate that password is correct */
         return 0; //Success! Username and password confirmed
      } else {
         return 2; //Indicates password failure
      }
   }
   
   /**
    * confirmUserUID - Checks whether or not the given username is in the database, if so it checks if the
    * given uid is the same uid in the database for that user. If the user doesn't exist or if the
    * uids don't match up, it returns an error code (1 or 2). On success it returns 0.
    */
   function confirmUserUID($username, $uid, $table) {
      if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT uid FROM $table WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      if(!$result || (mysql_numrows($result) < 1)) {
         return 1; //Indicates username failure
      }

      /* Retrieve uid from result, strip slashes */
      $dbarray = mysql_fetch_array($result);
      $dbarray['uid'] = stripslashes($dbarray['uid']);
      $uid = stripslashes($uid);

      if($uid == $dbarray['uid']) { /* Validate that uid is correct */
         return 0; //Success! UID confirmed
      } else {
         return 2; //Indicates UID invalid
      }
   }

   /**
    * confirmUID - Checks whether or not the given uid is in the database. If the user doesn't exist or if the
    * uids don't match up, it returns an error code. On success it returns 0.
    */
   function confirmUID($uid, $table) {
      /* Verify that user is in database */
      $q = "SELECT * FROM $table WHERE uid = '$uid'";
      $result = mysql_query($q, $this->connection);
      if(!$result || (mysql_numrows($result) < 1)) {
         return false; //Indicates uid failure
      } else {
         return true; //Success! UID confirmed
      }
   }
   
   /**
    * usernameTaken - Returns true if the username has been taken by another user, false otherwise.
    */
   function usernameTaken($username) {
      if(!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }

      $q = "SELECT username FROM ".DB_TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      return (mysql_numrows($result) > 0);
   }
   
   /**
    * usernameBanned - Returns true if the username has been banned by the administrator.
    */
   function usernameBanned($username) {
      if(!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }

      $q = "SELECT username FROM ".DB_TBL_BANNED_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      return (mysql_numrows($result) > 0);
   }
   
   /**
    * addNewUser - Inserts the given (username, password, email, etc) info into the
    * database. Appropriate user level is set. Returns true on success, false otherwise.
    */
   function addNewUser($username, $password, $email, $fullname, $birthdate, $address, $sex, $phone, $ulevel) {
      $time = time();
      $q = "INSERT INTO ".DB_TBL_USERS." (username, fullname, password, userlevel, email, regtime, birthdate, sex, address, phone) VALUES ('$username', '$fullname', '$password', $ulevel, '$email', $time, '$birthdate', '$sex', '$address', '$phone')";

      return mysql_query($q, $this->connection);
   }
   
   /**
    * updateUserField - Updates a field, specified by the field parameter, in the user's row
    * in the given table in the database.
    */
   function updateUserField($uid, $table, $field, $value) {
      $q = "UPDATE ".$table." SET ".$field." = '$value' WHERE uid = '$uid'";
      return mysql_query($q, $this->connection);
   }
   
   /**
    * getUID - Returns the result array from a mysql query asking for the uid for the given
    * username. If query fails, NULL is returned.
    */
   function getUID($username) {
      $q = "SELECT uid FROM ".DB_TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);

      /* Error occurred, return given name by default */
      if(!$result || (mysql_numrows($result) < 1)) {
         return NULL;
      }
      return mysql_result($result, 0);
   }

   /**
    * getUserInfo - Returns the result array from a mysql query asking for all information stored
    * regarding the given UID. If query fails, NULL is returned.
    */
   function getUserInfo($uid, $table) {
      $q = "SELECT * FROM $table WHERE uid = '$uid'";
      $result = mysql_query($q, $this->connection);

      if(!$result || (mysql_numrows($result) < 1)) { /* Error occurred, return given name by default */
         return NULL;
      }

      /* Return result array */
      $dbarray = mysql_fetch_array($result);
      return $dbarray;
   }
   
   /**
    * getNumAdmins - Returns the number of administrators users. The first time the function is called
    * on page load, the database is queried, on subsequent calls, the store result is returned.
    * This is to improve efficiency, effectively not querying the database when no call is made.
    */
   function getNumAdmins() {
      if($this->num_admins < 0) {
         $q = "SELECT * FROM ".DB_TBL_USERS." WHERE userlevel = '".ADMIN_LEVEL."'";
         $result = mysql_query($q, $this->connection);
         $this->num_admins = mysql_numrows($result);
      }
      return $this->num_admins;
   }

   /**
    * getNumMembers - Returns the number of signed-up users of the website, banned members
    * not included. The first time the function is called on page load, the database is
    * queried, on subsequent calls, the stored result is returned. This is to improve
    * efficiency, effectively not querying the database when no call is made.
    */
   function getNumMembers() {
      if($this->num_members < 0) {
         $q = "SELECT * FROM ".DB_TBL_USERS;
         $result = mysql_query($q, $this->connection);
         $this->num_members = mysql_numrows($result);
      }
      return $this->num_members;
   }
   
   /**
    * calcNumActiveUsers - Finds out how many active users are viewing site and sets
    * class variable accordingly.
    */
   function calcNumActiveUsers() {
      /* Calculate number of users at site */
      $q = "SELECT * FROM ".DB_TBL_ACTIVE_USERS;
      $result = mysql_query($q, $this->connection);
      $this->num_active_users = mysql_numrows($result);
   }
   
   /**
    * calcNumActiveGuests - Finds out how many active guests are viewing site and sets
    * class variable accordingly.
    */
   function calcNumActiveGuests(){
      /* Calculate number of guests at site */
      $q = "SELECT * FROM ".DB_TBL_ACTIVE_GUESTS;
      $result = mysql_query($q, $this->connection);
      $this->num_active_guests = mysql_numrows($result);
   }
   
   /**
    * addActiveUser - Updates user's last active timestamp in the database, and also
    * adds them to the table of active users, or updates timestamp if already there.
    */
   function addActiveUser($uid, $time) {
      $q = "UPDATE ".DB_TBL_USERS." SET lastvisit = '$time' WHERE uid = '$uid'";
      mysql_query($q, $this->connection);
      
      if(!TRACK_VISITORS) return;
      $q = "REPLACE INTO ".DB_TBL_ACTIVE_USERS." VALUES ('$uid', '$time')";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }
   
   /* addActiveGuest - Adds guest to active guests table */
   function addActiveGuest($ip, $time) {
      if(!TRACK_VISITORS) return;
      $q = "REPLACE INTO ".DB_TBL_ACTIVE_GUESTS." VALUES ('$ip', '$time')";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }
   
   /* removeActiveUser */
   function removeActiveUser($uid) {
      if(!TRACK_VISITORS) return;
      $q = "DELETE FROM ".DB_TBL_ACTIVE_USERS." WHERE uid = '$uid'";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }

   /* removeActiveGuest */
   function removeActiveGuest($ip) {
      if(!TRACK_VISITORS) return;
      $q = "DELETE FROM ".DB_TBL_ACTIVE_GUESTS." WHERE ip = '$ip'";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }

   /* removeInactiveUsers */
   function removeInactiveUsers() {
      if(!TRACK_VISITORS) return;
      $timeout = time()-USER_TIMEOUT*60;
      $q = "DELETE FROM ".DB_TBL_ACTIVE_USERS." WHERE timestamp < $timeout";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }

   /* removeInactiveGuests */
   function removeInactiveGuests() {
      if(!TRACK_VISITORS) return;
      $timeout = time()-GUEST_TIMEOUT*60;
      $q = "DELETE FROM ".DB_TBL_ACTIVE_GUESTS." WHERE timestamp < $timeout";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }

   /**
    * query - Performs the given query on the database and returns the result, which
    * may be false, true or a resource identifier.
    */
   function query($query) {
      return mysql_query($query, $this->connection);
   }
};

/* Create database connection */
$database = new MySQLDB;
?>