<?php

declare(strict_types=1);

namespace App\Customer\Controller\BillingInformation;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Customer\Provider\BillingInformationProvider;
use App\Customer\Service\BillingInformationManager;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BillingInformationDeleteController extends AbstractController
{
    private BillingInformationManager $billingInformationManager;

    private BillingInformationProvider $billingInformationProvider;

    public function __construct(
        BillingInformationManager $billingInformationManager,
        BillingInformationProvider $billingInformationProvider
    ) {
        $this->billingInformationManager = $billingInformationManager;
        $this->billingInformationProvider = $billingInformationProvider;
    }

    /**
     * @Route(
     *     "/customers/{customerId}/billing_information/{billingInformationId}",
     *     methods="DELETE",
     *     name="customer_billing_information_delete"
     * )
     *
     * @OA\Tag(name="Customer / Billing Information")
     * @OA\Response(response=204, description="Success")
     * @OA\Response(response=404, description="Resource not found")
     */
    public function delete(string $customerId, string $billingInformationId): Response
    {
        if ('current' === $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $billingInformation = $this->billingInformationProvider->getByCustomerAndId(
            $customer->getId(),
            Uuid::fromString($billingInformationId)
        );

        $this->billingInformationManager->delete($billingInformation);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
