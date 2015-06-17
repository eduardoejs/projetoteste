<?php

namespace Code\Sistema\Controller;


use Doctrine\ORM\EntityManager;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Code\Sistema\Service\CategoriaService;
use Code\Sistema\Entity\Categoria;
use Symfony\Component\HttpFoundation\Request;

class CategoriaApiController implements ControllerProviderInterface
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

        $categoriaController->get("/", function() use ($app){ return $this->indexAction($app); } );

        $categoriaController->get("/{id}", function($id) use ($app){ return $this->getAction($app, $id); } );

        $categoriaController->post("/", function(Request $request) use ($app){ return $this->createAction($app, $request); } );

        $categoriaController->put("/{id}", function(Request $request, $id) use ($app){ return $this->updateAction($app, $request, $id); } );

        $categoriaController->delete("/{id}", function(Request $request, $id) use ($app){ return $this->deleteAction($app, $request, $id); } );

        return $categoriaController;
    }


    public function indexAction(Application $app)
    {
        $categorias = $this->service->findAll();

        return $app->json($categorias);
    }

    public function getAction(Application $app, $id)
    {
        $categoria = $this->service->findById($id);

        return $app->json($categoria);
    }

    public function createAction(Application $app, Request $request)
    {
        $data['nome'] = $request->get('nome');

        if($this->service->insert($data)){
            return $app->json(['success'=>true, 'messages' => ['Inserido com sucesso']]);
        }

        $errors = [
            'success' => false,
            'messages' => [
                'Não foi possível efetuar o cadastro',
            ],
        ];
        return $app->json($errors);
    }

    public function updateAction(Application $app, Request $request, $id)
    {
        $data['id'] = $id;
        $data['nome'] = $request->get('nome');

        if($this->service->update($data)){
            return $app->json(['success'=>true, 'messages' => ['Alterado com sucesso']]);
        }

        $errors = [
            'success' => false,
            'messages' => [
                'Não foi possível alterar o cadastro',
            ],
        ];
        return $app->json($errors);
    }

    public function deleteAction(Application $app, Request $request, $id)
    {
        if($this->service->delete($id)){
            return $app->json(['success'=>true, 'messages' => ['Removido com sucesso']]);
        }

        $errors = [
            'success' => false,
            'messages' => [
                'Não foi possível remover o cadastro',
            ],
        ];
        return $app->json($errors);
    }


} 