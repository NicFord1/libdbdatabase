<?php

/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3: */

/**
 * The Users subpackage was developed to better model the user system of LibDBDatabase.
 *
 * PHP version 5
 *
 * LICENSE:
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 *
 * @package    LibDBDatabase
 * @subpackage Users
 * @author     Nicholas Ford <Nicholas+OSSCode@Nicks-Net.us>
 * @copyright  2009 Nicholas Ford
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    CVS: <?php
$
?> Id:$
 */

/*
* Place includes, constant defines and $_GLOBAL settings here.
* Make sure they have appropriate docblocks to avoid phpDocumentor
* construing they are documented by the page-level docblock.
*/
require_once("database.php");

/**
 * Describes a person to have certain attributes.
 *
 * @package    LibDBDatabase
 * @subpackage Users
 * @author     Nicholas Ford <Nicholas+OSSCode@Nicks-Net.us>
 * @copyright  2009 Nicholas Ford
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    Release: @package_version@
 */
abstract class Person {
   /**
    * The person's last name. Required.
    * @var string
    */
   public $lastName;
   /**
    * The person's first name. Required.
    * @var string
    */
   public $firstName;
   /**
    * The person's birthdate as a unix timestamp. Required.
    * @var int
    */
   public $birthDate;
   /**
    * The person's gender. Possible values are 'M' and 'F'.
    * @var char
    */
   public $gender;
   /**
    * The person's address
    * @var array
    */
   public $address;
   /**
    * The person's phone number
    * @var string
    */
   public $phone;

   /**
    * Constructor for the Person Class.
    *
    * @param array $info the information to set this Person.
    */
   function __construct($info) {
      date_default_timezone_set('America/New_York');
      $this->lastName = trim($info['lastname']);
      $this->firstName = trim($info['firstname']);
      $this->birthDate = trim($info['birthdate']);
      $this->gender = trim($info['gender']);
      $this->setAddress(trim($info['addrline1']), trim($info['addrline2']), trim($info['city']), trim($info['state']), trim($info['zip']));
      $this->phone = trim($info['phone']);
   }

   /**
    * @param string $line1
    * @param string $line2
    * @param string $city
    * @param string $state
    * @param string $zip
    *
    * @return void
    *
    * @access public
    */
   public function setAddress($line1, $line2, $city, $state, $zip) {
      $this->address = array("line1"=>$line1, "line2"=>$line2, "city"=>$city, "state"=>$state, "zip"=>$zip);
   }

   /**
    * @param string $day unix timestamp of the day to determine the person's age on
    *
    * @return int the age of the person for the given day.
    *             False if provide a time before the person was born.
    *
    * @access public
    */
   public function getAge($day=NULL) {
      if(!$day) {
      	date_default_timezone_set('America/New_York');
         $day = time();
      }

      if($this->birthDate < 0) {
      	$age = $day + ($this->birthDate * -1);
      } else {
      	$age = $day - $this->birthDate;
      }

      $year = 60 * 60 * 24 * 365;
      $age = floor($age / $year);

      if($age > 0) {
      	return $age;
      } else {
      	return false;
      }
   }
}

/**
 * Describes a user of the system to be a person with additional attributes.
 *
 * @package    LibDBDatabase
 * @subpackage Users
 * @author     Nicholas Ford <Nicholas+OSSCode@Nicks-Net.us>
 * @copyright  2009 Nicholas Ford
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    Release: @package_version@
 */
abstract class User extends Person {
   /**
    * The connection to the database. Required.
    * @var object
    */
   public $database;

   /**
    * The users unique id number. Auto-Generated.
    * @var int
    * @access private
    */
   private $_uid;
   /**
    * The users time of last visit. Auto-Generated.
    * @var int
    * @access private
    */
   private $_lastVisit;
   /**
    * The users time of registration. Auto-Generated.
    * @var int
    * @access private
    */
   private $_regTime;

   /**
    * The users username for logging into the system. Required.
    * @var string
    */
   public $username;
   /**
    * The users salted hash password for logging into the system. Required.
    * @var string
    */
   public $password;
   /**
    * The users email address. Required.
    * @var string
    */
   public $email;

   /**
    * Constructor for the User Class.
    *
    * @param array $info the information to set this User.
    */
   function __construct($info) {
      $this->database = new MYSQLDB;

      if($this->database->confirmUserUID($info['username'], $info['uid']) == 0) {
         $this->_uid = trim($info['uid']);
         $dbInfo = $this->database->getUserInfo($this->getUID(), DB_TBL_CUSTOMERS);
         parent::__construct($dbInfo);
         $this->_lastVisit = $dbInfo['lastvisit'];
         $this->_regTime = $dbInfo['regtime'];
         $this->username = stripslashes($dbInfo['username']);
         $this->password = $dbInfo['password'];
         $this->email = $dbInfo['email'];
      } else {
         parent::__construct($info);
         $this->_uid = NULL;
         $this->_lastVisit = NULL;
         $this->_regTime = NULL;
         $this->username = stripslashes($info['username']);
         $this->password = trim($info['password']);
         $this->email = trim($info['email']);
      }
   }

   public function getUID() {
      return $this->_uid;
   }

   public function getLastVisit() {
      return $this->_lastVisit;
   }

   public function getRegistrationTime() {
      return $this->_regTime;
   }

   /**
    * Method to update the user's information in the database.
    */
   public function update($info) {
      if($this->database->confirmUID($this->getUID(), DB_TBL_CUSTOMERS)) { //user exists in the database already so update user
         if($this->database->confirmUserUID($this->username, $this->getUID(), DB_TBL_CUSTOMERS) == 0) { //username matches uid
            if(isset($info['newuname'])) {
               $this->username = trim($info['newuname']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "username", $this->username);
            }
            if(isset($info['newpass'])) {
               $this->password = trim($info['newpass']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "password", $this->password);
            }
            if(isset($info['edemail'])) {
               $this->email = trim($info['edemail']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "email", $this->email);
            }
            if(isset($info['edfname'])) {
               $this->firstName = trim($info['edfname']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "firstname", $this->firstName);
            }
            if(isset($info['edlname'])) {
               $this->lastName = trim($info['edlname']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "lastname", $this->lastName);
            }
            if(isset($info['edbirthdate'])) {
               $this->birthDate = trim($info['edbirthdate']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "birthdate", $this->birthDate);
            }
            if(isset($info['edgender'])) {
               $this->gender = trim($info['edgender']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "gender", $this->gender);
            }
            if(isset($info['edaddrline1'])) {
               $this->address['line1'] = trim($info['edaddrline1']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "addrline1", $this->address['line1']);
            }
            if(isset($info['edaddrline2'])) {
               $this->address['line2'] = trim($info['edaddrline2']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "addrline2", $this->address['line2']);
            }
            if(isset($info['edcity'])) {
               $this->address['city'] = trim($info['edcity']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "city", $this->address['city']);
            }
            if(isset($info['edstate'])) {
               $this->address['state'] = trim($info['edstate']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "state", $this->address['state']);
            }
            if(isset($info['edzip'])) {
               $this->address['zip'] = trim($info['edzip']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "zip", $this->address['zip']);
            }
            if(isset($info['edphone'])) {
               $this->phone = trim($info['edphone']);
            	$this->database->updateUserField($this->getUID(), DB_TBL_CUSTOMERS, "phone", $this->phone);
            }
         } else { //username and uid do not match
            return 2;
         }
      } else { //user does not exist in system
         return 1;
      }
      return 0; //user information successfully updated
   }

   /**
    * Method to register the user's information to the database.
    */
   protected function register() {
      date_default_timezone_set('America/New_York');
      $this->_regTime = time();

      $q = "INSERT INTO ".DB_TBL_CUSTOMERS." (username, password, email, regtime, "
          ."firstname, lastname, birthdate, gender, addrline1, addrline2, "
          ."city, state, zip, phone) VALUES ('".$this->username."', '".$this->password."', "
          ."'".$this->email."', '".$this->_regTime."', '".$this->firstName."', "
          ."'".$this->lastName."', '".$this->birthDate."', '".$this->gender."', "
          ."'".$this->address['line1']."', '".$this->address['line2']."', '".$this->address['city']."', "
          ."'".$this->address['state']."', '".$this->address['zip']."', '".$this->phone."')";

      $result = $this->database->query($q);

      if($result) {
         $this->_uid = $this->database->getUID($this->username);
      }

      return $result;
   }

   /**
    * Overridden method to update the user's role in the system
    *
    * @param int usertype used to determine what kind of user
    */
   abstract protected function updateUserType($usertype);
}

/**
 * Describes an employee to be a user of the system with additional attributes.
 *
 * @package    LibDBDatabase
 * @subpackage Users
 * @author     Nicholas Ford <Nicholas+OSSCode@Nicks-Net.us>
 * @copyright  2009 Nicholas Ford
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    Release: @package_version@
 */
abstract class Employee extends User {
   /**
    * The employee's date of hire as a unix timestamp. Auto-Generated.
    * @var int
    */
   public $dateHired;

   /**
    * Constructor for the Employee Class.
    *
    * @param array $userInfo the information to set this User.
    * @param array $empInfo the information to set this Employee.
    */
   function __construct($userInfo, $empInfo) {
      parent::__construct($userInfo);

      if($empInfo != NULL) {
         $this->dateHired = $empInfo['hiredate'];
      } else {
      	$this->dateHired = NULL;
      }
   }

   /**
    * Method to update the Employee's information in the database.
    */
   public function update($info) {
      return parent::update($info);
   }
}

/********************************\
 * Types of Users of the System *
\********************************/
/**
 * Describes a guest to be a user of the system with additional attributes.
 *
 * @package    LibDBDatabase
 * @subpackage Users
 * @author     Nicholas Ford <Nicholas+OSSCode@Nicks-Net.us>
 * @copyright  2009 Nicholas Ford
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    Release: @package_version@
 */
final class Guest extends User {
	private $_ipAddress;

   /**
    * Constructor for the Guest Class.
    */
   function __construct($ipAddress, $time) {
      $this->database = new MYSQLDB;

      $this->username = GUEST;
      $this->_ipAddress = $ipAddress;
      $this->_lastVisit = $time;
   }

   /**
    * Overridden method to update out the user's information to the database.
    */
   public function update() {
      return $this->database->addActiveGuest($this->_ipAddress, $this->_lastVisit);
   }

   /**
    * Overridden method to register the user's information to the database.
    */
   public function register() {
   	return $this->update();
   }

   /**
    * Overridden method to update the user's role in the system
    *
    * @param int usertype used to determine what kind of user
    */
   public function updateUserType($usertype) {//Guest's are guests...
   	return GUEST; //Must first be a customer....that's life!
   }
}

/**
 * Describes a customer to be a user of the system with additional attributes.
 *
 * @package    LibDBDatabase
 * @subpackage Users
 * @author     Nicholas Ford <Nicholas+OSSCode@Nicks-Net.us>
 * @copyright  2009 Nicholas Ford
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    Release: @package_version@
 */
final class Customer extends User { //Customers(balance, history)
   /**
    * Constructor for the Customer Class.
    *
    * @param array $info the information to set this User.
    */
   function __construct($info) {
      parent::__construct($info);
   }

   /**
    * Method to update the customer's information in the database.
    */
   public function update($info) {
   	$result = parent::update($info);
      if($result == 0) { //user successfully updated, now update customer specifics
         //nothing else to update
      }
      return $result;
   }

   public function register() {
      return parent::register();
   }

   /**
    * Overridden method to update the user's role in the system
    *
    * @param int usertype used to determine what kind of user
    */
   public function updateUserType($usertype) {
   	if($usertype == CUST) {
   		return CUST; //already a Customer
   	} else if($usertype == TELLER) {
   		if($this->database->hireTeller($this->getUID())) {
   			return TELLER;
   		}
   	} else if($usertype == ADMIN) {
   		if($this->database->hireAdministrator($this->getUID())) {
   			return ADMIN;
   		}
   	} else {
         return false; //invalid usertype
   	}
   }
}

/**
 * Describes a teller to be an employee.
 *
 * @package    LibDBDatabase
 * @subpackage Users
 * @author     Nicholas Ford <Nicholas+OSSCode@Nicks-Net.us>
 * @copyright  2009 Nicholas Ford
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    Release: @package_version@
 */
final class Teller extends Employee {
   /**
    * Constructor for the Teller Class.
    *
    * @param array $userInfo the information to set this User.
    * @param array $empInfo the information to set this Employee.
    */
   function __construct($userInfo, $empInfo=NULL) {
      parent::__construct($userInfo, $empInfo);

      if(isset($userInfo['uid']) && ($this->database->confirmUserUID($userInfo['username'], $userInfo['uid']) == 0)) {
         $dbEmpInfo = $this->database->getUserInfo($userInfo['uid'], DB_TBL_TELLERS);
      }

      if($dbEmpInfo) {
         parent::__construct($userInfo, $dbEmpInfo);
      }
   }

   /**
    * Method to update the Teller's information in the database.
    */
   public function update($info) {
   	$result = parent::update($info);
      if($result == 0) { //employee successfully updated, now update Teller specifics
         if($this->database->confirmUID($this->getUID(), DB_TBL_TELLERS)) { //user is a teller
            if(isset($info['hiredate'])) {
               $this->dateHired = trim($info['hiredate']);
               $this->database->updateUserField($this->getUID(), DB_TBL_TELLERS, "hiredate", $this->dateHired);
            }
         } else { //not a known Teller in our system
         	$result = 3;
         }
      } else if($result == 1) { //user doesn't exist, make sure not in Teller table
      	$this->database->removeUser($this->getUID(), DB_TBL_TELLERS);
      }
      return $result;
   }

   public function register() {
      $result = parent::register();
      if($result) { //perform Teller Specific actions
         $result = $this->database->hireTeller($this->getUID(), $this->dateHired);
      }
      return $result;
   }

   /**
    * Overridden method to update the user's role in the system
    *
    * @param int usertype used to determine what kind of user
    */
   public function updateUserType($usertype) {
   	if($usertype == TELLER) {
   		return TELLER; //already a Teller
   	} else if($usertype == ADMIN) {
   		if($this->database->hireAdministrator($this->getUID())) {
   			$this->database->removeUser($this->getUID(), DB_TBL_TELLERS);
   			return ADMIN;
   		} else {
   			return false; //didn't successfully hire as Administrator
   		}
   	} else if($usertype == CUST){
   		$this->database->removeUser($this->getUID(), DB_TBL_TELLERS);
   	   return CUST;
   	} else {
         return false; //invalid usertype
   	}
   }
}

/**
 * Describes an administrator to be an employee.
 *
 * @package    LibDBDatabase
 * @subpackage Users
 * @author     Nicholas Ford <Nicholas+OSSCode@Nicks-Net.us>
 * @copyright  2009 Nicholas Ford
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    Release: @package_version@
 */
final class Administrator extends Employee {
   /**
    * Constructor for the Administrator Class.
    *
    * @param array $userInfo the information to set this User.
    * @param array $empInfo the information to set this Employee.
    */
   function __construct($userInfo, $empInfo=NULL) {
      parent::__construct($userInfo, $empInfo);

      if($this->database->confirmUserUID($userInfo['username'], $userInfo['uid']) == 0) {
         $dbEmpInfo = $this->database->getUserInfo($userInfo['uid'], DB_TBL_ADMINS);
      }

      if($dbEmpInfo) {
         parent::__construct($userInfo, $dbEmpInfo);
      }
   }

   /**
    * Method to update the Administrator's information in the database.
    */
   public function update($info) {
   	$result = parent::update($info);
      if($result == 0) { //employee successfully updated, now update Administrator specifics
         if($this->database->confirmUID($this->getUID(), DB_TBL_ADMINS)) { //user is a Administrator
            if(isset($info['hiredate'])) {
               $this->dateHired = trim($info['hiredate']);
               $this->database->updateUserField($this->getUID(), DB_TBL_ADMINS, "hiredate", $this->dateHired);
            }
         } else { //not a known Administrator in our system
         	$result = 3;
         }
      } else if($result == 1) { //user doesn't exist, make sure not in Administrator table
      	$this->database->removeUser($this->getUID(), DB_TBL_ADMINS);
      }
      return $result;
   }

   public function register() {
      $result = parent::register();
      if($result) {
         //perform Administrator Specific actions
         $result = $this->database->hireAdministrator($this->getUID(), $this->dateHired);
      }
      return $result;
   }

   /**
    * Overridden method to update the user's role in the system
    *
    * @param int usertype used to determine what kind of user
    */
   public function updateUserType($usertype) {
   	if($usertype == ADMIN) {
   		return ADMIN; //already an administrator
   	} else if($this->database->getNumAdmins() > 1) {
	   	if($usertype == TELLER) {
	   		if($this->database->hireTeller($this->getUID())) {
	   			$this->database->removeUser($this->getUID(), DB_TBL_ADMINS);
	   			return TELLER;
	   		} else {
	   			return false; //didn't successfully hire as teller
	   		}
	   	} else if($usertype == CUST){
	   		$this->database->removeUser($this->getUID(), DB_TBL_ADMINS);
	   	   return CUST;
	   	} else {
	         return false; //invalid usertype
	   	}
   	} else {
   		return false; //only adminstrator in system
   	}
   }
}
?>