<?php
/**
 * footer.php
 */

if (strpos(strtolower($_SERVER['PHP_SELF']), 'footer.php') !== false) {
   header("Location: ".SITE_BASE_URL."/index.php"); //Gracefully leave page
}

?>
    <div id="footer">
     <p>&copy; 2009 <a href="<?php echo SITE_BASE_URL?>">LibDBDatabase</a>. Design by <a href="http://andreasviklund.com/">Andreas Viklund</a></p>
    </div>