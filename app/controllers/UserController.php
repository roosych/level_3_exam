<?php

namespace App\controllers;

use App\classes\QueryBuilder;
use Delight\Auth\Auth;
use Delight\Auth\InvalidEmailException;
use Delight\Auth\EmailNotVerifiedException;
use Delight\Auth\InvalidPasswordException;
use Delight\Auth\Role;
use Delight\Auth\TooManyRequestsException;
use Delight\Auth\UserAlreadyExistsException;
use Delight\Auth\UnknownIdException;
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

        $admin = $this->auth->hasRole(Role::ADMIN);
        $current_user = $this->auth->id();
        $users = $this->queryBuilder->getAll('users');

        $data = [
            'users' => $users,
            'admin' => $admin,
            'current_user' => $current_user,
        ];

        echo $this->template->render('users', $data);
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
        catch (InvalidPasswordException $e) {
            Flash::error('Неверный логин или пароль');
            header('Location: /login');
            exit();
        }
        catch (EmailNotVerifiedException $e) {
            Flash::error('Ваш email не подтверждён');
            header('Location: /login');
            exit();
        }
        catch (TooManyRequestsException $e) {
            Flash::error('Попробуйте позже');
            header('Location: /login');
            exit();
        }
    }

    public function logout() {
        $this->auth->logOut();
        header('Location: /login');
        exit();
    }

    public function registration() {
        try {
            $this->auth->register($_POST['email'], $_POST['password']);
            Flash::success('<strong>Уведомление!</strong> Регистрация прошла успешно.');
            header('Location: /login');
            exit();
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
                $this->createUserInfo();
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

    public function add() {

        if(!$this->auth->hasRole(Role::ADMIN)) {
            header('Location: /');
            exit();
        }

        echo $this->template->render('add-user');
    }

    public function edit($id) {
        if($this->auth->getUserId() == $id || $this->auth->hasRole(Role::ADMIN)) {
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
        } else {
            header('Location: /');
            exit();
        }
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

        if($this->auth->getUserId() == $id || $this->auth->hasRole(Role::ADMIN)) {
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
        } else {
            header('Location: /');
            exit();
        }
    }

    public function security($id) {

        if($this->auth->getUserId() == $id || $this->auth->hasRole(Role::ADMIN)) {
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
        } else {
            header('Location: /');
            exit();
        }


    }

    public function editSecurity() {

        $id = $_POST['id'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user_by_email = $this->queryBuilder->getUserByEmail('users', $email);
        $user = $this->queryBuilder->getOne('users', $id);

        if($password !== '') {
            $this->editPassword($id, $password);
        }



        if($email == '') {
            Flash::error('<strong>Уведомление!</strong> Введите еmail');
            header('Location: /user/security/' .$id);
            exit();
        } else {
            if(!empty($user_by_email) && $email !== $user['email']) {
                Flash::error('<strong>Уведомление!</strong> Email существует');
                header('Location: /user/security/' .$id);
                exit();
            } else {
                $this->editEmail($id, $email);
                Flash::success('<strong>Уведомление!</strong> Email изменен');
                header('Location: /user/security/' .$id);
                exit();
            }
        }





    }

    public function editEmail($id, $email) {
        $data = [
            'email' => $email
        ];

        $this->queryBuilder->update($data, 'users', $id);
    }
    public function editPassword($id, $password) {
        $data = [
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $this->queryBuilder->update($data, 'users', $id);
    }


    public function deleteUser($id) {
        $user = $this->queryBuilder->getOne('users', $id);
        $filename = $user['avatar'];
        try {
            $this->auth->admin()->deleteUserById($id);
            unlink('uploads/' .$filename);
            Flash::success('<strong>Уведомление!</strong> Пользователь удален');
            header('Location: /users');
            exit();
        }
        catch (UnknownIdException $e) {
            header('Location: /');
            exit();
        }
    }

    public function media($id) {
        if($this->auth->getUserId() == $id || $this->auth->hasRole(Role::ADMIN)) {
            $user = $this->queryBuilder->getOne('users', $id);
            if(!$user) {
                echo $this->template->render('404');
                exit();
            }

            $data = [
                'user' => $user,
                'id' => $id
            ];
            echo $this->template->render('media', $data);
        } else {
            header('Location: /');
            exit();
        }


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
        exit();
    }

    public function editInfo() {
        $id = $_GET['id'];

        $data = [
            'fullname' => $_POST['fullname'],
            'workplace' => $_POST['workplace'],
            'phone' => $_POST['phone'],
            'adress' => $_POST['adress'],
        ];
        $this->queryBuilder->update($data, 'users', $id);
    }

    public function editSocialLinks() {
        $id = $_GET['id'];

        $data = [
            'vkontakte' => $_POST['vkontakte'],
            'telegram' => $_POST['telegram'],
            'instagram' => $_POST['instagram'],
        ];
        $this->queryBuilder->update($data, 'users', $id);
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

    public function uploadAvatar() {
        $image = $_FILES['avatar'];
        $id = $_POST['id'];

        if($image['size'] > 2048) {
            Flash::error('<strong>Уведомление!</strong> Файл не должен превышать 2мб');
            header('Location: /user/media/' .$id);
            exit();
        }

        if ($image['tmp_name'][0]){
            $pathinfo = pathinfo($image['name']);
            $tmp_name = $image['tmp_name'];
            $file_extension = $pathinfo['extension'];
            $filename = uniqid() .'.'. $file_extension;

            $data = [
                'avatar' => $filename,
                'id' => $id,
            ];

            $this->queryBuilder->update($data, 'users', $id);

            move_uploaded_file($tmp_name, 'uploads/' . $filename);

            Flash::success('<strong>Уведомление!</strong> Аватар профиля успешно обновлен');
            header('Location: /user/media/' .$id);
            exit();
        }

        Flash::error('<strong>Уведомление!</strong> Выберите файл');
        header('Location: /user/media/' .$id);
        exit();
    }
}