<?php

namespace App\Factory;

use App\Entity\Clasification;
use App\Repository\ClasificationRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Clasification>
 *
 * @method static Clasification|Proxy createOne(array $attributes = [])
 * @method static Clasification[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Clasification|Proxy find(object|array|mixed $criteria)
 * @method static Clasification|Proxy findOrCreate(array $attributes)
 * @method static Clasification|Proxy first(string $sortedField = 'id')
 * @method static Clasification|Proxy last(string $sortedField = 'id')
 * @method static Clasification|Proxy random(array $attributes = [])
 * @method static Clasification|Proxy randomOrCreate(array $attributes = [])
 * @method static Clasification[]|Proxy[] all()
 * @method static Clasification[]|Proxy[] findBy(array $attributes)
 * @method static Clasification[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Clasification[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ClasificationRepository|RepositoryProxy repository()
 * @method Clasification|Proxy create(array|callable $attributes = [])
 */
final class ClasificationFactory extends ModelFactory
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
            // ->afterInstantiate(function(Clasification $Clasification): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Clasification::class;
    }
}
