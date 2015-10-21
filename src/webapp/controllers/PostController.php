<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Post;
use tdt4237\webapp\controllers\UserController;
use tdt4237\webapp\models\Comment;
use tdt4237\webapp\validation\PostValidation;

class PostController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        if ($this->auth->guest()) 
		{
            $this->app->flash("info", "You must be logged in to view posts");
            $this->app->redirect("/login");
        }
		
        $posts = $this->postRepository->all();
		$doctorVisiblePosts = $this->postRepository->doctorVisiblePosts();

        $posts->sortByDate();
		if (!empty($doctorVisiblePosts))
		{
			$doctorVisiblePosts->sortByDate();
	        $this->render('posts.twig', ['user' => $this->auth->user(), 'posts' => $posts, 'doctorVisiblePosts' => $doctorVisiblePosts]);
		}
		else
		{
        	$this->render('posts.twig', ['user' => $this->auth->user(), 'posts' => $posts]);
		}
    }

    public function show($postId)
    {
		if(!$this->auth->guest()) 
		{
        	$post = $this->postRepository->find($postId);
			
			if ($this->auth->isDoctor() and !empty($post))
			{
				$authorsName = $post->getAuthor();
				$author = $this->userRepository->findByUser($authorsName);
				if (!empty($author))
				{
					if (! $author->hasBankAccount() or ! $author->isSubscribed())
					{
						http_response_code(401);
						exit;
					}
				}
			}
			
            $comments = $this->commentRepository->findByPostId($postId);
            $request = $this->app->request;
            $message = strip_tags($request->get('msg'));
            $variables = [];
    
            if($message) 
			{
                $variables['msg'] = $message;
            }
    
            $this->render('showpost.twig', [
                'post' => $post,
                'comments' => $comments,
                'flash' => $variables
            ]);
        }
        else 
		{
			$this->app->flash('info', 'You must log in to do that');
            $this->app->redirect('/login');
        }
    }

    public function addComment($postId)
    {
        if(!$this->auth->guest()) {
            $comment = new Comment();
            $comment->setAuthor($_SESSION['user']);
            $comment->setText($this->app->request->post("text"));
            $comment->setDate(date("dmY"));
            $comment->setPost($postId);

			$authorUser = $this->userRepository->findByUser($_SESSION['user']);
			$post = $this->postRepository->find($postId);
			$answeredByADoctor = $post->getAnsweredByDoctor();	

            $this->commentRepository->save($comment);
				
			//if author is a doctor and not already answered by a doctor				
			if($authorUser->isDoctor() && !$answeredByADoctor)
			{
			//Add 7$ to the doctor's balance
				$this->userRepository->addToBalance($authorUser, 7); 
				var_dump($authorUser);
			//Subtract 10$ from the user's balance
				$postAuthor = $this->userRepository->findByUser($post->getAuthor());
				$this->userRepository->addToBalance($postAuthor, -10); 
			}
            $this->app->redirect('/posts/' . $postId);
        }
        else 
		{
            $this->app->flash('info', 'You must log in to do that');
			$this->app->redirect('/login');
        }
    }

    public function showNewPostForm()
    {
        if ($this->auth->check()) 
		{
            $username = $_SESSION['user'];
            $this->render('createpost.twig', ['username' => $username]);
        } 
		else 
		{
			$this->app->flash('error', "You need to be logged in to create a post");
            $this->app->redirect("/");
        }
    }

    public function create()
    {
        if ($this->auth->guest()) 
		{
            $this->app->flash("info", "You must be logged on to create a post");
            $this->app->redirect("/login");
        } 
		else
		{
            $request = $this->app->request;
            $title = $request->post('title');
            $content = $request->post('content');
            $author = $_SESSION['user'];
            $date = date("dmY");

            $validation = new PostValidation($title, $author, $content);
            if ($validation->isGoodToGo()) 
			{
                $post = new Post();
                $post->setAuthor($author);
                $post->setTitle($title);
                $post->setContent($content);
                $post->setDate($date);
                $savedPost = $this->postRepository->save($post);
                $this->app->redirect('/posts/' . $savedPost . '?msg="Post succesfully posted');
            }
        }

            $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
            $this->app->render('createpost.twig');
            // RENDER HERE
    }
}

