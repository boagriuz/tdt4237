<?php

namespace tdt4237\webapp;

use Exception;
use tdt4237\webapp\Hash;
use tdt4237\webapp\repository\UserRepository;
use tdt4237\webapp\repository\FailedLoginAttemptRepository;

class Auth
{

    /**
     * @var Hash
     */
    private $hash;

    /**
     * @var UserRepository
     */
    private $userRepository;
	
	private $failedLoginAttemptRepository;

    public function __construct(UserRepository $userRepository, FailedLoginAttemptRepository $failedLoginAttemptRepository, Hash $hash)
    {
        $this->userRepository = $userRepository;
		$this->failedLoginAttemptRepository = $failedLoginAttemptRepository;
        $this->hash           = $hash;
    }

    public function checkCredentials($username, $password)
    {
        $user = $this->userRepository->findByUser($username);

        if ($user === false)
		{
            return false;
        }
		
		$isPasswordCorrect = $this->hash->check($password, $user->getSalt(), $user->getHash());

		if ($isPasswordCorrect === false)
		{
			$this->failedLoginAttemptRepository->Save($user->getUsername());
		}
		
        return $isPasswordCorrect;
    }

    /**
     * Check if is logged in.
     */
    public function check()
    {
        return isset($_SESSION['user']);
    }

    public function getUsername() {
        if(isset($_SESSION['user'])){
        return $_SESSION['user'];
        }
    }

    /**
     * Check if the person is a guest.
     */
    public function guest()
    {
        return $this->check() === false;
    }

    /**
     * Get currently logged in user.
     */
    public function user()
    {
        if ($this->check()) {
            return $this->userRepository->findByUser($_SESSION['user']);
        }

        throw new Exception('Not logged in but called Auth::user() anyway');
    }

    /**
     * Is currently logged in user admin?
     */
    public function isAdmin()
    {
        if ($this->check()) {
            return $_SESSION['isadmin'] === 'yes';
        }

        throw new Exception('Not logged in but called Auth::isAdmin() anyway');
    }

    public function isDoctor()
    {
        if($this->check()){
            return $_SESSION['isdoctor'] === 'yes';
        }
        
        throw new Exception('Not logged in but called Auth::isDoctor() anyway');
    }

    public function logout()
    {
        if(! $this->guest()) {
            session_destroy();
        }
    }

}
