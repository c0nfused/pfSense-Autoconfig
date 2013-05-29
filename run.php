<?php
/* Project Name:		pfSense Autoconfig
 * Author:				@c0nfus3d1
 * Website:				http://theyconfuse.me/
 *
 *************************************************************************
 * Copyright (c) 2013 Joshua Richard
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *************************************************************************/

require_once( "./gatewayAccess.class.php" );

set_time_limit(900);
ini_set("display_errors", 0);

/***
 * Assign values, or use the default.
 * Ex Usage: php ./run.php 10.0.0.1 admin pfsense
 */
$ip = ($argv[1] != '') ? $argv[1] : "192.168.1.1";
$user = ($argv[2] != '') ? $argv[2] : "admin";
$password = ($argv[3] != '') ? $argv[3] : "pfsense";

/***
 * Load the class
 */
$gateway = new gatewayAccess($ip, $user, $password);

/***
 * Log in to the pfSense Web Interface.
 * Expects a cookie named "PHPSESSID" to be returned.
 */
echo $gateway->login();

/***
 * Change Advanced System Settings
 * Set:
 * 	- Web Protocol to HTTP
 * 	- Max Processes to 50
 * 	- Disable HTTP_REFERER enforcement check
 * 	- Enable Secure Shell
 */
$data = array(
			"uri" => "system_advanced_admin.php",
			"post" => array(
						"webguiproto" => "http",
						"ssl-certref" => $gateway->getFormItemValue("system_advanced_admin.php", "ssl-certref"),
						"webguiport" => "",
						"max_procs" => "50",
						"althostnames" => "",
						"nohttpreferercheck" => "yes",
						"enablesshd" => "yes",
						"sshport" => "",
						"Submit" => "Save"
					)
		);

$gateway->changeSettings($data);

echo "Advanced Settings Changed\n";

/***
 * Wait 3 seconds for the settings to save and SSH to enable
 */
sleep(3);

/***
 * 
 Change System Settings
 * Set:
 * 	- Hostname to pfsense.localdomain
 * 	- DNS 1 Server to 8.8.8.8
 * 	- DNS 2 Server to 8.8.4.4
 * 	- Time zone to America/Chicago
 * 	- Theme to a company theme
 */
$data = array(
			"uri" => "system.php",
			"post" => array(
						"hostname" => "pfsense",
						"domain" => "localdomain",
						"dns1" => "8.8.8.8",
						"dns1gwint" => "none",
						"dns2" => "8.8.4.4",
						"dns2gwint" => "none",
						"dns3" => "",
						"dns3gwint" => "none",
						"dns4" => "",
						"dns4gwint" => "none",
						"dnsallowoverride" => "yes",
						"timezone" => "America/Chicago",
						"timeservers" => "0.pfsense.pool.ntp.org",
						"theme" => "companytheme",
						"Submit" => "Save"
					)
		);

$gateway->changeSettings($data);

echo "System Settings Changed\n";

/***
 * Open WAN Port 80
 */
$data = array(
			"uri" => "firewall_rules_edit.php",
			"post" => array(
						"ruleid" => "",
						"type" => "pass",
						"interface" => "wan",
						"proto" => "tcp",
						"srctype" => "any",
						"srcbeginport" => "",
						"srcbeginport_cust" => "",
						"srcendport" => "",
						"srcendport_cust" => "",
						"dsttype" => "any",
						"dstbeginport" => "",
						"dstbeginport_cust" => "80",
						"dstendport" => "",
						"dstendport_cust" => "",
						"descr" => "",
						"Submit" => "Save",
						"after" => "-1",
						"os" => "",
						"dscp" => "",
						"tag" => "",
						"tagged" => "",
						"max" => "",
						"max-src-nodes" => "",
						"max-src-conn" => "",
						"max-src-states" => "",
						"max-src-conn-rate" => "",
						"max-src-conn-rates" => "",
						"statetimeout" => "",
						"statetype" => "keep state",
						"sched" => "",
						"gateway" => "",
						"dnpipe" => "none",
						"pdnpipe" => "none",
						"ackqueue" => "none",
						"defaultqueue" => "none",
						"l7container" => "none"
					)
		);

$gateway->changeSettings($data);

echo "Opened WAN port 80\n";

/***
 * Apply Firewall Rules
 */
$data = array(
			"uri" => "firewall_rules.php",
			"post" => array(
						"apply" => "Apply changes",
						"if" => "wan"
					)
		);

$gateway->changeSettings($data);

echo "Applied Firewall Rules\n";

/***
 * Change Admin Password
 */
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

echo "Changed admin password\n";

echo "DONE\n";

die();
 
?>