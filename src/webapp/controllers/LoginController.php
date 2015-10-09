<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\repository\UserRepository;

class LoginController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->check()) 
		{
            $username = $this->auth->user()->getUsername();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
            return;
        }

        $this->render('login.twig', []);
    }

    public function login()
    {
        $request = $this->app->request;
        $user    = $request->post('user');
        $pass    = $request->post('pass');

        if ($this->auth->checkCredentials($user, $pass)) 
		{
            $_SESSION['user'] = $user;
           // setcookie("user", $user, 0, "/", "localhost:8080/", FALSE, TRUE);
           // setcookie("password",  $pass);
            $isAdmin = $this->auth->user()->isAdmin();

            if ($isAdmin) 
			{
                $_SESSION['isadmin'] = "yes";
            } 
			else 
			{
                $_SESSION['isadmin'] = "no";
            }

            $isDoctor = $this->auth->user()->isDoctor();

            if($isDoctor){
                $_SESSION['isdoctor'] = "yes";
            }
            else
            {
                $_SESSION['isdoctor'] = "no";
            }
            
            $this->app->flash('info', "You are now successfully logged in as $user.");
            $this->app->redirect('/');
            return;
        }
        
        $this->app->flashNow('error', 'Incorrect user/pass combination.');
        $this->render('login.twig', []);
    }
}
