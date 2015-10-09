<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
        }

        if (! $this->auth->isAdmin()) {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
        }

        $variables = [
            'users' => $this->userRepository->all(),
            'posts' => $this->postRepository->all()
        ];
        $this->render('admin.twig', $variables);
    }

    public function delete($username)
    {
		if (! $this->isAuthorized())
		{
			http_response_code(401);
			exit;
		}
		
        if ($this->userRepository->deleteByUsername($username) === 1) {
            $this->app->flash('info', "Sucessfully deleted '$username'");
            $this->app->redirect('/admin');
            return;
        }
        
        $this->app->flash('info', "An error ocurred. Unable to delete user '$username'.");
        $this->app->redirect('/admin');
    }

    public function deletePost($postId)
    {
		if (! $this->isAuthorized())
		{
			http_response_code(401);
			exit;
		}
		
        if ($this->postRepository->deleteByPostid($postId) === 1) {
            $this->app->flash('info', "Sucessfully deleted '$postId'");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error ocurred. Unable to delete user '$username'.");
        $this->app->redirect('/admin');
    }
	
	private function isAuthorized()
	{
		if (! $this->auth->check())
		{
			return false;
		}
		
		return $this->auth->isAdmin();
	}

    public function addPaidDoctor($username)
    {
        
        if (! $this->isAuthorized())
        {
            http_response_code(401);
            exit;
        }

        $user = $this->userRepository->findByUser($username);

        if($this->userRepository->paidDoctor($user) === 1)
        {
            $this->app->flash('info', "Sucessfully added '$username' as a doctor");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error ocurred. Unable to add user '$username' as doctor.");
        $this->app->redirect('/admin');
        
            //echoes the value set in the HTML form for each checked checkbox.
            //so, if I were to check 1, 3, and 5 it would echo value 1, value 3, value 5.
            //in your case, it would echo whatever $row['Report ID'] is equivalent to.
    }

    
}
