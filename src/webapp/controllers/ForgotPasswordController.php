<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 30.08.2015
 * Time: 00:07
 */

namespace tdt4237\webapp\controllers;
use tdt4237\webapp\models\User;


class ForgotPasswordController extends Controller {

    public function __construct() {
        parent::__construct();
    }


    function forgotPassword() {
        $this->render('forgotPassword.twig', []);
    }

    function submitName() {
        $username = $this->app->request->post('username');
        if($username != "") {
            $this->app->redirect('/forgot/' . $username);
        }
        else {
            $this->render('forgotPassword.twig');
            $this->app->flash("error", "Please input a username");
        }

    }

    function confirmForm($username) {
        if($username != "") {
            $user = $this->userRepository->findByUser($username);
            $this->render('forgotPasswordConfirm.twig', ['user' => $user]);
        }
        else {
            $this->app->flashNow("error", "Please write in a username");
        }
    }

    function confirm($username) {
        //get user by username
        $user = $this->userRepository->findByUser($username);
        //send user reset mail
        $to = $user->getEmail();
        $subject = "Health Forum: Password reset";
        $temp_pass = $this->createRandomPass();
        $msg = wordwrap("Hi there,\nThis email was sent using PHP's mail function.\nYour new password is: ".$temp_pass);
        $from = "From: noreply@tdt4237.idi.ntnu.no";
        $mail = mail($to, $subject, $msg, $from);

        if ($mail) {
            $this->app->flash('success', 'Thank you! The password was sent to your email');
        } else {
            $this->app->flash('failed', 'Error: your email was not sent!');
        }

        $this->app->redirect('/login');
    }

    function deny() {

    }


    function createRandomPass()
    {
            $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < 8; $i++) 
            {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            return implode($pass); //turn the array into a string
        
    }



} 