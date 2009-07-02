<?php
/**
 * newitem.php
 *
 * This is the New Item page. Only administrators are allowed to view this page.
 * This page allows for the creation of new media items.  Only admins can create new items.
 * This file is a heavily modified version of newuser.php
 */
include("../include/session.php");


/**
 * User logged in and not an administrator, redirect to main page automatically.
 */
if($session->logged_in && !($session->isAdmin())) {
   header("Location: ".SITE_BASE_URL."/index.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
 <head>
  <title>LibDBDatabase</title>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
  <meta name="description" content="LibDBDatabase: Library Database" />
  <meta name="keywords" content="library,database" />
  <meta name="author" content="LibDBDatabase / Original design: Andreas Viklund - http://andreasviklund.com/" />
  <link rel="stylesheet" href="<?php echo SITE_BASE_URL?>/css/andreas06.css" type="text/css" media="screen,projection" />
  <link rel="stylesheet" href="<?php echo SITE_BASE_URL?>/css/slide.css"  type="text/css" media="screen,projection" />
  <link rel="stylesheet" href="<?php echo SITE_BASE_URL?>/css/validate.css"  type="text/css" media="screen,projection" />

  <!-- javascripts -->
  <!-- PNG FIX for IE6 -->
  <!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
  <!--[if lte IE 6]>
   <script type="text/javascript" src="<?php echo SITE_BASE_URL?>/js/pngfix/supersleight-min.js"></script>
  <![endif]-->

  <!-- jQuery -->
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.form.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.validate.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.metadata.min.js" type="text/javascript"></script>
  <script src="<?php echo SITE_BASE_URL?>/js/jquery.maskedinput.min.js" type="text/javascript"></script>

  <script type="text/javascript">
     $.metadata.setType("attr", "validate");

     $(document).ready(function() {
        $("#regzip").mask("99999?-9999");
        $("#regphone").mask("(999) 999-9999");
        // validate registration form on keyup and submit
        $("#registration").validate({
           rules: {
              regisbnreq: {
                 required: true,
              },
			  regissn: {
                 required: true,
              },
			  regupc: {
                 required: true,
              },

              regtitle: {
                 required: true,
              },
              regreleasemonth: {
                 required: true
              },
              regreleaseday: {
                 required: true
              },
              regreleaseyear: {
                 required: true
              }
           },
           messages: {
              regisbnreq: "Please provide the ISBN",
			  regissn: "Please provide the ISSN",
			  regupc: "Please provide the UPC",
			  regtitle: "Please provide the title",
              regreleasemonth: "Please select the month this item was released",
              regreleaseday: "Please select the day this item was released",
              regreleaseyear: "Please select the year this item was released"
           }
        });
     });
</script>


  <!-- Sliding effect -->
  <script src="<?php echo SITE_BASE_URL?>/js/slide.js" type="text/javascript"></script>

 </head>

 <body>
  <?php include_once("../include/userpanel.php"); ?>

  <div id="container">

   <a id="top"></a>
   <p class="hide">
    Skip to:
    <a href="#menu">site menu</a>
    | <a href="#sectionmenu">section menu</a>
    | <a href="#main">main content</a>
   </p>

   <div id="sitename">
    <h1>LibDBDatabase</h1>
    <span>Library Database System</span>
    <a id="menu"></a>
   </div>

   <?php include_once("../include/topmenu.php"); ?>

   <div id="wrap1">
    <div id="wrap2">

     <div id="topbox">
      <strong>
       <span class="hide">Currently viewing: </span>
       <a href="<?php echo SITE_BASE_URL?>/index.php">LibDBDatabase</a> &raquo;
       <a href="<?php echo $_SERVER['PHP_SELF']?>">New Item Center</a>
      </strong>
     </div>

     <div id="leftside">
      <?php include_once("../include/sidemenu.php"); ?>
     </div>

     <a id="main"></a>
     <div id="contentalt">
      <h1>New Item Center</h1>
      <img src="<?php echo SITE_BASE_URL?>/img/gravatar-newuser.png" height="80" width="80" alt="New User Gravatar" />

      <font size="5" color="#ff0000">
       <b>::::::::::::::::::::::::::::::::::::::::::::</b>
      </font>
      <br /><br />
<?php
if($session->form->num_errors > 0) {
   echo "      <font size=\"2\" color=\"#ff0000\">".$session->form->num_errors." error(s) found</font>\n";
}
?>

<?php

    echo "<h1>Add a New Item to Our Library</h1>";

?>
	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
Item Type: 
		<select name="itemtype" onchange="location = 'newitem.php?itemtype=' + this.value;">
			<?php
				$itemtype;
				if((isset($_GET['itemtype']) && $_GET['itemtype'] == "CD") || (isset($_POST['itemtype']) && $_POST['itemtype'] == "CD")){
					$itemtype = 'CD';
				} else if((isset($_GET['itemtype']) && $_GET['itemtype'] == "DVD") || (isset($_POST['itemtype']) && $_POST['itemtype'] == "DVD")){
					$itemtype = 'DVD';
				} else if((isset($_GET['itemtype']) && $_GET['itemtype'] == "Periodical") || (isset($_POST['itemtype']) && $_POST['itemtype'] == "Periodical")){
					$itemtype = 'Periodical';
				} else{
					$itemtype = 'Book';
				}
				/*if((isset($_GET['itemtype']) && $_GET['itemtype'] == "Book") || (isset($_POST['itemtype']) && $_POST['itemtype'] == "Book") || 
					!isset($_GET['itemtype']) || !isset($_POST['itemtype']))*/
			?>
		  <option value="Book" <?php if($itemtype == 'Book'){ echo "selected"; } ?>>Book</option>
		  <option value="CD" <?php if($itemtype == 'CD'){ echo "selected"; } ?>>CD</option>
		  <option value="DVD" <?php if($itemtype == 'DVD'){ echo "selected"; } ?>>DVD</option>
		  <option value="Periodical" <?php if($itemtype == 'Periodical'){ echo "selected"; } ?>>Periodical</option>
		  </select>

       <table align="left" border="0" cellspacing="0" cellpadding="3">
	   
		<tr>
			<td><label for="regquantity" class="grey required">Quantity:</label></td>
			<td><input class="field" type="text" name="regquantity" maxlength="5"/></td>
			<td>&nbsp;</td>
        </tr>
		
		<?php
			//Print the id type field(s)
			$toprint;
			if($itemtype == "Book"){
				$toprint =
				"<tr>
					<td><label for=\"regisbnreq\" class=\"grey required\">ISBN:</label></td>
					<td><input class=\"field\" type=\"text\" name=\"regisbnreq\" maxlength=\"13\"/></td>
					<td>&nbsp;</td>
				</tr>";
			} else if($itemtype == "CD" || $itemtype == "DVD"){
				$toprint =
				"<tr>
					<td><label for=\"regupc\" class=\"grey required\">UPC:</label></td>
					<td><input class=\"field\" type=\"text\" name=\"regupc\" maxlength=\"13\"/></td>
					<td>&nbsp;</td>
				</tr>";
			} else if($itemtype == "Periodical"){
				$toprint =
				"<tr>
					<td><label for=\"regisbn\" class=\"grey\">ISBN:</label></td>
					<td><input class=\"field\" type=\"text\" name=\"regisbn\" maxlength=\"13\"/></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><label for=\"regissn\" class=\"grey required\">ISSN:</label></td>
					<td><input class=\"field\" type=\"text\" name=\"regissn\" maxlength=\"8\"/></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><label for=\"regsici\" class=\"grey\">SICI:</label></td>
					<td><input class=\"field\" type=\"text\" name=\"regsici\" maxlength=\"100\"/></td>
					<td>&nbsp;</td>
				</tr>";
			}
			echo $toprint;			
		?>
	
        <tr>
			<td><label for="regtitle" class="grey required">Title:</label></td>
			<td><input class="field" type="text" name="regtitle" maxlength="160"/></td>
			<td>&nbsp;</td>
        </tr>

        <tr>
			<td><label for="regauthor" class="grey">Author:</label></td>
			<td><input class="field" type="text" name="regauthor" maxlength="80"/></td>
			<td>&nbsp;</td>
        </tr>
		
		<tr>
			<td><label for="reggenre" class="grey">Genre:</label></td>
			<td><input class="field" type="text" name="reggenre" maxlength="30"/></td>
			<td>&nbsp;</td>
        </tr>
		
		<tr>
			<td><label for="regpublisher" class="grey">Publisher:</label></td>
			<td><input class="field" type="text" name="regpublisher" maxlength="80"/></td>
			<td>&nbsp;</td>
        </tr>

        <tr>
         <td>
          <label class="grey required">Release date:</label>
         </td>
         <td>
          <table>
           <tr>
            <td>
             <select name="regreleasemonth">
              <option value="">Month:</option>
              <option value="1"<?php if($session->form->value("regreleasemonth") == 1) echo " selected"; ?>>January</option>
              <option value="2"<?php if($session->form->value("regreleasemonth") == 2) echo " selected"; ?>>February</option>
              <option value="3"<?php if($session->form->value("regreleasemonth") == 3) echo " selected"; ?>>March</option>
              <option value="4"<?php if($session->form->value("regreleasemonth") == 4) echo " selected"; ?>>April</option>
              <option value="5"<?php if($session->form->value("regreleasemonth") == 5) echo " selected"; ?>>May</option>
              <option value="6"<?php if($session->form->value("regreleasemonth") == 6) echo " selected"; ?>>June</option>
              <option value="7"<?php if($session->form->value("regreleasemonth") == 7) echo " selected"; ?>>July</option>
              <option value="8"<?php if($session->form->value("regreleasemonth") == 8) echo " selected"; ?>>August</option>
              <option value="9"<?php if($session->form->value("regreleasemonth") == 9) echo " selected"; ?>>September</option>
              <option value="10"<?php if($session->form->value("regreleasemonth") == 10) echo " selected"; ?>>October</option>
              <option value="11"<?php if($session->form->value("regreleasemonth") == 11) echo " selected"; ?>>November</option>
              <option value="12"<?php if($session->form->value("regreleasemonth") == 12) echo " selected"; ?>>December</option>
             </select>

             <select name="regreleaseday">
              <option value="">Day:</option>
<?php
      for ($i = 1; $i <= 31; $i++) {
         echo "              <option value='$i'";
         if($session->form->value("regreleaseday") == $i) {
            echo " selected";
         }
         echo ">$i</option>";
      }
?>
             </select>

             <select name="regreleaseyear">
              <option value="">Year:</option>
<?php
      for ($i = date("Y"); $i >= date("Y")-120; $i--) {
         echo "              <option value='$i'";
         if($session->form->value("regreleaseyear") == $i) {
            echo " selected";
         }
         echo ">$i</option>";
      }
?>
             </select>
            </td>
           </tr>
          </table>
         </td>
         <td>&nbsp;</td>
        </tr>
		
		<tr>
			<td><label for="regdescription" class="grey">Description:</label></td>
			<td><textarea rows=7 cols=24 class="field" name="regdescription"/></textarea></td>
			<td>&nbsp;</td>
        </tr>

        <tr>
         <td colspan="2" align="right">
          <input type="submit" value="Add Item">
         </td>
        </tr>

       </table>
      </form>
<?php
	function getFromPost($idstring){
		if(isset($_POST["$idstring"])){
			return $_POST["$idstring"];
		} else{
			return "";
		}
	}
	//get fields
	$regquantity = getFromPost('regquantity');
	$regisbn = getFromPost('regisbn');
	$regisbnreq = getFromPost('regisbnreq');
	$regissn = getFromPost('regissn');
	$regupc = getFromPost('regupc');
	$regsici = getFromPost('regsici');
	$regtitle = getFromPost('regtitle');
	$regauthor = getFromPost('regauthor');
	$reggenre = getFromPost('reggenre');
	$regpublisher = getFromPost('regpublisher');
	$regdescription = getFromPost('regdescription');
	
	date_default_timezone_set('America/New_York');
	$regreleasemonth = getFromPost('regreleasemonth');
	$regreleaseday = getFromPost('regreleaseday');
	$regreleaseyear = getFromPost('regreleaseyear');
	$regtimestamp = strtotime("$regreleaseyear-$regreleasemonth-$regreleaseday  01:00:00");
	
	echo "<pre>$itemtype \n $$regquantity \n $regisbn \n $regisbnreq \n $regissn "
	."\n $regupc \n $regsici \n $regtitle \n $regauthor "
	."\n $reggenre \n $regpublisher \n $regdescription \n "
	."$regreleasemonth $regreleaseday $regreleaseyear "
	."\n $regtimestamp</pre>";

	//create the itemid in ldb_items
	$qmakeitemid = "INSERT INTO ".DB_TBL_PRFX."items (itemtype, quantity) VALUES('$itemtype', $regquantity)";
	mysql_query($qmakeitemid);
	
	$qmaxitemid = "SELECT MAX(itemid) as max FROM ".DB_TBL_PRFX."items";
	$rowmaxitemid = mysql_fetch_array($database->query($qmaxitemid));
	$maxitemid = $rowmaxitemid["max"];
	if($itemtype == 'Book'){
		$q = "INSERT INTO ".DB_TBL_PRFX."books (itemid, isbn, title, author, genre, publisher, releasedate, description) "
					."VALUES($maxitemid, '$regisbnreq','$regtitle', '$regauthor', '$reggenre', '$regpublisher', $regtimestamp,'$regdescription')";	
		mysql_query($q);
	}else if($itemtype == 'CD'){
		$q = "INSERT INTO ".DB_TBL_PRFX."cds (itemid, upc, title, author, genre, publisher, releasedate, description) "
					."VALUES($maxitemid, '$regupc','$regtitle', '$regauthor', '$reggenre', '$regpublisher', $regtimestamp,'$regdescription')";	
		mysql_query($q);
	}else if($itemtype == 'DVD'){
		$q = "INSERT INTO ".DB_TBL_PRFX."dvds (itemid, upc, title, author, genre, publisher, releasedate, description) "
					."VALUES($maxitemid, '$regupc','$regtitle', '$regauthor', '$reggenre', '$regpublisher', $regtimestamp,'$regdescription')";	
		mysql_query($q);
	}else if($itemtype == 'Periodical'){
		$q = "INSERT INTO ".DB_TBL_PRFX."periodicals (itemid, isbn, issn, sici, title, editor, genre, publisher, releasedate, description) "
					."VALUES($maxitemid, '$regisbn', '$regissn', '$regsici', '$regtitle', '$regauthor', '$reggenre', '$regpublisher', $regtimestamp,'$regdescription')";	
		mysql_query($q);
	}
	
?>
      <p class="hide"><a href="#top">Back to top</a></p>
     </div>
    </div>

    <?php include_once("../include/footer.php"); ?>

   </div>
  </div>
 </body>
</html>