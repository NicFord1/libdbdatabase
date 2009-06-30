<?php
/**
 * Mailer.php
 *
 * The Mailer class is meant to simplify the task of sending emails to users.
 * Note: this email system won't work if your server is not setup to send mail.
 */

require_once("config.php");

class Mailer {
   /**
    * sendWelcome - Sends a welcome message to the newly registered user,
    * also supplying the username and password.
    */
   function sendWelcome($user, $email, $pass, $selfreg=true) {
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = SITE_NAME." - Welcome!";
      $body = $user.",\n\n"
             ."Welcome! You've just been registered at ".SITE_NAME
             ." with the following information:\n\n"
             ."Username: ".$user."\n"
             ."Password: ".$pass."\n\n";
      if(!$selfreg) {
      	$body .= "It is highly recommended that you change your password "
                 ."upon your first logon by going to the My Account page.\n\n";
      }
      $body .= "If you ever lose or forget your password, a new password will "
              ."be generated for you and sent to this email address, if you "
              ."would like to change your email address you can do so by going "
              ."to the My Account page after signing in.\n\n"
              ."- ".SITE_NAME;

      return mail($email,$subject,$body,$from);
   }

   /**
    * sendNewPass - Sends the newly generated password to the user's
    * email address that was specified at sign-up.
    */
   function sendNewPass($user, $email, $pass) {
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = SITE_NAME." - Your new password";
      $body = $user.",\n\n"
             ."We've generated a new password for you at your request, you can "
             ."use this new password with your username to log in to "
             ."<a href=\"".SITE_BASE_URL."\">".SITE_NAME."</a>.\n\n"
             ."Username: ".$user."\n"
             ."New Password: ".$pass."\n\n"
             ."It is recommended that you change your password to something "
             ."easier to remember, which can be done by going to the My "
             ."Account page after signing in.\n\n"
             ."- ".SITE_NAME;

      return mail($email,$subject,$body,$from);
   }
};

/* Initialize mailer object */
$mailer = new Mailer;
?>