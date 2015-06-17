<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 27/11/14
 * Time: 20:19
 */

namespace Code\Sistema\Service\Interfaces;

use \Code\Sistema\Entity\Tag;
use \Doctrine\ORM\EntityManager;

interface TagServiceInterface
{
    public function __construct(Tag $tag, EntityManager $em);
    public function insert(array $data);
    public function update(array $data);
    public function delete($id);
    public function findAll();
    public function findById($id);
} 