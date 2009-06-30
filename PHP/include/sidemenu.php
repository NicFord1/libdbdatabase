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
       <a class="nav" href="#">Browse By:</a><br class="hide" />
       <a class="nav sub" href="#">Books</a><br class="hide" />
       <a class="nav sub" href="#">CDs</a><br class="hide" />
       <a class="nav sub" href="#">DVDs</a><br class="hide" />
       <a class="nav sub" href="#">Tie into media type plugin system...</a><br class="hide" />
       <a class="nav" href="<?php echo SITE_BASE_URL; ?>/search.php">Search</a><br class="hide" />
       <a class="hide" href="#top">Back to top</a>
      </p>