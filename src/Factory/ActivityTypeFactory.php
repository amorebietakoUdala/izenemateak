<?php

namespace App\Factory;

use App\Entity\ActivityType;
use App\Repository\ActivityTypeRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ActivityType>
 *
 * @method static ActivityType|Proxy createOne(array $attributes = [])
 * @method static ActivityType[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ActivityType|Proxy find(object|array|mixed $criteria)
 * @method static ActivityType|Proxy findOrCreate(array $attributes)
 * @method static ActivityType|Proxy first(string $sortedField = 'id')
 * @method static ActivityType|Proxy last(string $sortedField = 'id')
 * @method static ActivityType|Proxy random(array $attributes = [])
 * @method static ActivityType|Proxy randomOrCreate(array $attributes = [])
 * @method static ActivityType[]|Proxy[] all()
 * @method static ActivityType[]|Proxy[] findBy(array $attributes)
 * @method static ActivityType[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ActivityType[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ActivityTypeRepository|RepositoryProxy repository()
 * @method ActivityType|Proxy create(array|callable $attributes = [])
 */
final class ActivityTypeFactory extends ModelFactory
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
            // ->afterInstantiate(function(ActivityType $ActivityType): void {})
        ;
    }

    protected static function getClass(): string
    {
        return ActivityType::class;
    }
}
