<?php
/**
 * userpanel.php
 * Used on all pages user is logged into.
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'userpanel.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}

?>

  <!-- Sliding Panel -->
  <div id="toppanel">
   <div class="tab">
<?php
/**
 * User has already logged.  Greet user and provide logout link.
 */
if($session->logged_in) {
?>
    <ul class="login">
     <li class="left">&nbsp;</li>
     <li>Welcome <?=$session->username?>!</li>
     <li class="sep">|</li>
     <li><a href="<?=SITE_BASE_URL?>/process.php?logout">Logout</a></li>
     <li class="right">&nbsp;</li>
    </ul>
<?php
} else {
/**
 * Greet the guest.
 */
?>
    <ul class="login">
     <li class="left">&nbsp;</li>
     <li>Hello <?=$session->username?>!</li>
     <li class="right">&nbsp;</li>
    </ul>
<?php
}
?>
   </div>
  </div>
  <!-- /Sliding Panel -->