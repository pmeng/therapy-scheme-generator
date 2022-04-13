<?php

namespace App\DataFixtures;

use App\Entity\Therapy\Label;
use App\Entity\Therapy\Stub;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($l = 0; $l < 5; $l++) {
            $label = new Label();
            $label->setShortName('Title (label) #' . $l);
            $label->setReportName('Title (label) #' . $l);
            $manager->persist($label);
        }

        for ($s = 0; $s < 10; $s++) {
            $stub = new Stub();
            $stub->setName('Stub #' . $s);
            $stub->setDescription('description for stub #'. $s);
            $stub->setBackground('background for stub #'. $s);
            $manager->persist($stub);
        }

        $manager->flush();

        $labels = $manager->getRepository(Label::class)->findAll();
        $stubs = $manager->getRepository(Stub::class)->findAll();

        foreach ($labels as $label) {
            for ($i = 0; $i < rand(2, 6); $i++) {
                $stub = $stubs[array_rand($stubs)];
                $label->addStub($stub);
            }
            $manager->persist($label);
        }

        $manager->flush();
    }
}
