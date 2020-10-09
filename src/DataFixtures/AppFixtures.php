<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i < 10; $i++) {
            $microPost = new MicroPost();
            $microPost->setText('Really random text '. random_int(0, 200));
            $microPost->setTime(new \DateTime('2020-10-12'));
            $manager->persist($microPost);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
