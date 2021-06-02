<?php

namespace App\Payment\Controller\Customer;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Provider\CustomerProvider;
use App\Payment\Dto\PaymentDto;
use App\Payment\Request\CustomerPaymentSearchRequest;
use App\Payment\ResponseMapper\PaymentResponseMapper;
use App\Customer\Entity\Customer;
use App\Payment\Provider\CustomerPaymentProvider;
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
    private CustomerPaymentProvider $paymentProvider;

    private PaymentResponseMapper $paymentResponseMapper;

    private CustomerProvider $customerProvider;

    public function __construct(
        CustomerPaymentProvider $paymentProvider,
        PaymentResponseMapper $paymentResponseMapper,
        CustomerProvider $customerProvider
    ) {
        $this->paymentProvider = $paymentProvider;
        $this->paymentResponseMapper = $paymentResponseMapper;
        $this->customerProvider = $customerProvider;
    }

    /**
     * @Route("/customers/{customerId}/payments", methods="GET", name="customer_payments_find")
     * @ParamConverter("searchRequest", converter="querystring")
     * @Security(name="Bearer")
     *
     * @OA\Tag(name="Payment")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=CustomerPaymentSearchRequest::class)))
     * @OA\Response(
     *     response=200, description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=PaymentDto::class))))
     * )
     */
    public function getPayments(string $customerId, CustomerPaymentSearchRequest $searchRequest): Response
    {
        if ('current' === $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // TODO: implement authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $searchRequest->customerId = $customer->getId()->toString();
        $payments = $this->paymentProvider->search($searchRequest);
        $count = $this->paymentProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->paymentResponseMapper->mapMultiple($payments, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @Route("/customers/{customerId}/payments/{paymentId}", methods="GET", name="customer_payments_get_one")
     * @Security(name="Bearer")
     *
     * @OA\Tag(name="Payment")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=PaymentDto::class)))
     */
    public function getPayment(string $customerId, string $paymentId): Response
    {
        if ('current' == $customerId) {
            /** @var Customer $customer */
            $customer = $this->getUser();
        } else {
            // customer fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }

        $payment = $this->paymentProvider->getByCustomerAndId($customer->getId(), Uuid::fromString($paymentId));

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->paymentResponseMapper->map($payment)
        );
    }
}
