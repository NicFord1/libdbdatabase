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


define("SITE_BASE_URL", "http://LibDBDatabase.Nicks-Net.us");


/******************************\
 ** AUTHENTICATION CONSTANTS **
\******************************/
/**
 * Username and Level Variables - the admin page will only be accessible
 * to those users at the admin user level.  Feel free to change the guest
 * name and level Variables as you see fit, you may also add additional
 * level specifications.
 *
 * Levels must be digits between 0-9.
 */
define("GUEST_NAME", "Guest");
define("ADMIN_LEVEL",  9);
define("TELLER_LEVEL", 5);
define("CUST_LEVEL",   1);
define("GUEST_LEVEL",  0);


define("SALT_LENGTH", 17);


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
define("SALTLENGTH", 17);


/********************\
 ** MAIL CONSTANTS **
\********************/
/**
 * Email Variables - these specify what goes in the from field in the emails
 * that the script sends to users, and whether to send a welcome email to
 * newly registered users.
 */
define("EMAIL_FROM_NAME", "LibDBDatabase");
define("EMAIL_FROM_ADDR", "LibDBDatabase-noreply@Nicks-Net.us");
define("EMAIL_WELCOME", false);


/**
 * This constant forces all users to have lowercase usernames, capital
 * letters are converted automatically.
 */
define("ALL_LOWERCASE", false);

?>
