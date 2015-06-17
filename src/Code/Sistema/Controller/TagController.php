<?php

namespace Code\Sistema\Controller;


use Doctrine\ORM\EntityManager;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Code\Sistema\Service\TagService;
use Code\Sistema\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;

class TagController implements ControllerProviderInterface
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

        $tagController->get("/", function() use ($app){ return $this->indexAction($app); } )->bind( 'tags' );

        $tagController->match("/novo", function(Request $request) use ($app){ return $this->createAction($app, $request); } )->bind( 'nova-tag' )->method('GET|POST');

        $tagController->match("/editar/{id}", function(Request $request, $id) use ($app){ return $this->updateAction($app, $request, $id); } )->bind( 'editar-tag' )->method('GET|PUT');

        $tagController->match("/remover/{id}", function(Request $request, $id) use ($app){ return $this->deleteAction($app, $request, $id); } )->bind( 'remover-tag' )->method('GET|DELETE');

        return $tagController;
    }


    public function indexAction(Application $app)
    {
        $tags = $this->service->findAll();

        return $app['twig']->render('tag/index.twig', array(
            'tags' => $tags,
        ));
    }

    public function createAction(Application $app, Request $request)
    {
        if($request->isMethod('POST')){
            $data['nome'] = $request->get('nome');
            $this->service->insert($data);

            return $app->redirect('/tags');
        }

        return $app['twig']->render('tag/novo.twig', array(

        ));
    }

    public function updateAction(Application $app, Request $request, $id)
    {
        if($request->isMethod('PUT')){
            $data['id'] = $id;
            $data['nome'] = $request->get('nome');
            $this->service->update($data);

            return $app->redirect('/tags');
        }

        $tag = $this->service->findById($id);
        return $app['twig']->render('tag/editar.twig', array(
            'tag' => $tag
        ));
    }

    public function deleteAction(Application $app, Request $request, $id)
    {
        if($request->isMethod('DELETE')){
            $this->service->delete($id);

            return $app->redirect('/tags');
        }

        $tag = $this->service->findById($id);
        return $app['twig']->render('tag/remover.twig', array(
            'tag' => $tag
        ));
    }


} 