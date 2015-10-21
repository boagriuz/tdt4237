<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\NullUser;
use tdt4237\webapp\models\User;

class UserRepository
{
    const SELECT_ALL = "SELECT * FROM users";
    protected $INSERT_QUERY;
    protected $UPDATE_QUERY;
	protected $UPDATE_BALANCE;
    protected $FIND_BY_NAME;
    protected $DELETE_BY_NAME;
    protected $FIND_FULL_NAME;

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
		$this->UPDATE_QUERY = $this->pdo->prepare("UPDATE users SET email=?, age=?, bio=?, isadmin=?, fullname =?, address=?, postcode=?, isdoctor=?, bankaccount=?, issubscribed=? WHERE id=?");
		$this->UPDATE_BALANCE = $this->pdo->prepare("UPDATE users SET balance=? WHERE id=?");
		$this->INSERT_QUERY = $this->pdo->prepare("INSERT INTO users(user, pass, salt, email, age, bio, isadmin, fullname, address, postcode, isdoctor, bankaccount, issubscribed) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$this->FIND_BY_NAME = $this->pdo->prepare("SELECT * FROM users WHERE user=?");
		$this->DELETE_BY_NAME = $this->pdo->prepare("DELETE FROM users WHERE user=?");
		$this->FIND_FULL_NAME = $this->pdo->prepare("SELECT * FROM users WHERE user=?");
    }

    public function makeUserFromRow(array $row)
    {
        $user = new User($row['user'], $row['pass'], $row['salt'], $row['fullname'], $row['address'], $row['postcode'], $row['email']);
        $user->setUserId($row['id']);
        $user->setFullname($row['fullname']);
        $user->setAddress(($row['address']));
        $user->setPostcode((($row['postcode'])));
        $user->setBio($row['bio']);
        $user->setEmail($row['email']);
        $user->setIsAdmin($row['isadmin']);
        $user->setIsDoctor($row['isdoctor']);
		$user->setIsSubscribed($row['issubscribed']);
		
		if (!empty($row['bankaccount']))
		{
			$user->setBankAccount($row['bankaccount']);
		}

        if (!empty($row['email'])) {
            $user->setEmail(new Email($row['email']));
        }

        if (!empty($row['age'])) {
            $user->setAge(new Age($row['age']));
        }

		if(!empty($row['balance'])){
			$user->setBalance($row['balance']);
		}

        return $user;
    }

    public function getNameByUsername($username)
    {
		$this->FIND_FULL_NAME->bindValue(1, $username, PDO::PARAM_STR);
		$this->FIND_FULL_NAME->execute();
		$row = $this->FIND_FULL_NAME->fetch(PDO::FETCH_ASSOC);
		
        return $row['fullname'];
    }

    public function findByUser($username)
    {
		$this->FIND_BY_NAME->bindValue(1, $username, PDO::PARAM_STR);
        $this->FIND_BY_NAME->execute();
		$row = $this->FIND_BY_NAME->fetch(PDO::FETCH_ASSOC);
        
        if ($row === false) 
        {
            return false;
        }

        return $this->makeUserFromRow($row);
    }

    public function deleteByUsername($username)
    {
		$this->DELETE_BY_NAME->bindValue(1, $username, PDO::PARAM_STR);
		return $this->DELETE_BY_NAME->execute();
    }

    public function all()
    {
        $rows = $this->pdo->query(self::SELECT_ALL);
        
        if ($rows === false) {
            return [];
            throw new \Exception('PDO error in all()');
        }

        return array_map([$this, 'makeUserFromRow'], $rows->fetchAll());
    }

    public function save(User $user)
    {
        if ($user->getUserId() === null) {
            return $this->saveNewUser($user);
        }

        $this->saveExistingUser($user);
    }

    public function saveNewUser(User $user)
    {	
		$this->INSERT_QUERY->BindValue(1, $user->getUsername(), PDO::PARAM_STR);
		$this->INSERT_QUERY->BindValue(2, $user->getHash(), PDO::PARAM_STR);
		$this->INSERT_QUERY->BindValue(3, $user->getSalt(), PDO::PARAM_STR);
		$this->INSERT_QUERY->BindValue(4, $user->getEmail(), PDO::PARAM_STR);
		$this->INSERT_QUERY->BindValue(5, $user->getAge(), PDO::PARAM_STR);
		$this->INSERT_QUERY->BindValue(6, $user->getBio(), PDO::PARAM_STR);
		$this->INSERT_QUERY->BindValue(7, $user->getIsAdmin(), PDO::PARAM_INT);
		$this->INSERT_QUERY->BindValue(8, $user->getFullname(), PDO::PARAM_STR);
		$this->INSERT_QUERY->BindValue(9, $user->getAddress(), PDO::PARAM_STR);
		$this->INSERT_QUERY->BindValue(10, $user->getPostcode(), PDO::PARAM_STR);
		$this->INSERT_QUERY->BindValue(11, $user->getIsDoctor(), PDO::PARAM_INT);
		$this->INSERT_QUERY->BindValue(12, $user->getBankAccount(), PDO::PARAM_STR);
		$this->INSERT_QUERY->BindValue(13, $user->getIsSubscribed(), PDO::PARAM_INT);

        return $this->INSERT_QUERY->execute();
    }

    public function saveExistingUser(User $user)
    {
		$this->UPDATE_QUERY->bindValue(1, $user->getEmail(), PDO::PARAM_STR);
		$this->UPDATE_QUERY->bindValue(2, $user->getAge(), PDO::PARAM_STR);
		$this->UPDATE_QUERY->bindValue(3, $user->getBio(), PDO::PARAM_STR);
		$this->UPDATE_QUERY->bindValue(4, $user->getIsAdmin(), PDO::PARAM_INT);
		$this->UPDATE_QUERY->bindValue(5, $user->getFullname(), PDO::PARAM_STR);
		$this->UPDATE_QUERY->bindValue(6, $user->getAddress(), PDO::PARAM_STR);
		$this->UPDATE_QUERY->bindValue(7, $user->getPostcode(), PDO::PARAM_STR);
		$this->UPDATE_QUERY->bindValue(8, $user->getIsDoctor(), PDO::PARAM_INT);
		$this->UPDATE_QUERY->bindValue(9, $user->getBankAccount(), PDO::PARAM_STR);
		$this->UPDATE_QUERY->bindValue(10, $user->getIsSubscribed(), PDO::PARAM_INT);
		$this->UPDATE_QUERY->bindValue(11, $user->getUserId(), PDO::PARAM_INT);
		
		return $this->UPDATE_QUERY->execute();
    }

	public function saveBalance(User $user)
	{
		$this->UPDATE_BALANCE->bindValue(1, $user->getBalance(), PDO::PARAM_INT);
		$this->UPDATE_BALANCE->bindValue(2, $user->getUserId(), PDO::PARAM_INT);

		return $this->UPDATE_BALANCE->execute();
	}

    public function paidDoctor(User $user)
    {
        $user->setIsDoctor(1);
        return $this->saveExistingUser($user);
    }

    public function deleteDoctor(User $user)
	{
        $user->setIsDoctor(0);
        return $this->saveExistingUser($user);
    }
	public function addToBalance(User $user, $amount) 
	{
		$balance = $user->getBalance();
		$balance += $amount;
		$user->setBalance($balance);
		return $this->saveBalance($user);
	}
}
