<?php declare(strict_types=1);

namespace App\Repository;


use App\Entity\Tax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TaxRepository
 * @package App\Repository
 */
class TaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tax::class);
    }

    /**
     * Finds tax by country code
     *
     * @param string $countryCode
     * @return Tax|null
     */
    public function findByCountryCode(string $countryCode): ?Tax
    {
        return $this->findOneBy(['countryCode' => $countryCode]);
    }
}
