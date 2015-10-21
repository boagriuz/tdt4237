<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\User;

class FailedLoginAttemptRepository
{

    /**
     * @var PDO
     */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save($username)
    {
		$statement = $this->db->prepare("INSERT INTO failed_logins (user) VALUES (?)");
            
		$statement->bindValue(1, $username, PDO::PARAM_STR);
			
        return $statement->execute();
    }
	
	public function getFailedAttempts($username)
	{		
		$statement = $this->db->prepare("SELECT Count(1) AS failed FROM failed_logins WHERE datetime('now') < datetime(attemptedTimestamp, '+0 day', '+0 hour', '+1 minute') AND user = ?");
		
		$statement->bindValue(1, $username, PDO::PARAM_STR);
		$statement->execute();
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		
		if ($row === false)
		{
			return 0;
		}
		
		return (int) $row['failed'];
	}
}
