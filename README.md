## pfSense Autoconfig

Automatically save preset settings in a new pfSense install.

----------

Included is a PHP Class and an example script on how to use it.

*This is intended for command line use only.*

----------

Install pfSense like normal. Plug your computer in to the LAN port, and then run the script. It will automatically log in to pfSense and save any settings you define.

----------

Example Usage:

`php ./run.php 192.168.1.1 admin pfsense` to run the included sample file.

OR

	<?php
	require_once( "./gatewayAccess.class.php" );
	$gateway = new gatewayAccess();
	echo $gateway->login("192.168.1.1", "admin", "pfsense");
	
	$data = array(
			"uri" => "system_usermanager.php",
			"post" => array(
						"utype" => "system",
						"usernamefld" => "admin",
						"oldusername" => "admin",
						"passwordfld1" => "NEWPASSWORD",
						"passwordfld2" => "NEWPASSWORD",
						"descr" => "System Administrator",
						"expires" => "",
						"groups" => array("admins"),
						"authorizedkeys" => "",
						"ipsecpsk" => "",
						"save" => "Save",
						"id" => "0"
					)
		);

	$gateway->changeSettings($data);

	echo "Admin Password Changed to NEWPASSWORD\n";

----------

`$data['uri']` is the file name the data is to be posted to.

`$data['post']` is all of the data to post in an array.

----------

**Created By**


[@c0nfus3d1](https://twitter.com/c0nfus3d1) - [TheyConfuse.Me](http://theyconfuse.me) 