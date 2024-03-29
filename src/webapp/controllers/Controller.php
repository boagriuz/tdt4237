<?php

namespace tdt4237\webapp\controllers;



class Controller
{
    protected $app;
    
    protected $userRepository;
    protected $auth;
    protected $postRepository;
	protected $failedLoginAttemptRepository;

    public function __construct()
    {
        $this->app = \Slim\Slim::getInstance();
        $this->userRepository = $this->app->userRepository;
        $this->postRepository = $this->app->postRepository;
        $this->failedLoginAttemptRepository = $this->app->failedLoginAttemptRepository;
        $this->commentRepository = $this->app->commentRepository;
        $this->auth = $this->app->auth;
        $this->hash = $this->app->hash;
    }

    protected function render($template, $variables = [])
    {
        if ($this->auth->check()) {
            $variables['isLoggedIn'] = true;
            $variables['isAdmin'] = $this->auth->isAdmin();
            $variables['loggedInUsername'] = $_SESSION['user'];
            $variables['isDoctor'] = $this->auth->isDoctor();
            $variables['isSubscribed'] = $this->auth->user()->isSubscribed();
	        $variables['balance'] = $this->auth->user()->getBalance();
        }

        print $this->app->render($template, $variables);
    }
}
