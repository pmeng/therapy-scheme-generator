<?php

namespace App\DataFixtures;

use App\Entity\Template;
use App\Entity\Therapy\Label;
use App\Entity\Therapy\Stub;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create('en_US');

        for ($l = 0; $l < 25; $l++) {
            $label = new Label();
            $labelTitle = $generator->words(mt_rand(6, 12), true);
            $label->setShortName(substr($labelTitle, 0, 6));
            $label->setReportName($labelTitle);
            $manager->persist($label);
        }

        for ($s = 0; $s < 100; $s++) {
            $stub = new Stub();
            $stub->setName($generator->name);
            $stub->setDescription($generator->realText(200));
            $stub->setExcerpt($generator->realText(50));
            $stub->setBackground($generator->realText(250));
            $manager->persist($stub);
        }

        $manager->flush();

        $labels = $manager->getRepository(Label::class)->findAll();
        $stubs = $manager->getRepository(Stub::class)->findAll();
        $targets = [];

        foreach ($labels as $label) {
            $lid = $label->getId();
            $targets[$lid] = [];
            for ($i = 0; $i < rand(2, 6); $i++) {
                $stub = $stubs[array_rand($stubs)];
                $label->addStub($stub);
                $targets[$lid][] = $stub->getId();
            }
            $targets[$lid] = array_unique($targets[$lid]);
            $manager->persist($label);
        }

        for ($t = 0; $t < 100; $t++) {
            $template = new Template();
            $template->setName($generator->name . ' - ' . $generator->date());
            $template->setTargets($targets);
            $manager->persist($template);
        }

        $manager->flush();
    }
}
