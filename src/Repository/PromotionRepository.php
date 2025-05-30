<?php declare(strict_types=1);

namespace App\Repository;


use App\Entity\Promotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PromotionRepository
 * @package App\Repository
 */
class PromotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promotion::class);
    }

    /**
     * Finds promotion by code
     *
     * @param string $code
     * @return Promotion|null
     */
    public function findByCode(string $code): ?Promotion
    {
        return $this->findOneBy(['code' => $code]);
    }
}
