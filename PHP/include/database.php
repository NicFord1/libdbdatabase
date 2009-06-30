<?php
/**
 * database.php
 *
 * The Database class is meant to simplify the task of accessing information
 * from the website's database.
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'database.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}

require_once("db-config.php");
require_once("config.php");

class MySQLDB {
   public $connection;           //The MySQL database connection
   private $_numActiveCustomers; //Number of active customers viewing site
   private $_numActiveGuests;    //Number of active guests viewing site
   private $_numAdmins;          //Number of administrators
   private $_numCustomers;       //Number of signed-up customers

   /**
    * Constructor for the Database Class.
    */
   function __construct() {
      /* Make connection to database */
      $this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
      mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());

      /**
       * Only query database to find out number of Customers when
       * getNumCustomers() is called for the first time, until then, default
       * value set.
       */
      $this->_numAdmins = -1;
      $this->_numCustomers = -1;

      if(TRACK_VISITORS) { /* Calculate number of users & guests on site. */
         $this->calcNumActiveUsers();
         $this->calcNumActiveGuests();
      }
   }

   /**
    * confirmUserPass - Checks whether or not the given username is in the
    * database, if so it checks if the given password is the same password in
    * the database for that user. If the user doesn't exist or if the passwords
    * don't match up, it returns an error code (1 or 2). On success it returns
    * 0.
    */
   public function confirmUserPass($username, $password) {
      if(!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT password FROM ".DB_TBL_CUSTOMERS." WHERE username = '$username'";
      $result = $this->query($q);
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
    * confirmUserUID - Checks whether or not the given username is in the
    * database, if so it checks if the given uid is the same uid in the database
    * for that user. If the user doesn't exist or if the uids don't match up, it
    * returns an error code (1 or 2). On success it returns 0.
    */
   public function confirmUserUID($username, $uid) {
      if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT uid FROM ".DB_TBL_CUSTOMERS." WHERE username = '$username'";
      $result = $this->query($q);
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
    * confirmUID - Checks whether or not the given uid is in the database. If
    * the uid doesn't exist it returns false, true otherwise.
    */
   public function confirmUID($uid, $table) {
      if(!get_magic_quotes_gpc()) {
         $uid = addslashes($uid);
         $table = addslashes($table);
      }

      /* Verify that user is in database */
      $q = "SELECT * FROM $table WHERE uid = '$uid'";
      $result = $this->query($q);
      if(!$result || (mysql_numrows($result) < 1)) {
         return false; //Indicates uid failure
      } else {
         return true; //Success! UID confirmed
      }
   }

   /**
    * confirmItemID - Checks whether or not the given itemID is in the database.
    * If the itemID doesn't exist it returns false, true otherwise.
    */
   public function confirmItemID($itemID, $table) {
      if(!get_magic_quotes_gpc()) {
         $itemID = addslashes($itemID);
         $table = addslashes($table);
      }

      /* Verify that item is in database */
      $q = "SELECT * FROM $table WHERE itemid = '$itemID'";
      $result = $this->query($q);
      if(!$result || (mysql_numrows($result) < 1)) {
         return false; //Indicates itemID failure
      } else {
         return true; //Success! itemID confirmed
      }
   }

   /**
    * usernameTaken - Returns true if the username has been taken by another
    * user, false otherwise.
    */
   public function usernameTaken($username) {
      if(!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }
      $q = "SELECT username FROM ".DB_TBL_CUSTOMERS." WHERE username = '$username'";
      $result = $this->query($q);
      return (mysql_numrows($result) > 0);
   }

   /**
    * usernameBanned - Returns true if the username has been banned by an admin.
    */
   public function usernameBanned($username) {
      if(!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }
      $q = "SELECT username FROM ".DB_TBL_BANNED_USERS." WHERE username = '$username'";
      $result = $this->query($q);
      return (mysql_numrows($result) > 0);
   }

   /**
    * hireTeller - Inserts the given uid and current timestamp (date of hire)
    * into database. Appropriate usertype is set. Returns true on success,
    * false otherwise.
    */
   public function hireTeller($uid, $hireTime = NULL) {
      if(!get_magic_quotes_gpc()) {
         $uid = addslashes($uid);
         $hireTime = addslashes($hireTime);
      }

      if(!$hireTime) {
      	$hireTime = time();
      }

      if($this->confirmUID($uid, DB_TBL_CUSTOMERS)) { //Existing Customer?
         if($this->confirmUID($uid, DB_TBL_ADMINS)) { //Check if only Admin
         	$retval = $this->removeUser($uid, DB_TBL_ADMINS);
            if($retval) { //wasn't only admin
            	$q = "INSERT INTO ".DB_TBL_TELLERS." VALUES ('$uid', '$hireTime')";
               $retval = $this->query($q);
            }
         } else { //wasn't an admin
         	$q = "INSERT INTO ".DB_TBL_TELLERS." VALUES ('$uid', '$hireTime')";
            $retval = $this->query($q);
         }
      } else { //not an existing customer
         $retval = false;
      }
      return $retval;
   }

   /**
    * hireAdministrator - Inserts the given uid and current timestamp (date of
    * hire) into database. Appropriate usertype is set. Returns true on
    * success, false otherwise.
    */
   public function hireAdministrator($uid, $hireTime = NULL) {
      if(!get_magic_quotes_gpc()) {
         $uid = addslashes($uid);
         $hireTime = addslashes($hireTime);
      }

      if(!$hireTime) {
      	$hireTime = time();
      }

      if($this->confirmUID($uid, DB_TBL_CUSTOMERS)) { //Existing Customer?
      	$q = "INSERT INTO ".DB_TBL_ADMINS." VALUES ('$uid', '$hireTime')";
         $retval = $this->query($q);

         if($retval) { //keep a 'clean' database
         	$this->removeUser($uid, DB_TBL_TELLERS);
         }
      } else {
      	$retval = false;
      }
      return $retval;
   }

   public function removeUser($uid, $table) {
      if(!get_magic_quotes_gpc()) {
         $uid = addslashes($uid);
         $table = addslashes($table);
      }

   	//Make sure if user is the only Administrator, they aren't removed.
   	if($table == DB_TBL_ADMINS) {
         if($this->getNumAdmins() < 2) {
         	$q = false;
         } else {
            $q = "DELETE FROM $table WHERE uid = '$uid'";
         }
   	} else if($table == DB_TBL_TELLERS) {
   		$q = "DELETE FROM $table WHERE uid = '$uid'";
   	} else if($table == DB_TBL_CUSTOMERS) {
   		if($this->confirmUID($uid, DB_TBL_ADMINS)) {
            if($this->getNumAdmins() < 2) {//don't remove if only admin
            	$q = false;
            } else { //remove from both customer and admin tables
               $this->removeUser($uid, DB_TBL_ADMINS);
            	$q = "DELETE FROM $table WHERE uid = '$uid'";
            }
   		} else if($this->confirmUID($uid, DB_TBL_TELLERS)) {
            $this->removeUser($uid, DB_TBL_TELLERS);
   			$q = "DELETE FROM $table WHERE uid = '$uid'";
   		} else {
   			$q = "DELETE FROM $table WHERE uid = '$uid'";
   		}
   	} else {
   		$q = false;
   	}
   	return $this->query($q);
   }

   /**
    * updateUserField - Updates a field, specified by the field parameter, in
    * the user's row in the given table in the database.
    */
   function updateUserField($uid, $table, $field, $value) {
      if(!get_magic_quotes_gpc()) {
         $uid = addslashes($uid);
         $table = addslashes($table);
         $field = addslashes($field);
         $value = addslashes($value);
      }

      $q = "UPDATE ".$table." SET ".$field." = '$value' WHERE uid = '$uid'";
      return $this->query($q);
   }

   /**
    * updateItemField - Updates a field, specified by the field parameter, in
    * the item's row in the given table in the database.
    */
   function updateItemField($itemID, $table, $field, $value) {
      if(!get_magic_quotes_gpc()) {
         $itemID = addslashes($itemID);
         $table = addslashes($table);
         $field = addslashes($field);
         $value = addslashes($value);
      }

      $q = "UPDATE ".$table." SET ".$field." = '$value' WHERE itemid = '$itemID'";
      return $this->query($q);
   }

   /**
    * getUID - Returns the result array from a mysql query asking for the uid
    * for the given username. If query fails, NULL is returned.
    */
   function getUID($username) {
      if(!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }

      $q = "SELECT uid FROM ".DB_TBL_CUSTOMERS." WHERE username = '$username'";
      $result = $this->query($q);

      /* Error occurred, return given name by default */
      if(!$result || (mysql_numrows($result) < 1)) {
         return NULL;
      }
      return mysql_result($result, 0);
   }

   /**
    * getUserInfo - Returns the result array from a mysql query asking for all
    * information stored regarding the given UID. If query fails, NULL is
    * returned.
    */
   function getUserInfo($uid, $table) {
      if(!get_magic_quotes_gpc()) {
         $uid = addslashes($uid);
         $table = addslashes($uid);
      }

      $q = "SELECT * FROM $table WHERE uid = '$uid'";
      $result = $this->query($q);

      if(!$result || (mysql_numrows($result) < 1)) { /* Error occurred, return given name by default */
         return NULL;
      }
      return mysql_fetch_array($result);
   }

   /**
    * getItemInfo - Returns the result array from a mysql query asking for all
    * information stored regarding the given itemID. If query fails, Null is
    * returned.
    */
   function getItemInfo($itemID, $table) {
      if(!get_magic_quotes_gpc()) {
         $itemID = addslashes($itemID);
         $table = addslashes($table);
      }

	   $q = "SELECT * FROM $table WHERE itemid = '$itemID'";
	   $result = $this->query($q);

	   if(!$result || (mysql_numrows($result) < 1)) { /* Error occurred, return given name by default */
	      return NULL;
	   }
	   return mysql_fetch_array($result);
   }

   /**
    * getNumAdmins - Returns the number of administrators users. The first time
    * the function is called on page load, the database is queried, on
    * subsequent calls, the store result is returned. This is to improve
    * efficiency, effectively not querying the database when no call is made.
    */
   function getNumAdmins() {
      if($this->_numAdmins < 0) {
         $q = "SELECT * FROM ".DB_TBL_ADMINS;
         $result = $this->query($q);
         $this->_numAdmins = mysql_numrows($result);
      }
      return $this->_numAdmins;
   }

   /**
    * getNumTellers - Returns the number of teller users. The first time the
    * function is called on page load, the database is queried, on subsequent
    * calls, the store result is returned. This is to improve efficiency,
    * effectively not querying the database when no call is made.
    */
   function getNumTellers() {
      if($this->_numTellers < 0) {
         $q = "SELECT * FROM ".DB_TBL_TELLERS;
         $result = $this->query($q);
         $this->_numTellers = mysql_numrows($result);
      }
      return $this->_numTellers;
   }

   /**
    * getNumCustomers - Returns the number of signed-up users of the website,
    * banned Customers not included. The first time the function is called on
    * page load, the database is queried, on subsequent calls, the stored result
    * is returned. This is to improve efficiency, effectively not querying the
    * database when no call is made.
    */
   function getNumCustomers() {
      if($this->_numCustomers < 0) {
         $q = "SELECT * FROM ".DB_TBL_CUSTOMERS;
         $result = $this->query($q);
         $this->_numCustomers = mysql_numrows($result);
      }
      return $this->_numCustomers;
   }

   /**
    * addActiveUser - Updates user's last active timestamp in the database, and
    * also adds them to the table of active users, or updates timestamp if
    * already there.
    */
   function addActiveUser($uid, $time) {
      if(!get_magic_quotes_gpc()) {
         $uid = addslashes($uid);
         $time = addslashes($time);
      }

      $q = "UPDATE ".DB_TBL_CUSTOMERS." SET lastvisit = '$time' WHERE uid = '$uid'";
      $this->query($q);

      if(!TRACK_VISITORS) return;
      $q = "REPLACE INTO ".DB_TBL_ACTIVE_USERS." VALUES ('$uid', '$time')";
      $this->query($q);
      $this->calcNumActiveUsers();
   }

   function removeActiveUser($uid) {
      if(!get_magic_quotes_gpc()) {
         $uid = addslashes($uid);
      }

      if(!TRACK_VISITORS) return;
      $q = "DELETE FROM ".DB_TBL_ACTIVE_USERS." WHERE uid = '$uid'";
      $this->query($q);
      $this->calcNumActiveUsers();
   }

   function removeInactiveUsers() {
      if(!TRACK_VISITORS) return;
      $timeout = time()-USER_TIMEOUT*60;
      $q = "DELETE FROM ".DB_TBL_ACTIVE_USERS." WHERE timestamp < $timeout";
      $this->query($q);
      $this->calcNumActiveUsers();
   }

   /**
    * calcNumActiveUsers - Finds out how many active users are viewing site and
    * sets class variable accordingly.
    */
   function calcNumActiveUsers() {
      $q = "SELECT * FROM ".DB_TBL_ACTIVE_USERS;
      $result = $this->query($q);
      $this->_numActiveUsers = mysql_numrows($result);
   }

	function getNumActiveUsers() {
		return $this->_numActiveUsers;
	}

   function addActiveGuest($ip, $time) {
      if(!get_magic_quotes_gpc()) {
         $ip = addslashes($ip);
         $time = addslashes($time);
      }

      if(!TRACK_VISITORS) return;
      $q = "REPLACE INTO ".DB_TBL_ACTIVE_GUESTS." VALUES ('$ip', '$time')";
      $this->query($q);
      $this->calcNumActiveGuests();
   }

   function removeActiveGuest($ip) {
      if(!get_magic_quotes_gpc()) {
         $ip = addslashes($ip);
      }

      if(!TRACK_VISITORS) return;
      $q = "DELETE FROM ".DB_TBL_ACTIVE_GUESTS." WHERE ip = '$ip'";
      $this->query($q);
      $this->calcNumActiveGuests();
   }

   function removeInactiveGuests() {
      if(!TRACK_VISITORS) return;
      $timeout = time()-GUEST_TIMEOUT*60;
      $q = "DELETE FROM ".DB_TBL_ACTIVE_GUESTS." WHERE timestamp < $timeout";
      $this->query($q);
      $this->calcNumActiveGuests();
   }

   /**
    * calcNumActiveGuests - Finds out how many active guests are viewing site
    * and sets class variable accordingly.
    */
   function calcNumActiveGuests(){
      $q = "SELECT * FROM ".DB_TBL_ACTIVE_GUESTS;
      $result = $this->query($q);
      $this->_numActiveGuests = mysql_numrows($result);
   }

	function getNumActiveGuests() {
		return $this->_numActiveGuests;
	}

   /**
    * query - Performs the given query on the database and returns the result,
    * which may be false, true or a resource identifier.
    */
   function query($query) {
      if(!get_magic_quotes_gpc()) {
         $query = addslashes($query);
      }

      return mysql_query($query, $this->connection);
   }
};

/* Create database connection */
$database = new MySQLDB;
?>