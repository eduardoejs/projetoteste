<?php

namespace Code\Sistema\Controller;


use Code\Sistema\Entity\Categoria;
use Code\Sistema\Entity\Tag;
use Code\Sistema\Service\CategoriaService;
use Code\Sistema\Service\TagService;
use Doctrine\ORM\EntityManager;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Code\Sistema\Service\ProdutoService;
use Code\Sistema\Entity\Produto;
use Symfony\Component\HttpFoundation\Request;

class ProdutoController implements ControllerProviderInterface
{

    private $service;
    private $produto;

    private $tag_service;
    private $categoria_service;

    public function __construct(Application $app, EntityManager $em)
    {
        $this->produto = new Produto();
        $this->service = new ProdutoService($this->produto, $em);

        $this->categoria_service = new CategoriaService(new Categoria(), $em);
        $this->tag_service = new TagService(new Tag(), $em);
    }

    public function connect(Application $app)
    {
        $produtoController = $app['controllers_factory'];

        $produtoController->get("/listar/{page}", function($page) use ($app){ return $this->indexAction($app, $page); } )->bind( 'produtos' )->value('page', '');

        $produtoController->get("/buscar/{page}", function(Request $request, $page) use ($app){ return $this->searchAction($app, $request, $page); } )->bind( 'buscar-produtos' )->value('page', '');

        $produtoController->match("/novo", function(Request $request) use ($app){ return $this->createAction($app, $request); } )->bind( 'novo-produto' )->method('GET|POST');

        $produtoController->match("/editar/{id}", function(Request $request, $id) use ($app){ return $this->updateAction($app, $request, $id); } )->bind( 'editar-produto' )->method('GET|PUT');

        $produtoController->match("/remover/{id}", function(Request $request, $id) use ($app){ return $this->deleteAction($app, $request, $id); } )->bind( 'remover-produto' )->method('GET|DELETE');

        return $produtoController;
    }


    public function indexAction(Application $app, $page)
    {
        //Limite de registros
        $limitRegs = 10;

        $numProdutos = $this->service->getNumProdutos();

        $paginator = new \Code\Sistema\Helper\Paginator($page, $limitRegs, $numProdutos, $app['url_generator']->generate('produtos'));

        $produtos = $this->service->getProdutos($page, $limitRegs);

        return $app['twig']->render('produtos/index.twig', array(
            'produtos' => $produtos,
            'paginacao' => $paginator->createLinks(),
        ));
    }

    public function searchAction(Application $app, $request, $page)
    {
        $search = $request->get('search');

        //Limite de registros
        $limitRegs = 2;

        $numProdutos = $this->service->getNumProdutosSearch($search);

        $paginator = new \Code\Sistema\Helper\Paginator($page, $limitRegs, $numProdutos, $app['url_generator']->generate('buscar-produtos'), array('search' => $search));

        $produtos = $this->service->search($page, $limitRegs, $search);

        return $app['twig']->render('produtos/index.twig', array(
            'produtos' => $produtos,
            'paginacao' => $paginator->createLinks(),
            'search' => $search
        ));
    }

    public function createAction(Application $app, Request $request)
    {
        if($request->isMethod('POST')){
            $data['nome'] = $request->get('nome');
            $data['descricao'] = $request->get('descricao');
            $data['valor'] = $request->get('valor');
            $data['categoria'] = $request->get('categoria');
            $data['tags'] = $request->get('tag');
            $data['file'] = $request->files->get('path');
            $this->service->insert($data);

            return $app->redirect('/produtos/listar');
        }

        return $app['twig']->render('produtos/novo.twig', array(
            'categorias' => $this->categoria_service->findAll(),
            'tags' => $this->tag_service->findAll()
        ));
    }

    public function updateAction(Application $app, Request $request, $id)
    {
        if($request->isMethod('PUT')){
            $data['id'] = $id;
            $data['nome'] = $request->get('nome');
            $data['descricao'] = $request->get('descricao');
            $data['valor'] = $request->get('valor');
            $data['categoria'] = $request->get('categoria');
            $data['tags'] = $request->get('tag');
            $data['file'] = $request->files->get('path');
            $this->service->update($data);

            return $app->redirect('/produtos/listar');
        }

        $produto = $this->service->findById($id);
        return $app['twig']->render('produtos/editar.twig', array(
            'produto' => $produto,
            'categorias' => $this->categoria_service->findAll(),
            'tags' => $this->tag_service->findAll()
        ));
    }

    public function deleteAction(Application $app, Request $request, $id)
    {
        if($request->isMethod('DELETE')){
            $this->service->delete($id);

            return $app->redirect('/produtos/listar');
        }

        $produto = $this->service->findById($id);
        return $app['twig']->render('produtos/remover.twig', array(
            'produto' => $produto,
            'categorias' => $this->categoria_service->findAll(),
            'tags' => $this->tag_service->findAll()
        ));
    }


} 