<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 27/11/14
 * Time: 21:43
 */

namespace Code\Sistema\Service;

use Code\Sistema\Entity\Tag;
use Code\Sistema\Service\Interfaces\TagServiceInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

class TagService implements TagServiceInterface
{

        private $tag;
        private $em;

        public function __construct(Tag $tag, EntityManager $em)
        {
            $this->tag = $tag;
            $this->em = $em;
        }

        public function insert(array $data)
        {
            $this->tag->setNome($data['nome']);

            $this->em->persist($this->tag);
            $this->em->flush();

            return $this->tag;
        }

        public function update(array $data)
        {
            $this->tag = $this->em->getReference('Code\Sistema\Entity\Tag', $data['id']);

            $this->tag->setNome($data['nome']);

            $this->em->persist($this->tag);
            $this->em->flush();

            return $this->tag;
        }

        public function delete($id)
        {
            $this->tag = $this->em->getReference('Code\Sistema\Entity\Tag', $id);

            $this->em->remove($this->tag);
            $this->em->flush();

            return true;
        }

        public function findAll()
        {
            $repository = $this->em->getRepository('Code\Sistema\Entity\Tag');
            return $this->toArray($repository->findAll());
        }

        public function findById($id)
        {
            try
            {
                $repository = $this->em->getRepository('Code\Sistema\Entity\Tag');
                $tag = $repository->find($id);
                if(!$tag)
                    throw new InvalidArgumentException("Tag nao existe!");

                return $this->getData($tag);
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

        private function getData(Tag $tag)
        {
            $arrayTag['id'] = $tag->getId();
            $arrayTag['nome'] = $tag->getNome();

            return $arrayTag;
        }

} 