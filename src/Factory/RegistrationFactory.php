<?php

namespace App\Factory;

use App\Entity\Registration;
use App\Repository\RegistrationRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Registration>
 *
 * @method static Registration|Proxy createOne(array $attributes = [])
 * @method static Registration[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Registration|Proxy find(object|array|mixed $criteria)
 * @method static Registration|Proxy findOrCreate(array $attributes)
 * @method static Registration|Proxy first(string $sortedField = 'id')
 * @method static Registration|Proxy last(string $sortedField = 'id')
 * @method static Registration|Proxy random(array $attributes = [])
 * @method static Registration|Proxy randomOrCreate(array $attributes = [])
 * @method static Registration[]|Proxy[] all()
 * @method static Registration[]|Proxy[] findBy(array $attributes)
 * @method static Registration[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Registration[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static RegistrationRepository|RepositoryProxy repository()
 * @method Registration|Proxy create(array|callable $attributes = [])
 */
final class RegistrationFactory extends ModelFactory
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
            'email' => self::faker()->email(),
            'dni' => '11111111H',
            'name' => self::faker()->firstName(),
            'surname1' => self::faker()->lastName(),
            'surname2' => self::faker()->lastName(),
            'telephone1' => self::faker()->phoneNumber(),
            'telephone2' => self::faker()->phoneNumber(),
            'dateOfBirth' => self::faker()->dateTimeBetween('-50 years', '-5 years'),
            'representativeDni' => '22222222J',
            'representativeName' => self::faker()->firstName(),
            'representativeSurname1' => self::faker()->lastName(),
            'representativeSurname2' => self::faker()->lastName(),
            'paymentDni' => '33333333P',
            'paymentIBANAccount' => self::faker()->iban('es'),
            'subscriber' => self::faker()->boolean(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Registration $registration): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Registration::class;
    }
}
