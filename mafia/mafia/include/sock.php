<?php
# $Date: 2007-02-09 11:03:32 +0000 (Fri, 09 Feb 2007) $
# iono Licensing Integration #5 (fsock) - http://www.olate.co.uk
/**
 * iono License Key File Handling
 *
 * @copyright Olate Ltd 2007
 * @link http://www.olate.co.uk
 * @version 1.2.2
 * @package iono
 */
class iono_keys
{
		/**
		 * @var string The user's license key
		 * @access private
		 */

		var $license_key;

		/**
		 * @var string The iono root site location
		 * @access private
		 */

		var $home_url_site = 'www.game-script.net';

		/**
		 * @var int The iono root site location port for access
		 * @access private
		 */

		var $home_url_port = 80;

		/**
		 * @var string The iono location
		 * @access private
		 */

		var $home_url_iono = '/order/remote.php';

		/**
		 * @var string The location of the key file to use
		 * @access private
		 */

		var $key_location;

		/**
		 * @var string Remote Authentication String from your iono installation
		 * @access private
		 */

		var $remote_auth;

		/**
		 * @var int The maximum age of the key file before it is regenerated (seconds)
		 * @access private
		 */

		var $key_age;

		/**
		 * @var array The data stored in the key
		 * @access private
		 */

		var $key_data;

		/**
		 * @var int Current timestamp. Needs to be constant throughout class so is set here
		 * @access private
		 */

		var $now;

		/**
		 * @var int The result of the key actions
		 * @access public
		 */

		var $result;

		/**
		 * Sets the class vars and then checks the key file.
		 * @param string $license_key The user's license key
		 * @param string $remote_auth The remote authorisation string from iono settings
		 * @param string $key_location The location of the key file to use
		 * @param int $key_age The maximum age of the key file before it is regenerated (seconds) default 15 days (1296000)
		 */

		function iono_keys($license_key, $remote_auth, $key_location = 'key.php', $key_age = 1296000)
		{
				// Set the class vars
				$this->license_key = $license_key;
				$this->remote_auth = $remote_auth;
				$this->key_location = $key_location;
				$this->key_age = $key_age;
				$this->now = time();
				if (empty($license_key))
				{
						$this->result = 4;
						return false;
				}
				if (empty($remote_auth))
				{
						$this->result = 4;
						return false;
				}
				// Does the key exist? If not, then we need to create it. Else read it.
				if (file_exists($this->key_location))
				{
						$this->result = $this->read_key();
				}
				else
				{
						$this->result = $this->generate_key();
						if (empty($this->result))
						{
								$this->result = $this->read_key();
						}
				}
				unset($this->remote_auth);
				return true;
		}
		/**
		 * Gets the license details form the iono server and writes to the key file
		 *
		 * Responses:
		 * - 8: License disabled
		 * - 9: License suspended
		 * - 5: License expired
		 * - 10: Unable to open file for writing
		 * - 11: Unable to write to file
		 * - 12: Unable to communicate with iono
		 * @return int Response code
		 * @access private
		 */
		function generate_key()
		{
				// Build request
				$request = 'remote=licenses&type=5&license_key=' . urlencode(base64_encode($this->license_key));
				$request .= '&host_ip=' . urlencode(base64_encode($_SERVER['SERVER_ADDR'])) . '&host_name=' . urlencode(base64_encode($_SERVER['SERVER_NAME']));
				$request .= '&hash=' . urlencode(base64_encode(md5($request)));
				$request = $this->home_url_iono . '?' . $request;
				// Build HTTP header
				$header = "GET $request HTTP/1.0\r\nHost: $this->home_url_site\r\nConnection: Close\r\nUser-Agent: iono (www.olate.co.uk/iono)\r\n";
				$header .= "\r\n\r\n";
				// Contact license server
				$fpointer = @fsockopen($this->home_url_site, $this->home_url_port, $errno, $errstr, 5);
				$return = '';
				if ($fpointer)
				{
						@fwrite($fpointer, $header);
						while (!@feof($fpointer))
						{
								$return .= @fread($fpointer, 1024);
						}
						@fclose($fpointer);
				}
				else
				{
						return 12;
				}
				// Get rid of HTTP headers
				$content = explode("\r\n\r\n", $return);
				$content = explode($content[0], $return);
				//echo $content[1];
				//exit;
				// Split up the content
				$string = urldecode($content[1]);
				//$string = '1|key|' .(time()+2000) .'|localhost|127.0.0.1';
				$exploded = explode('|', $string);
				switch ($exploded[0]) // If we have an inactive license, return the status code

				{
						case 0: // Disabled
								return 8;
								break;
						case 2: // Suspended
								return 9;
								break;
						case 3: // Expired
								return 5;
								break;
						case 10: // Invalid key
								return 4;
								break;
				}
				$data['license_key'] = $exploded[1];
				$data['expiry'] = $exploded[2];
				$data['hostname'] = $exploded[3];
				$data['ip'] = $exploded[4];
				$data['timestamp'] = $this->now;
				// On first generation the hostname and IP will be blank
				// So set to current values
				if (empty($data['hostname']))
				{
						$data['hostname'] = $_SERVER['SERVER_NAME'];
				}
				if (empty($data['ip']))
				{
						$data['ip'] = $_SERVER['SERVER_ADDR'];
				}
				$data_encoded = serialize($data);
				$data_encoded = base64_encode($data_encoded);
				$data_encoded = md5($this->now . $this->remote_auth) . $data_encoded;
				$data_encoded = strrev($data_encoded);
				$data_encoded_hash = sha1($data_encoded . $this->remote_auth);
				$fp = fopen($this->key_location, 'w');
				if ($fp)
				{
						$fp_write = fwrite($fp, wordwrap($data_encoded . $data_encoded_hash, 40, "\n", true));
						if (!$fp_write)
						{
								return 11; // Unable to write to file
						}
						fclose($fp);
				}
				else
				{
						return 10; // Unable to open file for writing
				}
		}
		/**
		 * Read the key file and then return a response code
		 *
		 * Responses:
		 * - 0: Unable to read key
		 * - 1: Everything is OK
		 * - 2: SHA1 hash incorrect (key may have been tampered with)
		 * - 3: MD5 hash incorrect (key may have been tampered with)
		 * - 4: License key does not match key string in key file
		 * - 5: License has expired
		 * - 6: Host name does not match key file
		 * - 7: IP does not match key file
		 * @return int Response code
		 * @access private
		 */
		function read_key()
		{
				$key = file_get_contents($this->key_location);
				if ($key !== false)
				{
						$key = str_replace("\n", '', $key); // Remove the line breaks from the key string
						// Split out SHA1 hash from the key data
						$key_string = substr($key, 0, strlen($key) - 40);
						$key_sha_hash = substr($key, strlen($key) - 40, (strlen($key)));
						if (sha1($key_string . $this->remote_auth) == $key_sha_hash) // Compare SHA1 hash to the key data

						{
								$key = strrev($key_string); // Back the right way around
								$key_hash = substr($key, 0, 32); // Get the MD5 hash of the data from the string
								$key_data = substr($key, 32); // Get the data from the string
								$key_data = base64_decode($key_data);
								$key_data = unserialize($key_data);
								if (md5($key_data['timestamp'] . $this->remote_auth) == $key_hash) // Check the MD5 hash

								{
										// Is it more than $this->key_age seconds old?
										if (($this->now - $key_data['timestamp']) >= $this->key_age)
										{
												unlink($this->key_location);
												$this->result = $this->generate_key();
												if (empty($this->result))
												{
														$this->result = $this->read_key();
												}
												return 1; // Have to return here because there is a 1 second delay due to the nature of time()
										}
										else
										{
												$this->key_data = $key_data;
												if ($key_data['license_key'] != $this->license_key)
												{
														return 4; // License key does not match key string in key file
												}
												if ($key_data['expiry'] <= $this->now && $key_data['expiry'] != 1)
												{
														return 5; // License key does not match key string in key file
												}
												// Do we have multiple hostnames?
												if (substr_count($key_data['hostname'], ',') == 0)
												{ // No
														if ($key_data['hostname'] != $_SERVER['SERVER_NAME'] && !empty($key_data['hostname']))
														{
																return 6; // Host name does not match key file
														}
												}
												else
												{ // Yes
														$hostnames = explode(',', $key_data['hostname']);
														if (!in_array($_SERVER['SERVER_NAME'], $hostnames))
														{
																return 6; // Host name is not in key file
														}
												}
												// Do we have multiple IPs?
												if (substr_count($key_data['ip'], ',') == 0)
												{ // No
														if ($key_data['ip'] != $_SERVER['SERVER_ADDR'] && !empty($key_data['ip']))
														{
																return 7; // IP does not match key file
														}
												}
												else
												{ // yes
														$ips = explode(',', $key_data['ip']);
														if (!in_array($_SERVER['SERVER_ADDR'], $ips))
														{
																return 7; // IP is not in key file
														}
												}
												return 1;
										}
								}
								else
								{
										return 3; // MD5 hash incorrect (key may have been tampered with)
								}
						}
						else
						{
								return 2; // SHA1 hash incorrect (key may have been tampered with)
						}
				}
				else
				{
						return 0;
				}
		}
		/**
		 * Returns array of key data
		 *
		 * @return array Array of data in the key file
		 */
		function get_data()
		{
				return $this->key_data;
		}
}
?>