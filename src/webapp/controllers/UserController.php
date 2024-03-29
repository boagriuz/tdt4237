<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\User;
use tdt4237\webapp\validation\EditUserFormValidation;
use tdt4237\webapp\validation\RegistrationFormValidation;


class UserController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->guest()) {
            return $this->render('newUserForm.twig', []);
        }

        $username = $this->auth->user()->getUserName();
        $this->app->flash('info', 'You are already logged in as ' . $username);
        $this->app->redirect('/');
    }

    public function create()
    {
        $request  = $this->app->request;
        
        $fullname = $request->post('fullname');
        $address = $request->post('address');
        $postcode = $request->post('postcode');
        $email = $request->post('email');
        $username = $request->post('user');
        $password = $request->post('pass_one');
        $retype_pass = $request->post('pass_two');
        $userRepo = $this->app->userRepository;
        $validation = new RegistrationFormValidation($username, $password, $retype_pass, $fullname, $address, $postcode, $email, $this->app->userRepository);

        if ($validation->isGoodToGo()) {
            $password = $password;
			$salt = $this->hash->generateSalt();
            $password = $this->hash->make($password, $salt);
            $user = new User($username, $password, $salt, $fullname, $address, $postcode, $email);
            $this->userRepository->save($user);

            $this->app->flash('info', 'Thanks for creating a user. Now log in.');
            return $this->app->redirect('/login');
        }

        $errors = join('<br />', $validation->getValidationErrors());
        $this->app->flashNow('error', $errors);
        $this->render('newUserForm.twig', ['fullname' => $fullname, 'address' => $address, 'email' => $email, 'postcode' => $postcode, 'username' => $username]);
    }

    public function all()
    {
        $this->render('users.twig', [
            'users' => $this->userRepository->all()
        ]);
    }

    public function logout()
    {
        $this->auth->logout();
        $this->app->redirect('/login');
    }

    public function show($username)
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged in to do that");
            $this->app->redirect("/login");

        } else {
            $user = $this->userRepository->findByUser($username);

            if ($user != false && $user->getUsername() == $this->auth->getUsername()) {

                $this->render('showuser.twig', [
                    'user' => $user,
                    'username' => $username
                ]);
            } else if ($this->auth->check()) {

                $this->render('showuserlite.twig', [
                    'user' => $user,
                    'username' => $username
                ]);
            }
        }
    }

    public function showUserEditForm()
    {
        $this->makeSureUserIsAuthenticated();

        $this->render('edituser.twig', [
            'user' => $this->auth->user()
        ]);
    }

    public function receiveUserEditForm()
    {
        $this->makeSureUserIsAuthenticated();
        $user = $this->auth->user();

        $request = $this->app->request;
        $email   = $request->post('email');
        $bio     = $request->post('bio');
        $age     = $request->post('age');
        $fullname = $request->post('fullname');
        $address = $request->post('address');
        $postcode = $request->post('postcode');
		$bankaccount = $request->post('bankaccount');
		$isSubscribed = ($request->post('isSubscribed')) === "1" ? 1 : 0;

        $validation = new EditUserFormValidation($fullname, $address, $postcode, $email, $bio, $age, $bankaccount);

        if ($validation->isGoodToGo()) {
            $user->setEmail(new Email($email));
            $user->setBio($bio);
            $user->setAge(new Age($age));
            $user->setFullname($fullname);
            $user->setAddress($address);
            $user->setPostcode($postcode);
			$user->setBankAccount($bankaccount);
			$user->setIsSubscribed($isSubscribed);
            $this->userRepository->save($user);

            $this->app->flashNow('info', 'Your profile was successfully saved.');
            return $this->render('edituser.twig', ['user' => $user]);
        }

        $this->app->flashNow('error', join("<br/>", $validation->getValidationErrors()));
        $this->render('edituser.twig', ['user' => $user]);
    }

    public function makeSureUserIsAuthenticated()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', 'You must be logged in to edit your profile.');
            $this->app->redirect('/login');
        }
    }
}
