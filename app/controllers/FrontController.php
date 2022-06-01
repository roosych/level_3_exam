<?php

namespace App\controllers;

use App\classes\QueryBuilder;
use Delight\Auth\Auth;
use Delight\Auth\Role;
use League\Plates\Engine;

class FrontController {

    private $template;
    private $queryBuilder;
    private $auth;

    public function __construct(Engine $template, QueryBuilder $queryBuilder, Auth $auth)
    {
        $this->template = $template;
        $this->queryBuilder = $queryBuilder;
        $this->auth = $auth;
    }

    public function index() {
        if(!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit();
        }
        header('Location: /users');
    }

    public function users() {
        if(!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit();
        }

        $admin = $this->auth->hasRole(Role::ADMIN);
        $current_user = $this->auth->id();
        $users = $this->queryBuilder->getAll('users');

        $data = [
            'users' => $users,
            'admin' => $admin,
            'current_user' => $current_user
        ];

        echo $this->template->render('users', $data);
    }

}