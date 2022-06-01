<?php

namespace App\controllers;

use App\classes\QueryBuilder;
use Delight\Auth\Auth;
use Delight\Auth\Role;
use League\Plates\Engine;
use Tamtamchik\SimpleFlash\Flash;

class UserController
{
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

    }

    public function add() {
        echo $this->template->render('add-user');
    }

    public function edit($id) {

        $logged_id = $this->auth->getUserId();
        $admin = $this->auth->hasRole(Role::ADMIN);

        if($logged_id != $id || $admin) {
            header('Location: /');
            exit();
        }

        $user = $this->queryBuilder->getOne('users', $id);

        if(!$user) {
            echo $this->template->render('404');
            exit();
        }

        $data = [
            'user' => $user,
            'id' => $id
        ];

        echo $this->template->render('edit-user', $data);
    }

    public function show($id) {
        $user = $this->queryBuilder->getOne('users', $id);

        if(!$user) {
            echo $this->template->render('404');
            exit();
        }

        $data = [
            'user' => $user,
            'id' => $id
        ];

        echo $this->template->render('show-user', $data);
    }

    public function status($id) {
        $user = $this->queryBuilder->getOne('users', $id);

        if(!$user) {
            echo $this->template->render('404');
            exit();
        }

        $statuses = [
            [
                'value' => 1,
                'title' => 'Онлайн'
            ],
            [
                'value' => 2,
                'title' => 'Отошел'
            ],
            [
                'value' => 3,
                'title' => 'Не беспокоить'
            ],
        ];

        $data = [
            'user' => $user,
            'statuses' => $statuses,
            'id' => $id
        ];

        echo $this->template->render('status-user', $data);
    }

    public function security($id) {
        $user = $this->queryBuilder->getOne('users', $id);

        if(!$user) {
            echo $this->template->render('404');
            exit();
        }

        $data = [
            'user' => $user,
            'id' => $id
        ];
        echo $this->template->render('security', $data);
    }

    public function editSecurity() {

        d($_POST);
    }

    public function createUserInfo() {
        $data = [
            'fullname' => '',
            'phone' => '',
            'adress' => '',
            'workplace' => '',
            'avatar' => '',
            'available_status' => 0,
            'vkontakte' => '',
            'telegram' => '',
            'instagram' => '',

        ];
        $this->queryBuilder->insert($data, 'users');
    }

    public function editUser() {
        self::editInfo($_POST['fullname'], $_POST['workplace'], $_POST['phone'], $_POST['adress'], $_POST['id']);
        Flash::success('<strong>Уведомление!</strong> Данные успешно обновлены');
        header('Location: /user/edit/' . $_POST['id']);
    }

    public function editInfo($fullname, $workplace, $phone, $adress, $user_id) {
        $data = [
            'fullname' => $fullname,
            'workplace' => $workplace,
            'phone' => $phone,
            'adress' => $adress,
        ];
        $this->queryBuilder->update($data, 'users', $user_id);
    }

    public function editSocialLinks($vk, $tg, $inst, $user_id) {
        $data = [
            'vkontakte' => $vk,
            'telegram' => $tg,
            'instagram' => $inst,
        ];
        $this->queryBuilder->update($data, 'users', $user_id);
    }

    public function setUserStatus() {
        $value = $_POST['available_status'];
        $id = $_POST['id'];

        $data = [
          'available_status' => $value,
          'id' => $id
        ];

        $this->queryBuilder->update($data, 'users', $id);
        Flash::success('<strong>Уведомление!</strong> Статус успешно обновлен');
        header('Location: /user/status/' .$id);
        exit();
    }

    public function uploadAvatar($image, $user_id) {

    }



}