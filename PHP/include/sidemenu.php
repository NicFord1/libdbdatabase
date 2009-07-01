<?php
/**
 * sidemenu.php
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'sidemenu.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}

/**
 * Display general main menu.
 */
//Tie in Main menu with media type plugin system if possible/doable by demo time...
echo "<a id=\"sectionmenu\"></a>\n";
echo "<h1>Main menu:</h1>\n";
echo "<p class=\"menublock\">\n";
echo "<a class=\"nav\" href=\"".SITE_BASE_URL."/index.php\">Browse</a><br class=\"hide\" />";
echo "<a class=\"nav sub\" href=\"".SITE_BASE_URL."/index.php?type=BOOK\">Books</a><br class=\"hide\" />\n";
echo "<a class=\"nav sub\" href=\"".SITE_BASE_URL."/index.php?type=CD\">CDs</a><br class=\"hide\" />\n";
echo "<a class=\"nav sub\" href=\"".SITE_BASE_URL."/index.php?type=DVD\">DVDs</a><br class=\"hide\" />\n";
echo "<a class=\"nav sub\" href=\"".SITE_BASE_URL."/index.php?type=PERIODICAL\">Periodicals</a><br class=\"hide\" />\n";
echo "<a class=\"nav\" href=\"".SITE_BASE_URL."/search.php\">Search</a><br class=\"hide\" />\n";

if(!$session->logged_in) {
	echo "<a class=\"nav\" href=\"".SITE_BASE_URL."/newuser.php\">Register</a><br class=\"hide\" />\n";
}

echo "<a class=\"hide\" href=\"#top\">Back to top</a>\n";
echo "</p>\n";

/**
 * Display relavent menus based on user privileges.
 */
if($session->isCustomer()) { //Customer, Teller OR Administrator
   //should anything go here in a customer menu (i.e., stuff not for guests)?
}

if($session->isTeller() || $session->isAdmin()) { //Teller or Admin
   echo "<h1>Teller menu:</h1>\n";
   echo "<p class=\"menublock\">\n";
   echo "<a class=\"nav\" href=\"".SITE_BASE_URL."/checkout.php\">Check Out</a><br class=\"hide\" />\n";
   echo "<a class=\"nav\" href=\"".SITE_BASE_URL."/checkin.php\">Check In</a><br class=\"hide\" />\n";
   if($session->isTeller()) {
      echo "<a class=\"nav\" href=\"".SITE_BASE_URL."/newuser.php\">Register New Member</a><br class=\"hide\" />\n";
   }
   echo "<a class=\"hide\" href=\"#top\">Back to top</a>\n";
   echo "</p>\n";
}

if($session->isAdmin()) { //Admin
   //should anything go here in an administrator menu?
   echo "<h1>Administrator menu:</h1>\n";
   echo "<a class=\"nav\" href=\"".SITE_BASE_URL."/admin/admin.php\">Admin Center</a><br class=\"hide\" />\n";
   echo "<a class=\"nav\" href=\"".SITE_BASE_URL."/newuser.php\">Register New Member</a><br class=\"hide\" />\n";
   echo "<a class=\"hide\" href=\"#top\">Back to top</a>\n";
   echo "</p>\n";
}
?>