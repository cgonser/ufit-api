<?php

namespace App\Tests\Api;

use App\Customer\DataFixtures\MeasurementTypeFixtures;
use App\Payment\DataFixtures\PaymentMethodFixtures;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use App\Localization\DataFixtures\CurrencyFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractApiTest extends WebTestCase
{
    protected ?ReferenceRepository $fixtures = null;

    public function loadFixtures(): void
    {
        if (null === $this->fixtures) {
            $databaseTool = $this->getContainer()->get(DatabaseToolCollection::class)->get();

            $this->fixtures = $databaseTool->loadFixtures([
                CurrencyFixtures::class,
                MeasurementTypeFixtures::class,
                PaymentMethodFixtures::class,
            ])->getReferenceRepository();
        }
    }

    protected function createAuthenticatedClient(string $username, string $password): KernelBrowser
    {
        $client = static::createClient();

        $this->authenticateClient($client, $username, $password);

        return $client;
    }

    abstract protected function authenticateClient(KernelBrowser $client, string $username, string $password): void;

    protected function assertJsonResponse(int $httpCode): void
    {
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertResponseStatusCodeSame($httpCode);
    }

    protected function getAndAssertJsonResponseData(KernelBrowser $client): array
    {
        $jsonResponse = $client->getResponse()->getContent();
        $this->assertJson($jsonResponse);

        return json_decode($jsonResponse, true);
    }
}