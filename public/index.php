<?php

//Inclui o arquivo de configuração
require_once __DIR__ . "/../bootstrap.php";

use Code\Sistema\Config\Routes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Cria rota para a index
$app->get('/', function () use ($app) {

    $repo = $app['user_provider'];
    $user = $repo->loadUserByUsername( $app['security']->getToken()->getUser());
    $roles = $user->getRoles();
    
    return $app['twig']->render('home/index.twig', array(
        'username' => $app['security']->getToken()->getUser(),
        'role' => $roles[0]
    ));

})->bind('home');


$app->get('/criaAdmin', function() use($app){
    $repo = $app['user_repository'];
    $repo->createAdminUser('admin', 'admin');
    return $app->redirect($app['url_generator']->generate('home'));
    
});
$app->get('/criaUser', function() use($app){
    $repo = $app['user_repository'];
    $repo->createUser('user', 'user');
    return $app->redirect($app['url_generator']->generate('home'));
    
});

$app->get('/login', function(Request $request) use($app){
    return $app['twig']->render('/home/login.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username')
    ));
    
})->bind('login');

$routes = new Routes();
$routes->init($app, $em);


Request::enableHttpMethodParameterOverride();
//Roda a aplicação
$app->run();