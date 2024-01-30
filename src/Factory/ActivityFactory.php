<?php

namespace App\Factory;

use App\Entity\Activity;
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
    protected function getDefaults(): array
    {
        $name = self::faker()->sentence(3);
        $turn = self::faker()->sentence(3);
        return [
            'nameEs' => $name,
            'nameEu' => $name.'_eu',
            'turnEs' => $turn,
            'turnEu' => $turn.'_eu',
            'startDate' => self::faker()->dateTimeBetween('-1 Month'),
            'endDate' => self::faker()->dateTimeBetween('now', '+1 Month'),
            'active' => self::faker()->boolean(),
            'places' => self::faker()->numberBetween(10,30),
            'status' => self::faker()->numberBetween(0,2),
            'limitPlaces' => self::faker()->boolean(),
            'cost' => self::faker()->randomFloat(2,10,30),
            'deposit' => self::faker()->boolean() ? self::faker()->randomFloat(2,5,10) : null,
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this;
    }

    protected static function getClass(): string
    {
        return Activity::class;
    }
}
