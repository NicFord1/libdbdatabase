<?php

/*
 * LibDBDatabase database configuration -- db-config.php
 *
 * This file is intended to group all database variables to make it easier
 * for the site administrator to tweak the login script.
 *
 * Written by: Nicholas Ford a.k.a. NicFord1 [Nicholas+OSSCode@Nicks-Net.us]
 * Created: 2009.06.14
 * Last Updated: 2009.06.14
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'db-config.php') !== false) {
    die('This file can not be used on its own!');
}

/************************\
 ** DATABASE CONSTANTS **
\************************/
/**
 * These are required in order for there to be a successful connection
 * to the MySQL database. Make sure the information is correct.
 */
define("DB_SERVER", "h50mysql31.secureserver.net");
define("DB_USER", "LibDBDatabase");
define("DB_PASS", "S!TaQ{Y(K6}O@g");
define("DB_NAME", "LibDBDatabase");

/******************************\
 ** DATABASE TABLE CONSTANTS **
\******************************/
/*
 * These hold the names of all the database tables used in the script.
 */
define("DB_TBL_PRFX", "ldb_");
define("DB_TBL_USERS", DB_TBL_PRFX."users");
define("DB_TBL_ACTIVE_USERS",  DB_TBL_PRFX."activeusers");
define("DB_TBL_ACTIVE_GUESTS", DB_TBL_PRFX."activeguests");
define("DB_TBL_BANNED_USERS",  DB_TBL_PRFX."bannedusers");

?>