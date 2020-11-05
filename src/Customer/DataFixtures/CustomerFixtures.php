<?php

namespace App\Customer\DataFixtures;

use App\Customer\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CustomerFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadCustomers($manager);
    }

    private function loadCustomers(ObjectManager $manager): void
    {
        foreach ($this->getCustomerData() as [$name, $password, $email, $roles]) {
            $user = new Customer();
            $user->setName($name);
            $user->setEmail($email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($email, $user);
        }

        $manager->flush();
    }

    private function getCustomerData(): array
    {
        return [
            ['Customer 1', '123', 'customer1@customer.com', ['ROLE_CUSTOMER']],
            ['Customer 2', '123', 'customer2@customer.com', ['ROLE_CUSTOMER']],
            ['Customer 3', '123', 'customer3@customer.com', ['ROLE_CUSTOMER']],
        ];
    }
}
