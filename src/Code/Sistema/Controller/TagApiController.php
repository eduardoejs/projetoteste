<?php

namespace Code\Sistema\Controller;


use Doctrine\ORM\EntityManager;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Code\Sistema\Service\TagService;
use Code\Sistema\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;

class TagApiController implements ControllerProviderInterface
{

    private $service;
    private $tag;

    public function __construct(Application $app, EntityManager $em)
    {
        $this->tag = new Tag();
        $this->service = new TagService($this->tag, $em);
    }

    public function connect(Application $app)
    {
        $tagController = $app['controllers_factory'];

        $tagController->get("/", function() use ($app){ return $this->indexAction($app); } );

        $tagController->get("/{id}", function($id) use ($app){ return $this->getAction($app, $id); } );

        $tagController->post("/", function(Request $request) use ($app){ return $this->createAction($app, $request); } );

        $tagController->put("/{id}", function(Request $request, $id) use ($app){ return $this->updateAction($app, $request, $id); } );

        $tagController->delete("/{id}", function(Request $request, $id) use ($app){ return $this->deleteAction($app, $request, $id); } );

        return $tagController;
    }


    public function indexAction(Application $app)
    {
        $tags = $this->service->findAll();

        return $app->json($tags);
    }

    public function getAction(Application $app, $id)
    {
        $tag = $this->service->findById($id);

        return $app->json($tag);
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