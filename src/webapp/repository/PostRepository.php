<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Post;
use tdt4237\webapp\models\PostCollection;

class PostRepository
{

    /**
     * @var PDO
     */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    
    public static function create($id, $author, $title, $content, $date, $answeredByDoctor)
    {
        $post = new Post;
        
        return $post
            ->setPostId($id)
            ->setAuthor($author)
            ->setTitle($title)
            ->setContent($content)
            ->setDate($date)
			->setAnsweredByDoctor($answeredByDoctor);
    }

    public function find($postId)
    {
		$statement = $this->db->prepare("SELECT *,
				CASE WHEN EXISTS (
					SELECT * from comments JOIN users ON comments.author == users.user WHERE posts.postId == comments.belongs_to_post AND users.isdoctor == 1) 
					THEN '1'
					ELSE '0' 
				END AS answeredByDoctor  
			FROM posts WHERE postId = ?");
        $statement->bindValue(1, $postId, PDO::PARAM_INT);
		$statement->execute();
		$row = $statement->fetch();

        if($row === false) {
            return false;
        }

        return $this->makeFromRow($row);
    }
	
	public function doctorVisiblePosts()
	{
		$sql = "SELECT *,
			CASE WHEN EXISTS (
					SELECT * from comments JOIN users ON comments.author == users.user WHERE posts.postId == comments.belongs_to_post AND users.isdoctor == 1) 
					THEN '1'
					ELSE '0' 
				END AS answeredByDoctor 		
			FROM posts JOIN users ON posts.author == users.user WHERE (NOT users.bankaccount == 0) AND users.issubscribed == 1"; //TODO: Add answered by doctor
        $results = $this->db->query($sql);

        if($results === false) {
            return [];
            throw new \Exception('PDO error in posts all()');
        }

        $fetch = $results->fetchAll();
        if(count($fetch) == 0) {
            return false;
        }

        return new PostCollection(
            array_map([$this, 'makeFromRow'], $fetch)
        );
	}

    public function all()
    {
        $sql   = "SELECT posts.*,
			CASE WHEN EXISTS (
				SELECT * from comments JOIN users ON comments.author == users.user WHERE posts.postId == comments.belongs_to_post AND users.isdoctor == 1) 
					THEN '1'
					ELSE '0' 
				END AS answeredByDoctor 
			FROM posts";
        $results = $this->db->query($sql);

        if($results === false) {
            return [];
            throw new \Exception('PDO error in posts all()');
        }

        $fetch = $results->fetchAll();
        if(count($fetch) == 0) {
            return false;
        }

        return new PostCollection(
            array_map([$this, 'makeFromRow'], $fetch)
        );
    }

    public function makeFromRow($row)
    {
        return static::create(
            $row['postId'],
            $row['author'],
            $row['title'],
            $row['content'],
            $row['date'],
			$row['answeredByDoctor']
        );

       //  $this->db = $db;
    }

    public function deleteByPostid($postId)
    {
		$statement = $this->db->prepare("DELETE FROM posts WHERE postid=?");
		$statement->bindValue(1, $postId, PDO::PARAM_INT);
        return $statement->execute();
    }


    public function save(Post $post)
    {
        $title   = $post->getTitle();
        $author  = $post->getAuthor();
        $content = $post->getContent();
        $date    = $post->getDate();
		
		$statement = $this->db->prepare("INSERT INTO posts (title, author, content, date) VALUES (?, ?, ?, ?)");
		
		$statement->bindValue(1, $title, PDO::PARAM_STR);
		$statement->bindValue(2, $author, PDO::PARAM_STR);
		$statement->bindValue(3, $content, PDO::PARAM_STR);
		$statement->bindValue(4, $date, PDO::PARAM_STR);
		$statement->execute();

        return $this->db->lastInsertId();
    }
}
