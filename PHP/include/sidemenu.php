<?php
/**
 * sidemenu.php
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'sidemenu.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}
//Tie in Main menu with media type plugin system if possible/doable by demo time...
?>
      <a id="sectionmenu"></a>
      <h1>Main menu:</h1>
      <p class="menublock">
       <a class="nav" href="#">Browse By:</a><br class="hide" />
       <a class="nav sub" href="<?php echo SITE_BASE_URL; ?>/index.php?type=BOOK">Books</a><br class="hide" />
       <a class="nav sub" href="<?php echo SITE_BASE_URL; ?>/index.php?type=CD">CDs</a><br class="hide" />
       <a class="nav sub" href="<?php echo SITE_BASE_URL; ?>/index.php?type=DVD">DVDs</a><br class="hide" />
       <a class="nav sub" href="<?php echo SITE_BASE_URL; ?>/index.php?type=PERIODICAL">Periodicals</a><br class="hide" />
       <a class="nav" href="<?php echo SITE_BASE_URL; ?>/search.php">Search</a><br class="hide" />
       <a class="hide" href="#top">Back to top</a>
      </p>


<?php
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
   echo "<a class=\"hide\" href=\"#top\">Back to top</a>\n";
   echo "</p>\n";
}

if($session->isAdmin()) { //Admin
   //should anything go here in an administrator menu?
}
?>