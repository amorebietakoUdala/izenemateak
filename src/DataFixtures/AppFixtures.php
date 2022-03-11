<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Tag;
use App\Factory\ActivityFactory;
use App\Factory\AnswerFactory;
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
        StatusFactory::createOne([
            'statusNumber' => 0,
            'descriptionEs' => 'Preinscripción',
            'descriptionEu' => 'Aurre izen-ematea',
        ]);
        StatusFactory::createOne([
            'statusNumber' => 1,
            'descriptionEs' => 'Sorteado',
            'descriptionEu' => 'Zozketatua',
        ]);
        StatusFactory::createOne([
            'statusNumber' => 2,
            'descriptionEs' => 'Cerrado',
            'descriptionEu' => 'Itxia',
        ]);
        CourseFactory::createMany(10, function () {
            return [
                'activity' => ActivityFactory::random(),
                'status' => StatusFactory::random(),
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
