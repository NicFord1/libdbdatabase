<html><body>
<?
require_once ("recaptchalib.php");

// get a key at http://mailhide.recaptcha.net/apikey
$mailhide_pubkey = '01NIDavOWMJR9KgyTrSmbiRA==';
$mailhide_privkey = '318809DA38C723828596A18F04224B7F';

?>

The Mailhide version of example@example.com is
<? echo recaptcha_mailhide_html ($mailhide_pubkey, $mailhide_privkey, "example@example.com"); ?>. <br>

The url for the email is:
<? echo recaptcha_mailhide_url ($mailhide_pubkey, $mailhide_privkey, "example@example.com"); ?> <br>

</body></html>
