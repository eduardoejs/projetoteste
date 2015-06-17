<?php

namespace Code\Sistema\Controller;


use Doctrine\ORM\EntityManager;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Code\Sistema\Service\CategoriaService;
use Code\Sistema\Entity\Categoria;
use Symfony\Component\HttpFoundation\Request;

class CategoriaController implements ControllerProviderInterface
{

    private $service;
    private $categoria;

    public function __construct(Application $app, EntityManager $em)
    {
        $this->categoria = new Categoria();
        $this->service = new CategoriaService($this->categoria, $em);
    }

    public function connect(Application $app)
    {
        $categoriaController = $app['controllers_factory'];

        $categoriaController->get("/", function() use ($app){ return $this->indexAction($app); } )->bind( 'categorias' );

        $categoriaController->match("/novo", function(Request $request) use ($app){ return $this->createAction($app, $request); } )->bind( 'nova-categoria' )->method('GET|POST');

        $categoriaController->match("/editar/{id}", function(Request $request, $id) use ($app){ return $this->updateAction($app, $request, $id); } )->bind( 'editar-categoria' )->method('GET|PUT');

        $categoriaController->match("/remover/{id}", function(Request $request, $id) use ($app){ return $this->deleteAction($app, $request, $id); } )->bind( 'remover-categoria' )->method('GET|DELETE');

        return $categoriaController;
    }


    public function indexAction(Application $app)
    {
        $categorias = $this->service->findAll();

        return $app['twig']->render('categoria/index.twig', array(
            'categorias' => $categorias,
        ));
    }

    public function createAction(Application $app, Request $request)
    {
        if($request->isMethod('POST')){
            $data['nome'] = $request->get('nome');
            $this->service->insert($data);

            return $app->redirect('/categorias');
        }

        return $app['twig']->render('categoria/novo.twig', array(

        ));
    }

    public function updateAction(Application $app, Request $request, $id)
    {
        if($request->isMethod('PUT')){
            $data['id'] = $id;
            $data['nome'] = $request->get('nome');
            $this->service->update($data);

            return $app->redirect('/categorias');
        }

        $categoria = $this->service->findById($id);
        return $app['twig']->render('categoria/editar.twig', array(
            'categoria' => $categoria
        ));
    }

    public function deleteAction(Application $app, Request $request, $id)
    {
        if($request->isMethod('DELETE')){
            $this->service->delete($id);

            return $app->redirect('/categorias');
        }

        $categoria = $this->service->findById($id);
        return $app['twig']->render('categoria/remover.twig', array(
            'categoria' => $categoria
        ));
    }


} 