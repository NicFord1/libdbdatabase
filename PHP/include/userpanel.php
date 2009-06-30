<?php
/**
 * userpanel.php
 * Used on all pages user is logged into.
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'userpanel.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}
require_once("session.php");
?>

  <!-- Sliding Panel -->
  <div id="toppanel">
   <div class="tab">
    <ul class="login">
     <li class="left">&nbsp;</li>
     <li>Welcome <?php echo $session->user->username; ?>!</li>
<?php
/**
 * User has already logged.  Provide logout link.
 */
if($session->logged_in) {
?>
     <li class="sep">|</li>
     <li><a href="<?php echo SITE_BASE_URL; ?>/process.php?logout">Logout</a></li>
<?php
}
?>
     <li class="right">&nbsp;</li>
    </ul>
   </div>
  </div>
  <!-- /Sliding Panel -->