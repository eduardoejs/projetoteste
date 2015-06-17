<?php

namespace Code\Sistema\Entity;

use \Code\Sistema\Entity\Interfaces\ProdutoInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Code\Sistema\Entity\Categoria;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Code\Sistema\Service\ProdutoService;


/**
 * @ORM\Entity(repositoryClass="Code\Sistema\Entity\ProdutoRepository")
 * @ORM\Table(name="produtos")
 * @ORM\HasLifecycleCallbacks
 */

class Produto implements ProdutoInterface
{
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue
         */
        private $id;

        /**
         * @ORM\Column(type="string", length=255)
         */
        private $nome;

        /**
         * @ORM\Column(type="string", length=255)
         */
        private $descricao;

        /**
         * @ORM\Column(type="float")
         */
        private $valor;

        /**
         * @ORM\ManyToOne(targetEntity="Code\Sistema\Entity\Categoria")
         * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id", onDelete="SET NULL")
         */
        private $categoria;

        /**
         * @ORM\ManyToMany(targetEntity="Code\Sistema\Entity\Tag")
         * @ORM\JoinTable(name="produtos_tags",
         *      joinColumns={@ORM\JoinColumn(name="produto_id", referencedColumnName="id", onDelete="CASCADE")},
         *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")}
         *      )
         **/
        private $tags;

        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $path;


        private $file;

        /**
         * @ORM\PrePersist
         * @ORM\PreUpdate
         */
        public function createPath()
        {
            if($this->file != null)
                $this->path = ProdutoService::uploadImage($this);
        }

        /**
         * @ORM\PreRemove
         */
        public function removePath()
        {
            return ProdutoService::removeImage($this);
        }

        public function getPath()
        {
            return $this->path;
        }

        public function setFile(UploadedFile $file = null)
        {
            $this->file = $file;
        }

        /**
         * Get file.
         *
         * @return UploadedFile
         */
        public function getFile()
        {
            return $this->file;
        }

        public function __construct()
        {
            $this->tags = new ArrayCollection();
        }

        public function addTag($tags)
        {
            $this->tags->add($tags);
        }

        public function getTags()
        {
            return $this->tags;
        }

        public function setCategoria(Categoria $categoria)
        {
            $this->categoria = $categoria;
        }


        public function getCategoria()
        {
            return $this->categoria;
        }


        public function setId($id)
        {
            $this->id = $id;
            return $this;
        }

        public function getId()
        {
            return $this->id;
        }

        public function setDescricao($descricao)
        {
            $this->descricao = $descricao;
            return $this;
        }

        public function getDescricao()
        {
            return $this->descricao;
        }

        public function setNome($nome)
        {
            $this->nome = $nome;
            return $this;
        }

        public function getNome()
        {
            return $this->nome;
        }

        public function setValor($valor)
        {
            $this->valor = $valor;
            return $this;
        }

        public function getValor()
        {
            return $this->valor;
        }



        public function getAbsolutePath()
        {
            return null === $this->path
                ? null
                : $this->getUploadRootDir().'/'.$this->path;
        }


        public function getWebPath()
        {
            return null === $this->path
                ? null
                : $this->getUploadDir().'/'.$this->path;
        }

        public function getUploadRootDir()
        {
            return __DIR__.'/../../../../public/'.$this->getUploadDir();
        }

        public function getUploadDir()
        {
            return 'uploads/images';
        }

        public function getUploadAcceptedTypes()
        {
            return array('jpg', 'jpeg', 'png');
        }

} 