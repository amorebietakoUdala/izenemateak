<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Tag;
use App\Factory\ActivityFactory;
use App\Factory\AnswerFactory;
use App\Factory\ClasificationFactory;
use App\Factory\CourseFactory;
use App\Factory\CourseSessionFactory;
use App\Factory\QuestionFactory;
use App\Factory\QuestionTagFactory;
use App\Factory\RegistrationFactory;
use App\Factory\SessionFactory;
use App\Factory\StatusFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        ActivityFactory::createMany(10);
        ClasificationFactory::createOne([
            'descriptionEs' => 'Zelaieta',
            'descriptionEu' => 'Zelaieta',
        ]);
        ClasificationFactory::createOne([
            'descriptionEs' => 'Larrea',
            'descriptionEu' => 'Larrea',
        ]);
        CourseFactory::createMany(10, function () {
            return [
                'activity' => ActivityFactory::random(),
                'clasification' => ClasificationFactory::random(),
            ];
        });

        RegistrationFactory::createMany(50, function () {
            return [
                'course' => CourseFactory::random(),
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
