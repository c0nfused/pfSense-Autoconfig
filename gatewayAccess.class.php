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

class gatewayAccess {
	function __construct($ip = "10.0.0.1", $user = "admin", $password = "pfsense") {
		$this->ip = $ip;
		$this->user = $user;
		$this->password = $password;
	}
	
	function login() {
		$postDATA = array (
			"usernamefld" => $this->user,
			"passwordfld" => $this->password,
			"login" => "Login",
		);
		
		$ch = curl_init ("http://{$this->ip}/index.php");
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_HEADER, 1);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:  "));
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $postDATA);
				$result = curl_exec($ch);
					preg_match('/^Set-Cookie:\s*([^;]*)/mi', $result, $m);
						parse_str($m[1], $cookies);
							$this->session = $cookies['PHPSESSID'];
							
		return "Logging in to {$this->ip}\nPHPSESSID {$this->session}\n";
	}
	
	function getCSRFMagicToken($url = null) {
		$ch = curl_init ($url);
			curl_setopt ($ch, CURLOPT_COOKIE, "PHPSESSID={$this->session}");
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:  "));
				$result = curl_exec($ch);
				
				$bits = explode('var csrfMagicToken = "', $result);
				$bits = explode('";', $bits[1]);
					$token = $bits[0];
		
		return $token;
	}
	
	function getFormItemValue($uri = null, $item = null) {
		$url = "http://{$this->ip}/$uri";
		$ch = curl_init ($url);
			curl_setopt ($ch, CURLOPT_COOKIE, "PHPSESSID={$this->session}");
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:  "));
				$result = curl_exec($ch);
				
				$bits = explode('name="' . $item . '"', $result);
				$bits = explode('>', $bits[1]);
				$bits = explode('value="', $bits[0]);
				$bits = explode('"', $bits[1]);
				$value = $bits[0];
		
		return $value;
	}
	
	function changeSettings($data) {
		$url = "http://{$this->ip}/{$data['uri']}";
		
		$data['post']['__csrf_magic'] = $this->getCSRFMagicToken($url);

		$ch = curl_init ($url);
			curl_setopt ($ch, CURLOPT_COOKIE, "PHPSESSID={$this->session}");
			curl_setopt ($ch, CURLOPT_POST, true);
			curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:  "));
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $data['post']);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
				$returndata = curl_exec ($ch);
	}
}

?>