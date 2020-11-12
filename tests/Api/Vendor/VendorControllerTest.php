<?php

namespace App\Tests\Api\Vendor;

use App\Vendor\Exception\VendorEmailAddressInUseException;
use Symfony\Component\HttpFoundation\Response;

class VendorControllerTest extends VendorBaseTest
{
    public function testGetVendorUnauthenticated()
    {
        $client = $this->createClient();
        $client->request('GET', '/vendors');

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testPostVendor()
    {
        $client = $this->createClient();

        $vendor = $this->getVendor();

        $email = time().$vendor->getEmail();

        $this->jsonPost(
            $client,
            '/vendors',
            [
                'name' => $vendor->getName(),
                'email' => $email,
                'password' => self::DEFAULT_VENDOR_PASSWORD,
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($response['name'], $vendor->getName());
        $this->assertSame($response['email'], $email);
    }

    public function testPostDuplicatedVendor()
    {
        $client = $this->createClient();

        $vendor = $this->getVendor();

        $this->jsonPost(
            $client,
            '/vendors',
            [
                'name' => $vendor->getName(),
                'email' => $vendor->getEmail(),
                'password' => self::DEFAULT_VENDOR_PASSWORD,
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($response['message'], (new VendorEmailAddressInUseException())->getMessage());
    }

    public function testGetVendor()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/vendors/current');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);
        $vendor = $this->getVendor();
        $this->assertSame($response['name'], $vendor->getName());
        $this->assertSame($response['email'], $vendor->getEmail());
    }

    public function testPutVendor()
    {
        $client = $this->createAuthenticatedClient();

        $vendor = $this->getVendor();
        $vendor->setName('updated-'.$vendor->getName());

        $this->jsonPut(
            $client,
            '/vendors/current',
            [
                'name' => $vendor->getName(),
                'email' => $vendor->getEmail(),
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $client->request('GET', '/vendors/current');
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($response['name'], $vendor->getName());
        $this->assertSame($response['email'], $vendor->getEmail());
    }
}
