<?php

namespace App\Core\DataFixtures;

use App\Core\Entity\PaymentMethod;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaymentMethodFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadCurrencies($manager);
    }

    private function loadCurrencies(ObjectManager $manager): void
    {
        foreach ($this->getData() as list($name, $countriesEnabled, $countriesDisabled)) {
            $paymentMethod = new PaymentMethod();
            $paymentMethod->setName($name);
            $paymentMethod->setCountriesEnabled($countriesEnabled);
            $paymentMethod->setCountriesDisabled($countriesDisabled);

            $manager->persist($paymentMethod);

            $this->addReference('paymentMethod-'.$paymentMethod->getName(), $paymentMethod);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            ['Boleto', ['BR'], []],
            ['Credit Card', [], []],
        ];
    }
}