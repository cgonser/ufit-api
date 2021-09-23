<?php

declare(strict_types=1);

namespace App\Payment\DataFixtures;

use App\Payment\Entity\PaymentMethod;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaymentMethodFixtures extends Fixture
{
    public function load(ObjectManager $objectManager): void
    {
        $this->loadCurrencies($objectManager);
    }

    private function loadCurrencies(ObjectManager $objectManager): void
    {
        foreach ($this->getData() as [$name, $countriesEnabled, $countriesDisabled]) {
            $paymentMethod = new PaymentMethod();
            $paymentMethod->setName($name);
            $paymentMethod->setCountriesEnabled($countriesEnabled);
            $paymentMethod->setCountriesDisabled($countriesDisabled);

            $objectManager->persist($paymentMethod);

            $this->addReference('paymentMethod-'.$paymentMethod->getName(), $paymentMethod);
        }

        $objectManager->flush();
    }

    /**
     * @return array<int, mixed[]>
     */
    private function getData(): array
    {
        return [['Boleto', ['BR'], []], ['Credit Card', [], []]];
    }
}
