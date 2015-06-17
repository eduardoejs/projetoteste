<?php

namespace Code\Sistema\Controller;


use Doctrine\ORM\EntityManager;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Code\Sistema\Service\ProdutoService;
use Code\Sistema\Entity\Produto;
use Symfony\Component\HttpFoundation\Request;

class ProdutoApiController implements ControllerProviderInterface
{

    private $service;
    private $produto;

    public function __construct(Application $app, EntityManager $em)
    {
        $this->produto = new Produto();
        $this->service = new ProdutoService($this->produto, $em);
    }

    public function connect(Application $app)
    {
        $produtoController = $app['controllers_factory'];

        $produtoController->get("/", function() use ($app){ return $this->indexAction($app); } );

        $produtoController->get("/{id}", function($id) use ($app){ return $this->getAction($app, $id); } );

        $produtoController->post("/", function(Request $request) use ($app){ return $this->createAction($app, $request); } );

        $produtoController->put("/{id}", function(Request $request, $id) use ($app){ return $this->updateAction($app, $request, $id); } );

        $produtoController->delete("/{id}", function(Request $request, $id) use ($app){ return $this->deleteAction($app, $request, $id); } );

        return $produtoController;
    }


    public function indexAction(Application $app)
    {
        $produtos = $this->service->findAll();

        return $app->json($produtos);
    }

    public function getAction(Application $app, $id)
    {
        $produto = $this->service->findById($id);

        return $app->json($produto);
    }

    public function createAction(Application $app, Request $request)
    {
        $data['nome'] = $request->get('nome');
        $data['descricao'] = $request->get('descricao');
        $data['valor'] = $request->get('valor');
        $data['categoria'] = $request->get('categoria');
        $data['tags'] = explode(',', $request->get('tags'));
        $data['file'] = $request->files->get('path');

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
        $data['descricao'] = $request->get('descricao');
        $data['valor'] = $request->get('valor');
        $data['categoria'] = $request->get('categoria');
        $data['tags'] = explode(',', $request->get('tags'));
        $data['file'] = $request->files->get('path');

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