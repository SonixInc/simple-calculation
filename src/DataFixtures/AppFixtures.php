<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Promotion;
use App\Entity\Tax;
use App\Enum\PromotionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->createProducts($manager);
        $this->createPromotions($manager);
        $this->createTaxes($manager);

        $manager->flush();
    }

    /**
     * Creates 3 products in database
     *
     * @param ObjectManager $manager
     * @return void
     */
    private function createProducts(ObjectManager $manager): void
    {
        $product1 = new Product();
        $product1->setName('IPhone');
        $product1->setPrice(100);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('Headphones');
        $product2->setPrice(20);
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setName('Case');
        $product3->setPrice(10);
        $manager->persist($product3);
    }

    /**
     * Creates some promotions in database
     *
     * @param ObjectManager $manager
     * @return void
     */
    private function createPromotions(ObjectManager $manager): void
    {
        $promotion1 = new Promotion();
        $promotion1->setName('Surprise promotion');
        $promotion1->setCode('F5');
        $promotion1->setType(PromotionType::Fixed);
        $promotion1->setDiscount(5);
        $manager->persist($promotion1);

        $promotion2 = new Promotion();
        $promotion2->setName('Christmas promotion');
        $promotion2->setCode('P10');
        $promotion2->setType(PromotionType::Percentage);
        $promotion2->setDiscount(10);
        $manager->persist($promotion2);

        $promotion3 = new Promotion();
        $promotion3->setName('Partner promotion');
        $promotion3->setCode('P50');
        $promotion3->setType(PromotionType::Percentage);
        $promotion3->setDiscount(50);
        $manager->persist($promotion3);
    }

    /**
     * Create taxes for different countries in database
     *
     * @param ObjectManager $manager
     * @return void
     */
    private function createTaxes(ObjectManager $manager): void
    {
        $tax1 = new Tax();
        $tax1->setRate(19);
        $tax1->setCountryCode('DE');
        $manager->persist($tax1);

        $tax2 = new Tax();
        $tax2->setRate(22);
        $tax2->setCountryCode('IT');
        $manager->persist($tax2);

        $tax3 = new Tax();
        $tax3->setRate(20);
        $tax3->setCountryCode('FR');
        $manager->persist($tax3);

        $tax4 = new Tax();
        $tax4->setRate(24);
        $tax4->setCountryCode('GR');
        $manager->persist($tax4);
    }
}
