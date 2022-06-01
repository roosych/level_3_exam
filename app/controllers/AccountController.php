<?php

namespace App\controllers;

use App\classes\QueryBuilder;
use League\Plates\Engine;
use Delight\Auth\Auth;
use Delight\Auth\Role;
use Delight\Auth\InvalidEmailException;
use Delight\Auth\InvalidPasswordException;
use Delight\Auth\UserAlreadyExistsException;
use Delight\Auth\TooManyRequestsException;
use PDO;
use Tamtamchik\SimpleFlash\Flash;

class AccountController
{
    private $template;
    private $auth;
    private $pdo;
    private $queryBuilder;
    private $user;

    public function __construct(PDO $pdo, Engine $template, Auth $auth, QueryBuilder $queryBuilder, UserController $user)
    {
        $this->pdo = $pdo;
        $this->template = $template;
        $this->auth = $auth;
        $this->queryBuilder = $queryBuilder;
        $this->user = $user;
    }

    public function login() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /users');
            exit();
        }
        echo $this->template->render('login');
    }

    public function register() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /users');
            exit();
        }
        echo $this->template->render('register');
    }

    public function auth() {
        try {
            $this->auth->login($_POST['email'], $_POST['password']);
            header('Location: /users');
            exit();
        }
        catch (InvalidEmailException $e) {
            Flash::error('Неверный логин или пароль');
            header('Location: /login');
            exit();
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            Flash::error('Неверный логин или пароль');
            header('Location: /login');
            exit();
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            Flash::error('Ваш email не подтверждён');
            header('Location: /login');
            exit();
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            Flash::error('Попробуйте позже');
            header('Location: /login');
            exit();
        }
    }

    public function registration() {
        try {
            $this->auth->register($_POST['email'], $_POST['password']);
        }
        catch (InvalidEmailException $e) {
            Flash::error('<strong>Уведомление!</strong> Введите корректный email.');
            header('Location: /register');
            exit();
        }
        catch (InvalidPasswordException $e) {
            die('Invalid password');
        }
        catch (UserAlreadyExistsException $e) {
            Flash::error('<strong>Уведомление!</strong> Этот эл. адрес уже занят другим пользователем.');
            header('Location: /register');
            exit();
        }
        catch (TooManyRequestsException $e) {
            Flash::error('<strong>Уведомление!</strong> Повторите попытку позже.');
            header('Location: /register');
            exit();
        }
    }

    public function create() {
        try {
            if ($this->auth->hasRole(Role::ADMIN)) {
                $this->auth->admin()->createUser($_POST['email'], $_POST['password']);
                $this->user->createUserInfo();
                Flash::success('Пользователь успешно создан');
                header('Location: /users');
                exit();
            }
        }
        catch (InvalidEmailException $e) {
            Flash::error('<strong>Уведомление!</strong> Введите корректный email.');
            header('Location: /user/add');
            exit();
        }
        catch (InvalidPasswordException $e) {
            die('Invalid password');
        }
        catch (UserAlreadyExistsException $e) {
            Flash::error('<strong>Уведомление!</strong> Этот эл. адрес уже занят другим пользователем.');
            header('Location: /user/add');
            exit();
        }
    }

    public function logout() {
        $this->auth->logOut();
        header('Location: /login');
        exit();
    }

}

