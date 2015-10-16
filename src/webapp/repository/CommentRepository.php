<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Comment;

class CommentRepository
{

    /**
     * @var PDO
     */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save(Comment $comment)
    {
        $id = $comment->getCommentId();
        $author  = $comment->getAuthor();
        $text    = $comment->getText();
        $date = (string) $comment->getDate();
        $postid = $comment->getPost();

        if ($comment->getCommentId() === null) 
        {
			$statement = $this->db->prepare("INSERT INTO comments (author, text, date, belongs_to_post) VALUES (?, ?, ?, ?)");
            
			$statement->bindValue(1, $author, PDO::PARAM_STR);
			$statement->bindValue(2, $text, PDO::PARAM_STR);
			$statement->bindValue(3, $date, PDO::PARAM_STR);
			$statement->bindValue(4, $postid, PDO::PARAM_STR);
			
            return $statement->execute();
        }
    }

    public function findByPostId($postId)
    {
		$statement = $this->db->prepare("SELECT * FROM comments WHERE belongs_to_post = ?");
		$statement->bindValue(1, $postId, PDO::PARAM_INT);
		$statement->execute();
		
		$rows = $statement->fetchAll();
		
        return array_map([$this, 'makeFromRow'], $rows);
    }

    public function makeFromRow($row)
    {
        $comment = new Comment;
        
        return $comment
            ->setCommentId($row['commentId'])
            ->setAuthor($row['author'])
            ->setText($row['text'])
            ->setDate($row['date'])
            ->setPost($row['belongs_to_post']);
    }
}
