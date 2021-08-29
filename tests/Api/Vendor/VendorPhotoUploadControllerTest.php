<?php

declare(strict_types=1);

namespace App\Tests\Api\Vendor;

use App\Vendor\Entity\Vendor;
use Aws\S3\S3Client;
use joshtronic\LoremIpsum;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorPhotoUploadControllerTest extends AbstractVendorTest
{
    private const IMAGES_PATH = __DIR__.'/../../Resources/vendor-images/';

    /**
     * @dataProvider providePhotos
     */
    public function testPutPhoto(UploadedFile $uploadedFile): void
    {
        $client = static::createClient();

        $vendorData = $this->getVendorDummyData();
        $this->createVendorDummy($vendorData);

        $client->request(Request::METHOD_PUT, '/vendors/current/photo', [], [], [], $uploadedFile->getContent());
        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->authenticateClient($client, $vendorData['email'], $vendorData['password']);

        $this->setUpUploadEnvironment();
        $client->setServerParameter('CONTENT_TYPE', $uploadedFile->getMimeType());
        $client->request(Request::METHOD_PUT, '/vendors/current/photo', [], [], [], $uploadedFile->getContent());
        $this->assertJsonResponse(Response::HTTP_OK);
        $responseData = $this->getAndAssertJsonResponseData($client);

        $this->tearDownUploadEnvironment();
    }

    public function providePhotos(): array
    {
        $files = [];

        $finder = new Finder();
        $finder->files()->in(self::IMAGES_PATH);
        foreach ($finder as $file) {
            $files [] = [
                new UploadedFile($file->getRealPath(), $file->getRelativePathname()),
            ];
        }

        return $files;
    }

    private function setUpUploadEnvironment(): void
    {
        $bucketName = static::getContainer()->getParameter('s3.buckets.customer_photo');
        $s3Client = static::getContainer()->get(S3Client::class);

        if ($s3Client->doesBucketExist($bucketName)) {
            return;
        }

        $s3Client->createBucket(['Bucket' => $bucketName]);
    }

    private function tearDownUploadEnvironment(): void
    {
        $bucketName = static::getContainer()->getParameter('s3.buckets.customer_photo');
        $s3Client = static::getContainer()->get(S3Client::class);

        $objects = $s3Client->getIterator('ListObjects', (['Bucket' => $bucketName]));

        foreach ($objects as $object) {
            $s3Client->deleteObject([
                'Bucket' => $bucketName,
                'Key' => $object['Key'],
            ]);
        }

        $s3Client->deleteBucket([
            'Bucket' => $bucketName,
        ]);
    }
}
