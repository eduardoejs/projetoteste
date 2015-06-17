<?php

namespace Code\Sistema\Config;

use Doctrine\ORM\EntityManager;
use Silex\Application;

class Routes
{

    public function init(Application $app, EntityManager $em)
    {
        $app->mount( '/produtos', new \Code\Sistema\Controller\ProdutoController($app, $em) );
        $app->mount( '/api/produtos', new \Code\Sistema\Controller\ProdutoApiController($app, $em) );

        $app->mount( '/categorias', new \Code\Sistema\Controller\CategoriaController($app, $em) );
        $app->mount( '/api/categorias', new \Code\Sistema\Controller\CategoriaApiController($app, $em) );

        $app->mount( '/tags', new \Code\Sistema\Controller\TagController($app, $em) );
        $app->mount( '/api/tags', new \Code\Sistema\Controller\TagApiController($app, $em) );
    }


}

