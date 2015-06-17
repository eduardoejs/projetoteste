<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 27/11/14
 * Time: 21:43
 */

namespace Code\Sistema\Service;

use Code\Sistema\Entity\Categoria;
use Code\Sistema\Service\Interfaces\CategoriaServiceInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

class CategoriaService implements CategoriaServiceInterface
{

        private $categoria;
        private $em;

        public function __construct(Categoria $categoria, EntityManager $em)
        {
            $this->categoria = $categoria;
            $this->em = $em;
        }

        public function insert(array $data)
        {
            $this->categoria->setNome($data['nome']);

            $this->em->persist($this->categoria);
            $this->em->flush();

            return $this->categoria;
        }

        public function update(array $data)
        {
            $this->categoria = $this->em->getReference('Code\Sistema\Entity\Categoria', $data['id']);

            $this->categoria->setNome($data['nome']);

            $this->em->persist($this->categoria);
            $this->em->flush();

            return $this->categoria;
        }

        public function delete($id)
        {
            try{
                $this->categoria = $this->em->getReference('Code\Sistema\Entity\Categoria', $id);

                $this->em->remove($this->categoria);
                $this->em->flush();

                return true;
            }catch (\Exception $e){
                die('A categoria deve estar vazia para ser removida!');
            }
        }

        public function findAll()
        {
            $repository = $this->em->getRepository('Code\Sistema\Entity\Categoria');
            return $this->toArray($repository->findAll());
        }

        public function findById($id)
        {
            try
            {
                $repository = $this->em->getRepository('Code\Sistema\Entity\Categoria');
                $categoria = $repository->find($id);
                if(!$categoria)
                    throw new InvalidArgumentException("Categoria nao existe!");

                return $this->getData($categoria);
            }
            catch(\Exception $e)
            {
                return array(
                    'message', $e->getMessage(),
                    'id' => 0,
                    'nome' => '',
                );
            }
        }

        private function toArray(array $arrayObject)
        {
            $newArray = array();
            foreach($arrayObject as $key => $object){
                $newArray[$key]['id'] = $object->getId();
                $newArray[$key]['nome'] = $object->getNome();
            }

            return $newArray;
        }

        private function getData(Categoria $categoria)
        {
            $arrayCategoria['id'] = $categoria->getId();
            $arrayCategoria['nome'] = $categoria->getNome();

            return $arrayCategoria;
        }

} 