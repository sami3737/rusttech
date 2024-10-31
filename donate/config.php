<?php

// Enter your API key from sdonate.com
$sdonateapi = '472V2197DQHL6OPBPIWZ';

// Enter your steam API key from https://steamcommunity.com/dev/apikey
$steamapi = 'FB009E17CE46FCF9DA5767536645F3EF';

// The host of your MySQL database. If it's hosted on the same server as your website just leave it as 'localhost'
$dbhost = 'localhost';

// The name of your MySQL database
$dbname = 'test';

// The username for your MySQL account
$dbusername = 'root';

// The password for your MySQL account
$dbpassword = '';

// The currency code of the main currency you wish to use.
$currencycode = 'AUD';




// -------------------------------------------------------------------------------------------------------------------------------------------------------- //
// THE SETTINGS BELOW ARE OPTIONAL AND ONLY REQUIRED IF YOU WANT TO ENABLE GOOGLE RECAPTCHA, LEAVE BOTH EMPTY IF YOU DO NOT WANT TO ENABLE GOOGLE RECAPTCHA
// -------------------------------------------------------------------------------------------------------------------------------------------------------- //

// Enter your Google reCAPTCHA site key from https://www.google.com/recaptcha/admin
$recaptchasitekey = '';

// Enter your Google reCAPTCHA secret key from https://www.google.com/recaptcha/admin
$recaptchasecretkey = '';

// Enter the directory your store is installed in, you only need to do this if the one SDonate automatically finds is incorrect. Leave this empty otherwise. Make sure you include the "http://" at the start.
$dir = "";



// ---------------------------- DO NOT EDIT ANYTHING BELOW HERE ---------------------------- //

require_once('currencycodes.php');
require_once('base_funcs.php');

?>
