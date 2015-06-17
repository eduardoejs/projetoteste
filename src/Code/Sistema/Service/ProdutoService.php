<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 27/11/14
 * Time: 21:43
 */

namespace Code\Sistema\Service;

use Code\Sistema\Entity\Interfaces\ProdutoInterface;
use Code\Sistema\Entity\Produto;
use Doctrine\ORM\EntityManager;
use \Code\Sistema\Service\Interfaces\ProdutoServiceInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ProdutoService implements ProdutoServiceInterface
{

        private $produto;
        private $em;

        public function __construct(ProdutoInterface $produto, EntityManager $em)
        {
            $this->produto = $produto;
            $this->em = $em;
        }

        public function insert(array $data)
        {
            try{
                $this->produto->setNome($data['nome']);
                $this->produto->setDescricao($data['descricao']);
                if($data['valor'] < 0)
                    throw new InvalidArgumentException('O valor não pode ser negativo ');

                $this->produto->setValor($data['valor']);

                if($data['categoria'] == null)
                    throw new InvalidArgumentException("A categoria é obrigatória!");

                $categoria = $this->em->getReference("Code\Sistema\Entity\Categoria", $data['categoria']);
                $this->produto->setCategoria($categoria);

                if(count($data['tags']) > 0){
                    foreach($data['tags'] as $tag){
                        $entityTag = $this->em->getReference("Code\Sistema\Entity\Tag", $tag);
                        $this->produto->addTag($entityTag);
                    }
                }
                $this->produto->setFile($data['file']);

                $this->em->persist($this->produto);
                $this->em->flush();

                return $this->produto;
            }catch (\Exception $e){
                die('Message: '. $e->getMessage());
            }
        }

        public function update(array $data)
        {
            try{
                $this->produto = $this->em->getReference('Code\Sistema\Entity\Produto', $data['id']);

                $this->produto->setNome($data['nome']);
                $this->produto->setDescricao($data['descricao']);
                if($data['valor'] < 0)
                    throw new InvalidArgumentException('O valor não pode ser negativo ');

                $this->produto->setValor($data['valor']);

                if($data['categoria'] == null)
                    throw new InvalidArgumentException("A categoria é obrigatória!");

                $categoria = $this->em->getReference("Code\Sistema\Entity\Categoria", $data['categoria']);
                $this->produto->setCategoria($categoria);

                $produtoRepository = $this->em->getRepository("Code\Sistema\Entity\Produto", $this->produto->getId());
                $produtoRepository->removeAssociationTag($this->produto->getId());

                if(count($data['tags']) > 0){
                    foreach($data['tags'] as $tag){
                        $entityTag = $this->em->getReference("Code\Sistema\Entity\Tag", $tag);
                        $this->produto->addTag($entityTag);
                    }
                }
                
                if($data['file'] != null){
                    $produtoAntes = $produtoRepository->find($this->produto->getId());
                    self::removeImage($produtoAntes);
                    $this->produto->setFile($data['file']);
                }

                $this->em->persist($this->produto);
                $this->em->flush();

                return $this->produto;
            }catch (\Exception $e){
                die('Message: '. $e->getMessage());
            }
        }

        public function delete($id)
        {
            $this->produto = $this->em->getReference('Code\Sistema\Entity\Produto', $id);

            $this->em->remove($this->produto);
            $this->em->flush();

            return true;
        }

        public function findAll()
        {
            $repository = $this->em->getRepository('Code\Sistema\Entity\Produto');
            return $this->toArray($repository->findAll());
        }

        public function getProdutos($page, $limitRegs)
        {
            $inicial = ($page > 1) ? ($page - 1) * $limitRegs : 0;

            $repository = $this->em->getRepository('Code\Sistema\Entity\Produto');
            return $this->toArray($repository->listProdutos($inicial, $limitRegs));
        }

        public function search($page, $limitRegs, $search)
        {
            $inicial = ($page > 1) ? ($page - 1) * $limitRegs : 0;

            $repository = $this->em->getRepository('Code\Sistema\Entity\Produto');
            return $this->toArray($repository->search($inicial, $limitRegs, $search));
        }

        public function findById($id)
        {
            try
            {
                $repository = $this->em->getRepository('Code\Sistema\Entity\Produto');
                $produto = $repository->find($id);
                if(!$produto)
                    throw new InvalidArgumentException("Produto nao existe!");

                return $this->getData($produto);
            }
            catch(\Exception $e)
            {
                return array(
                    'message', $e->getMessage(),
                    'id' => 0,
                    'nome' => '',
                    'descricao' => '',
                    'valor' => '',
                    'path' => ''
                );
            }
        }

        private function toArray(array $arrayObject)
        {
            $newArray = array();
            foreach($arrayObject as $key => $object){
                $newArray[$key]['id'] = $object->getId();
                $newArray[$key]['nome'] = $object->getNome();
                $newArray[$key]['descricao'] = $object->getDescricao();
                $newArray[$key]['valor'] = $object->getValor();
                $newArray[$key]['path'] = $object->getPath();
                if($object->getCategoria()){
                    $newArray[$key]['categoria']['id'] = $object->getCategoria()->getId();
                    $newArray[$key]['categoria']['nome'] = $object->getCategoria()->getNome();
                }else{
                    $newArray[$key]['categoria']['id'] = null;
                    $newArray[$key]['categoria']['nome'] = null;
                }
                foreach($object->getTags() as $k => $tag){
                    $newArray[$key]['tags'][$k]['id'] = $tag->getId();
                    $newArray[$key]['tags'][$k]['nome'] = $tag->getNome();
                }
            }

            return $newArray;
        }

        public function getNumProdutos()
        {
            $repository = $this->em->getRepository('Code\Sistema\Entity\Produto');
            return count($repository->findAll());
        }

        public function getNumProdutosSearch($search)
        {
            $repository = $this->em->getRepository('Code\Sistema\Entity\Produto');
            return count($repository->findAllSearch($search));
        }

        private function getData(Produto $produto)
        {
            $arrayProduto['id'] = $produto->getId();
            $arrayProduto['nome'] = $produto->getNome();
            $arrayProduto['descricao'] = $produto->getDescricao();
            $arrayProduto['valor'] = $produto->getValor();
            $arrayProduto['path'] = $produto->getPath();
            if($produto->getCategoria()){
                $arrayProduto['categoria']['id'] = $produto->getCategoria()->getId();
                $arrayProduto['categoria']['nome'] = $produto->getCategoria()->getNome();
            }else{
                $arrayProduto['categoria']['id'] = null;
                $arrayProduto['categoria']['nome'] = null;
            }
            if(count($produto->getTags()) > 0){
                foreach($produto->getTags() as $k => $tag){
                    $arrayProduto['tags'][$k]['id'] = $tag->getId();
                    $arrayProduto['tags'][$k]['nome'] = $tag->getNome();
                }
            }else{
                $arrayProduto['tags'] = null;
            }
            return $arrayProduto;
        }


        static public function uploadImage(Produto $produto)
        {
            if (null === $produto->getFile()) {
                return;
            }

            if(!in_array($produto->getFile()->getClientOriginalExtension(), $produto->getUploadAcceptedTypes()))
                throw new InvalidArgumentException("Tipo de arquivo não permitido");

            $filename = sha1($produto->getFile()->getClientOriginalName() . date('Y-m-d H:i:s')) . '.' . $produto->getFile()->getClientOriginalExtension();

            $produto->getFile()->move(
                $produto->getUploadRootDir(),
                $filename
            );

            return $filename;

        }

        static public function removeImage(Produto $produto)
        {
            if (null === $produto->getPath()) {
                return;
            }

            if(file_exists($produto->getAbsolutePath()))
                unlink($produto->getAbsolutePath());

            return true;
        }

} 