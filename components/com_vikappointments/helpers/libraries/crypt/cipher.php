<?php
/** 
 * @package   	VikAppointments
 * @subpackage 	com_vikappointments
 * @author    	Matteo Galletti - e4j
 * @copyright 	Copyright (C) 2019 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license  	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link 		https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * SecureCipher extends JCrypt for encryption, decryption and key generation/storage.
 *
 * @since  	1.6
 */
final class SecureCipher extends JCryptCipherCrypto
{
	/**
	 * Encryption key object.
	 *
	 * @var JCryptKey
	 */
	private $key = null;

	/**
	 * The instance of the class, which can be instantiated only once.
	 *
	 * @var SecureCipher
	 */
	private static $instance = null;

	/**
	 * Class constructor.
	 * The very first time this class is used, it attempts to create a new encryption key,
	 * which will be retrieved for all the future useges.
	 *
	 * @uses JCryptCipherCrypto::generateKey()
	 */
	private function __construct()
	{
		// constructor not accessible

		// get config with debug enabled
		$config = UIFactory::getConfig();

		// recover key from configuration (obtain JSON string)
		$key = $config->get('securehashkey', null);

		if (!$key)
		{
			// if key is empty, generate a new one
			$key = $this->generateKey();

			// build standard class to represent secure key
			$obj = new stdClass;
			$obj->type 		= $key->type;
			$obj->public 	= base64_encode($key->public); 	// encode public key in base 64 to avoid errors
			$obj->private 	= base64_encode($key->private); // encode private key in base 64 to avoid errors

			// store key in configuration
			$config->set('securehashkey', $obj);
		}
		else
		{
			// key is not empty, decode stored key
			$key = json_decode($key);

			// create a new JCryptKey instance with the stored keys
			$key = new JCryptKey($key->type, base64_decode($key->private), base64_decode($key->public));
		}

		$this->key = $key;

	}

	/**
	 * Class cloner.
	 */
	private function __clone()
	{
		// cloning function not accessible
	}

	/**
	 * Instantiates a new SecureCipher object.
	 *
	 * @return 	self  The cipher object.
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new SecureCipher();
		}

		return self::$instance;
	}

	/**
	 * @override
	 * Method used to encrypt a data string.
	 *
	 * @param   string 		$data  	The data string to encrypt.
	 * @param   JCryptKey  	$key   	The key object to use for encryption.
	 *								In case the key is empty, get the existing one.
	 *
	 * @return  string  	The encrypted data string.
	 *
	 * @throws  RuntimeException
	 *
	 * @uses 	JCryptCipherCrypto::encrypt()
	 */
	public function encrypt($data, JCryptKey $key = null)
	{
		// the encryption key becomes optional
		if ($key === null)
		{
			// when the encription key is not provided, get the existing one
			$key = $this->key;
		}

		// call the parent method to encrypt
		return parent::encrypt($data, $key);
	}

	/**
	 * @override
	 * Method used to decrypt a data string.
	 *
	 * @param   string     	$data  	The encrypted string to decrypt.
	 * @param   JCryptKey  	$key   	The key object to use for decryption.
	 *								In case the key is empty, get the existing one.
	 *
	 * @return  string  	The decrypted data string.
	 *
	 * @throws  RuntimeException
	 *
	 * @uses 	JCryptCipherCrypto::decrypt()
	 */
	public function decrypt($data, JCryptKey $key = null)
	{
		// the encryption key becomes optional
		if ($key === null)
		{
			// when the encription key is not provided, get the existing one
			$key = $this->key;
		}

		// call the parent method to decrypt
		return parent::decrypt($data, $key);
	}

	/**
	 * Method used to encrypt safely a data string using a Base 64 encoding.
	 *
	 * @param   string 		$data  	The data string to encrypt.
	 * @param   JCryptKey  	$key   	The key object to use for encryption.
	 *								In case the key is empty, get the existing one.
	 *
	 * @return  string  	The encrypted data string in Base 64.
	 *
	 * @throws  RuntimeException
	 *
	 * @uses SecureCipher::encrypt()
	 */
	public function safeEncodingEncryption($data, JCryptKey $key = null)
	{
		return base64_encode($this->encrypt($data, $key));
	}

	/**
	 * Method used to decrypt a data string encoded in Base 64.
	 *
	 * @param   string     	$data  	The encrypted Base 64 string to decrypt.
	 * @param   JCryptKey  	$key   	The key object to use for decryption.
	 *								In case the key is empty, get the existing one.
	 *
	 * @return  string  	The decrypted data string.
	 *
	 * @throws  RuntimeException
	 *
	 * @uses SecureCipher::decrypt()
	 */
	public function safeEncodingDecryption($data, JCryptKey $key = null)
	{
		return $this->decrypt(base64_decode($data), $key);
	}
}
