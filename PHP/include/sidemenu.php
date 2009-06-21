<?php
/**
 * sidemenu.php
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'sidemenu.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}

?>
      <a id="sectionmenu"></a>
      <h1>Example menu:</h1>
      <p class="menublock">
       <a class="nav" href="#">Forum</a><br class="hide" />
       <a class="nav sub" href="#">- Latest news</a><br class="hide" />
       <a class="nav sub" href="#">- Members</a><br class="hide" />
       <a class="nav sub" href="#">- Newsletter</a><br class="hide" />
       <a class="hide" href="#top">Back to top</a>
      </p>