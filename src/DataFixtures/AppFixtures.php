<?php

namespace App\DataFixtures;

use App\Factory\ActivityTypeFactory;
use App\Factory\ClasificationFactory;
use App\Factory\ActivityFactory;
use App\Factory\RegistrationFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        ActivityTypeFactory::createMany(10);
        ClasificationFactory::createOne([
            'descriptionEs' => 'Zelaieta',
            'descriptionEu' => 'Zelaieta',
        ]);
        ClasificationFactory::createOne([
            'descriptionEs' => 'Larrea',
            'descriptionEu' => 'Larrea',
        ]);
        ActivityFactory::createMany(10, function () {
            return [
                'activityType' => ActivityTypeFactory::random(),
                'clasification' => ClasificationFactory::random(),
            ];
        });

        RegistrationFactory::createMany(50, function () {
            return [
                'activity' => ActivityFactory::random(),
            ];
        });
        UserFactory::createOne([
            'username' => 'ibilbao',
            'email' => 'ibilbao@amorebieta.eus',
            'roles' => ['ROLE_ADMIN']
        ]);

        UserFactory::createMany(10);

        $manager->flush();
    }
}
