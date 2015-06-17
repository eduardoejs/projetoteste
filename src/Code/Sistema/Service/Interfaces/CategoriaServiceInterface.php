<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 27/11/14
 * Time: 20:19
 */

namespace Code\Sistema\Service\Interfaces;

use \Code\Sistema\Entity\Categoria;
use \Doctrine\ORM\EntityManager;

interface CategoriaServiceInterface
{
    public function __construct(Categoria $categoria, EntityManager $em);
    public function insert(array $data);
    public function update(array $data);
    public function delete($id);
    public function findAll();
    public function findById($id);
} 