<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 08/01/15
 * Time: 11:47
 */

namespace Code\Sistema\Entity;


use Doctrine\ORM\EntityRepository;


class ProdutoRepository extends EntityRepository
{

    public function search($inicio, $limit, $busca)
    {
        return $this
            ->createQueryBuilder("p")
            ->leftJoin('p.tags', 't')
            ->where('p.nome LIKE :busca')
            ->orWhere('p.descricao LIKE :busca')
            ->orWhere('t.nome LIKE :busca')
            ->setParameter('busca', "%{$busca}%")
            ->setFirstResult($inicio)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findAllSearch($busca)
    {
        return $this
            ->createQueryBuilder("p")
            ->leftJoin('p.tags', 't')
            ->where('p.nome LIKE :busca')
            ->orWhere('p.descricao LIKE :busca')
            ->orWhere('t.nome LIKE :busca')
            ->setParameter('busca', "%{$busca}%")
            ->getQuery()
            ->getResult();
    }

    public function listProdutos($inicio, $limit)
    {
        return $this
                ->createQueryBuilder("p")
                ->setFirstResult($inicio)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
    }

    public function removeAssociationTag($idProduto)
    {
        $sql = "DELETE FROM produtos_tags WHERE produto_id = :id";
        $params = array('id' => $idProduto);

        return $this->getEntityManager()->getConnection()->prepare($sql)->execute($params);
    }

} 