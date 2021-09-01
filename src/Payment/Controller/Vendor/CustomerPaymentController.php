<?php

declare(strict_types=1);

namespace App\Payment\Controller\Vendor;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Payment\Dto\PaymentDto;
use App\Payment\Provider\PaymentProvider;
use App\Payment\Request\PaymentSearchRequest;
use App\Payment\ResponseMapper\PaymentResponseMapper;
use App\Vendor\Entity\Vendor;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerPaymentController extends AbstractController
{
    public function __construct(private PaymentProvider $paymentProvider, private PaymentResponseMapper $paymentResponseMapper)
    {
    }

    /**
     *
     * @OA\Tag(name="Vendor / Customer / Payment")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=PaymentSearchRequest::class)))
     * @OA\Response(
     *     response=200, description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=PaymentDto::class))))
     * )
     * @Security(name="Bearer")
     */
    #[Route(path: '/vendors/{vendorId}/customers/{customerId}/payments', methods: 'GET', name: 'vendor_customer_payments_find')]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    public function getPayments(string $vendorId, string $customerId, PaymentSearchRequest $paymentSearchRequest) : ApiJsonResponse
    {
        if ('current' === $vendorId) {
            /** @var Vendor $user */
            $user = $this->getUser();
        } else {
            // vendor fetching not implemented yet; requires also authorization
            throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
        }
        $paymentSearchRequest->vendorId = $user->getId()
            ->toString();
        $paymentSearchRequest->customerId = $customerId;
        $payments = $this->paymentProvider->search($paymentSearchRequest);
        $count = $this->paymentProvider->count($paymentSearchRequest);
        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->paymentResponseMapper->mapMultiplePublic($payments),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
