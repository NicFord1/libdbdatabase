<?php
/**
 * topmenu.php
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'topmenu.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}

?>
   <div id="nav">
    <ul>
     <li <?=($_SERVER['PHP_SELF'] == '/index.php' || $_SERVER['PHP_SELF'] == '/' ? 'id="current"' : '')?>>
      <a href="<?=SITE_BASE_URL?>/index.php">Front page</a>
     </li>
<?php
/**
 * Display relavent links based on user privileges.
 */
if($session->isCustomer()) { //Customer
   echo "     <li ".($_SERVER['PHP_SELF'] == '/search.php' ? 'id="current"' : '').">"
       ."<a href=\"".SITE_BASE_URL."/search.php\">Search</a></li>";
   echo "     <li ".($_SERVER['PHP_SELF'] == '/userhistory.php' ? 'id="current"' : '').">"
       ."<a href=\"".SITE_BASE_URL."/userhistory.php\">View My History</a></li>";

   echo "     <li ".($_SERVER['PHP_SELF'] == '/userinfo.php' ? 'id="current"' : '').">"
       ."<a href=\"".SITE_BASE_URL."/userinfo.php\">View My Account</a></li>";
}

if($session->isTeller() || $session->isAdmin()) { //Teller or Admin
   echo "     <li ".($_SERVER['PHP_SELF'] == '/checkinout.php' ? 'id="current"' : '').">"
       ."<a href=\"".SITE_BASE_URL."/checkin.php\">Check In</a></li>";
   
   echo "     <li ".($_SERVER['PHP_SELF'] == '/newuser.php' ? 'id="current"' : '').">"
       ."<a href=\"".SITE_BASE_URL."/newuser.php\">Register New Member</a></li>";
}

if($session->isAdmin()) { //Admin
   echo "     <li ".($_SERVER['PHP_SELF'] == '/admin/admin.php' ? 'id="current"' : '').">"
       ."<a href=\"".SITE_BASE_URL."/admin/admin.php\">Admin Center</a></li>";
}
?>
    </ul>
    <p class="hide"><a href="#top">Back to top</a></p>
   </div>