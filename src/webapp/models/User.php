<?php

namespace tdt4237\webapp\models;

class User
{
    protected $userId  = null;
    protected $username;
    protected $fullname;
    protected $address;
    protected $postcode;
    protected $hash;
    protected $email   = null;
    protected $bio     = 'Bio is empty.';
    protected $age;
    protected $isAdmin = 0;
    protected $isDoctor = 0;
	protected $bankaccount = '0';
	protected $isSubscribed = 0;

    function __construct($username, $hash, $fullname, $address, $postcode, $email)
    {
        $this->username = $username;
        $this->hash = $hash;
        $this->fullname = $fullname;
        $this->address = $address;
        $this->postcode = $postcode;
        $this->email = $email;
		$this->isAdmin = 0;
		$this->isDoctor = 0;
		$this->bankaccount = 0;
		$this->isSubscribed = 0;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getFullname() {
        return $this->fullname;
    }

    public function setFullname($fullname) {
        $this->fullname = $fullname;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getPostcode() {
        return $this->postcode;
    }

    public function setPostcode($postcode) {
        $this->postcode = $postcode;
    }

    public function isAdmin()
    {
        return $this->isAdmin == '1';
    }

    public function isDoctor()
    {
        return $this->isDoctor == '1';
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
        return $this;
    }

    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }
	
	public function getIsAdmin()
	{
		return $this->isAdmin;
	}

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }
	
	public function getIsDoctor()
	{
		return $this->isDoctor;
	}

    public function setIsDoctor($isDoctor)
	{
        $this->isDoctor = $isDoctor;
        return $this;
    }
	
	public function getBankAccount()
	{
		return $this->bankaccount;
	}
	
	public function setBankAccount($bankaccount)
	{
		$this->bankaccount = $bankaccount;
		return $this;
	}
	
	public function isSubscribed()
	{
		return $this->isSubscribed == '1';
	}
	
	public function getIsSubscribed()
	{
		return $this->isSubscribed;
	}
	
	public function setIsSubscribed($isSubscribed)
	{
		$this->isSubscribed = $isSubscribed;
		return $this;
	}
}
