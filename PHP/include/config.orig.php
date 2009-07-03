<?php

/*
 * LibDBDatabase configuration -- config.php
 *
 * This file is intended to group all system variables & constants to make it
 * easier for the site administrator to tweak the LibDBDatabase script.
 *
 * Written by: Nicholas Ford a.k.a. NicFord1 [Nicholas+OSSCode@Nicks-Net.us]
 * Created: 2009.06.14
 * Last Updated: 2009.06.14
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'config.php') !== false) {
    die('This file can not be used on its own!');
}


define("SITE_NAME", "LibDBDatabase");
define("SITE_BASE_URL", "http://LibDBDatabase.change.me");


/******************************\
 ** AUTHENTICATION CONSTANTS **
\******************************/
/**
 * Usertype Constant - the admin page will only be accessible to those users
 * of the type Administrator. Feel free to change as you see fit, you may also
 * add additional usertypes.
 */
define("GUEST",  "Guest");
define("CUST",   "Customer");
define("TELLER", "Teller");
define("ADMIN",  "Administrator");


/**
 * This boolean constant controls whether or not the script keeps track of
 * active users and active guests who are visiting the site.
 */
define("TRACK_VISITORS", true);


/**
 * Timeout Constants - these Variables refer to the maximum amount of time
 * (in minutes) after their last page fresh that a visitor is still considered
 * to be active.
 */
define("USER_TIMEOUT", 10);
define("GUEST_TIMEOUT", 5);


/**
 * Cookie Constants - these are the parameters to the setcookie function
 * call, change them if necessary to fit your website. If you need help,
 * visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default
define("COOKIE_PATH", "/");  //Avaible in whole domain


/**
 * Hashing Variables - these are the parameters to the hashing function.
 */
define("SALT_LENGTH", 17);


/********************\
 ** MAIL CONSTANTS **
\********************/
/**
 * Email Variables - these specify what goes in the from field in the emails
 * that the script sends to users, and whether to send a welcome email to
 * newly registered users.
 */
define("EMAIL_FROM_NAME", SITE_NAME);
define("EMAIL_FROM_ADDR", "LibDBDatabase-noreply@change.me");
define("EMAIL_WELCOME", true);


/**
 * This constant forces all users to have lowercase usernames, capital
 * letters are converted automatically.
 */
define("ALL_LOWERCASE", false);
?>