<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 27/11/14
 * Time: 20:19
 */

namespace Code\Sistema\Service\Interfaces;

use \Code\Sistema\Entity\Interfaces\ProdutoInterface;
use \Doctrine\ORM\EntityManager;

interface ProdutoServiceInterface
{
    public function __construct(ProdutoInterface $produto, EntityManager $em);
    public function insert(array $data);
    public function update(array $data);
    public function delete($id);
    public function findAll();
    public function getProdutos($page, $limit);
    public function search($page, $limit, $search);
    public function findById($id);
    public function getNumProdutos();
} 