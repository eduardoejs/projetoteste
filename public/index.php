<?php

//Inclui o arquivo de configuração
require_once __DIR__ . "/../bootstrap.php";

use Code\Sistema\Config\Routes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



//Cria rota para a index
$app->get('/', function () use ($app) {

    return $app['twig']->render('home/index.twig', array(

    ));

})->bind('home');


$routes = new Routes();
$routes->init($app, $em);


Request::enableHttpMethodParameterOverride();
//Roda a aplicação
$app->run();