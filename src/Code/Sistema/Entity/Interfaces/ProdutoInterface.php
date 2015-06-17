<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 27/11/14
 * Time: 20:12
 */

namespace Code\Sistema\Entity\Interfaces;

use Symfony\Component\HttpFoundation\File\UploadedFile;


interface ProdutoInterface
{
    public function setId($id);
    public function getId();

    public function setNome($nome);
    public function getNome();

    public function setDescricao($descricao);
    public function getDescricao();

    public function setValor($valor);
    public function getValor();

    public function createPath();
    public function removePath();
    public function getPath();

    public function setFile(UploadedFile $file);
    public function getFile();

    //public function getAbsolutePath();
    //public function getWebPath();
}