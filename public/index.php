<?php
if( !session_id() ) @session_start();
require '../vendor/autoload.php';

use DI\ContainerBuilder;
use League\Plates\Engine;
use Aura\SqlQuery\QueryFactory;
use Delight\Auth\Auth;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    Engine::class => function() {
        return new Engine('../app/views');
    },

    QueryFactory::class => function() {
        return new QueryFactory('mysql');
    },

    PDO::class => function() {
        return new PDO(
            'mysql:host=127.0.0.1;dbname=level3;charset=utf8', 'root', ''
        );
    },

    Auth::class => function($containerBuilder) {
        return new Auth($containerBuilder->get('PDO'));
    }
]);
$container = $containerBuilder->build();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', ['App\controllers\FrontController', 'index']);
    $r->addRoute('GET', '/users', ['App\controllers\FrontController', 'users']);
    $r->addRoute('GET', '/login', ['App\controllers\AccountController', 'login']);
    $r->addRoute('GET', '/register', ['App\controllers\AccountController', 'register']);

    $r->addRoute('POST', '/auth', ['App\controllers\AccountController', 'auth']);
    $r->addRoute('POST', '/registration', ['App\controllers\AccountController', 'registration']);

    $r->addRoute('GET', '/logout', ['App\controllers\AccountController', 'logout']);

    $r->addRoute('GET', '/user/add', ['App\controllers\UserController', 'add']);
    $r->addRoute('GET', '/user/edit/{id:\d+}', ['App\controllers\UserController', 'edit']);
    $r->addRoute('GET', '/user/show/{id:\d+}', ['App\controllers\UserController', 'show']);
    $r->addRoute('GET', '/user/security/{id:\d+}', ['App\controllers\UserController', 'security']);

    $r->addRoute('GET', '/user/status/{id:\d+}', ['App\controllers\UserController', 'status']);

    //handlers
    $r->addRoute('POST', '/user/create', ['App\controllers\AccountController', 'create']);
    $r->addRoute('POST', '/user/edituser', ['App\controllers\UserController', 'edituser']);
    $r->addRoute('POST', '/user/setstatus', ['App\controllers\UserController', 'setuserstatus']);
    $r->addRoute('POST', '/user/editsecurity', ['App\controllers\UserController', 'editsecurity']);

});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $container->call($handler, $vars);
//        $controller = new $handler[0];
//        call_user_func([$controller, $handler[1]], $vars);
        break;
}