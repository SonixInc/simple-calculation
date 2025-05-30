<?php declare(strict_types=1);

namespace App\Repository;


use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ProductRepository
 * @package App\Repository
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Finds product by id
     *
     * @param string $id
     * @return Product|null
     */
    public function findById(string $id): ?Product
    {
        return $this->findOneBy(['id' => $id]);
    }
}
