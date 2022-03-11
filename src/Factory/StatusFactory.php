<?php

namespace App\Factory;

use App\Entity\Status;
use App\Repository\StatusRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Status>
 *
 * @method static Status|Proxy createOne(array $attributes = [])
 * @method static Status[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Status|Proxy find(object|array|mixed $criteria)
 * @method static Status|Proxy findOrCreate(array $attributes)
 * @method static Status|Proxy first(string $sortedField = 'id')
 * @method static Status|Proxy last(string $sortedField = 'id')
 * @method static Status|Proxy random(array $attributes = [])
 * @method static Status|Proxy randomOrCreate(array $attributes = [])
 * @method static Status[]|Proxy[] all()
 * @method static Status[]|Proxy[] findBy(array $attributes)
 * @method static Status[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Status[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static StatusRepository|RepositoryProxy repository()
 * @method Status|Proxy create(array|callable $attributes = [])
 */
final class StatusFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        $word = self::faker()->word(2);
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'descriptionEs' => $word,
            'descriptionEu' => $word.'_eu',
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Status $Status): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Status::class;
    }
}
