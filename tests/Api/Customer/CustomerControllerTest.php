<?php

namespace App\Tests\Api\Customer;

use App\Customer\Exception\CustomerEmailAddressInUseException;
use Symfony\Component\HttpFoundation\Response;

class CustomerControllerTest extends CustomerBaseTest
{
    public function testGetCustomerUnauthenticated()
    {
        $client = $this->createClient();
        $client->request('GET', '/customers');

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testPostCustomer()
    {
        $client = $this->createClient();

        $customer = $this->getCustomer();

        $email = time().$customer->getEmail();

        $this->jsonPost(
            $client,
            '/customers',
            [
                'name' => $customer->getName(),
                'email' => $email,
                'password' => self::DEFAULT_CUSTOMER_PASSWORD,
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($response['name'], $customer->getName());
        $this->assertSame($response['email'], $email);
    }

    public function testPostDuplicatedCustomer()
    {
        $client = $this->createClient();

        $customer = $this->getCustomer();

        $this->jsonPost(
            $client,
            '/customers',
            [
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
                'password' => self::DEFAULT_CUSTOMER_PASSWORD,
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($response['message'], (new CustomerEmailAddressInUseException())->getMessage());
    }

    public function testGetCustomer()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/customers/current');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);
        $customer = $this->getCustomer();
        $this->assertSame($response['name'], $customer->getName());
        $this->assertSame($response['email'], $customer->getEmail());
    }

    public function testPutCustomer()
    {
        $client = $this->createAuthenticatedClient();

        $customer = $this->getCustomer();
        $customer->setName('updated-'.$customer->getName());

        $this->jsonPut(
            $client,
            '/customers/current',
            [
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $client->request('GET', '/customers/current');
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($response['name'], $customer->getName());
        $this->assertSame($response['email'], $customer->getEmail());
    }
}
