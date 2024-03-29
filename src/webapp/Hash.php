<?php

namespace tdt4237\webapp;

use Symfony\Component\Config\Definition\Exception\Exception;

class Hash
{
    public function __construct()
    {
    }
	
	public static function generateSalt()
	{
		return bin2hex(openssl_random_pseudo_bytes(64));
	}

    public static function make($plaintext, $salt)
    {
        return hash('sha512', $plaintext . $salt);
    }

    public function check($plaintext, $salt, $hash)
    {
        return $this->make($plaintext, $salt) === $hash;
    }
}
