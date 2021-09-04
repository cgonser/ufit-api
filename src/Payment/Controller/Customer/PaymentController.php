<?php

declare(strict_types=1);

namespace App\Payment\Controller\Customer;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Payment\Dto\PaymentDto;
use App\Payment\Provider\PaymentProvider;
use App\Payment\Request\PaymentSearchRequest;
use App\Payment\ResponseMapper\PaymentResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    public function __construct(
        private PaymentProvider $paymentProvider,
        private PaymentResponseMapper $paymentResponseMapper,
        private CustomerProvider $customerProvider,
    ) {
    }

    /**
     * @OA\Tag(name="Payment")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=PaymentSearchRequest::class)))
     * @OA\Response(
     *     response=200, description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=PaymentDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(path: '/customers/{customerId}/payments', name: 'customer_payments_find', methods: 'GET')]
    #[ParamConverter(data: 'paymentSearchRequest', converter: 'querystring')]
    public function getPayments(string $customerId, PaymentSearchRequest $paymentSearchRequest): ApiJsonResponse
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

        $paymentSearchRequest->customerId = $customer->getId()->toString();

        $payments = $this->paymentProvider->search($paymentSearchRequest);
        $count = $this->paymentProvider->count($paymentSearchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->paymentResponseMapper->mapMultiple($payments, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @OA\Tag(name="Payment")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=PaymentDto::class)))
     * @Security(name="Bearer")
     */
    #[Route(path: '/customers/{customerId}/payments/{paymentId}', name: 'customer_payments_get_one', methods: 'GET')]
    public function getPayment(string $customerId, string $paymentId): ApiJsonResponse
    {
        $customer = $this->customerProvider->get(Uuid::fromString($customerId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $customer);

        $payment = $this->paymentProvider->getByCustomerAndId($customer->getId(), Uuid::fromString($paymentId));

        return new ApiJsonResponse(Response::HTTP_OK, $this->paymentResponseMapper->map($payment));
    }
}
