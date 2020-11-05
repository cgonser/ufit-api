<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CurrencyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadCurrencies($manager);
    }

    private function loadCurrencies(ObjectManager $manager): void
    {
        foreach ($this->getData() as [$name, $code]) {
            $currency = new Currency();
            $currency->setName($name);
            $currency->setCode($code);

            $manager->persist($currency);
            $this->addReference($currency->getCode(), $currency);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            ['Real', 'BRL'],
            ['Euro', 'EUR'],
            ['US Dollars', 'USD'],
        ];
    }
}
