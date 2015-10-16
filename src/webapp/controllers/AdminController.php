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
		else
		{
	        $this->app->flash('info', "An error ocurred. Unable to delete user '$username'.");
	        $this->app->redirect('/admin');
		}
    }

    public function deletePost($postId)
    {
		if (! $this->isAuthorized())
		{
			http_response_code(401);
			exit;
		}
		
        if ($this->postRepository->deleteByPostid($postId) !== false) {
            $this->app->flash('info', "Sucessfully deleted '$postId'");
            $this->app->redirect('/admin');
            return;
        }
		else
		{
	        $this->app->flash('info', "An error ocurred. Unable to delete post '$postId'.");
	        $this->app->redirect('/admin');
		}
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
        
        if($this->userRepository->paidDoctor($user) !== false)
        {
            $this->app->flash('info', "Sucessfully added '$username' as a doctor");
            $this->app->redirect('/admin');
            return;
        }
		else
		{
	        $this->app->flash('info', "An error ocurred. Unable to add user '$username' as doctor.");
	        $this->app->redirect('/admin');
		}
    }


    public function deletePaidDoctor($username)
    {
        if (! $this->isAuthorized())
        {
            http_response_code(401);
            exit;
        }

        $user = $this->userRepository->findByUser($username);
        
        if($this->userRepository->deleteDoctor($user) !== false)
        {
            $this->app->flash('info', "Removed '$username' as a doctor");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error ocurred. Unable to remove user '$username' as doctor.");
        $this->app->redirect('/admin');
    }

    
}
