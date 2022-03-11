<?php

namespace App\Factory;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Activity>
 *
 * @method static Activity|Proxy createOne(array $attributes = [])
 * @method static Activity[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Activity|Proxy find(object|array|mixed $criteria)
 * @method static Activity|Proxy findOrCreate(array $attributes)
 * @method static Activity|Proxy first(string $sortedField = 'id')
 * @method static Activity|Proxy last(string $sortedField = 'id')
 * @method static Activity|Proxy random(array $attributes = [])
 * @method static Activity|Proxy randomOrCreate(array $attributes = [])
 * @method static Activity[]|Proxy[] all()
 * @method static Activity[]|Proxy[] findBy(array $attributes)
 * @method static Activity[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Activity[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ActivityRepository|RepositoryProxy repository()
 * @method Activity|Proxy create(array|callable $attributes = [])
 */
final class ActivityFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'name' => self::faker()->word(4),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Activity $activity): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Activity::class;
    }
}
