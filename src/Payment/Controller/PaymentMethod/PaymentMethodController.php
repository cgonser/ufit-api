<?php

declare(strict_types=1);

namespace App\Payment\Controller\PaymentMethod;

use App\Core\Response\ApiJsonResponse;
use App\Payment\Dto\PaymentMethodDto;
use App\Payment\Provider\PaymentMethodProvider;
use App\Payment\Request\PaymentMethodSearchRequest;
use App\Payment\ResponseMapper\PaymentMethodResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentMethodController extends AbstractController
{
    private PaymentMethodProvider $paymentMethodProvider;

    private PaymentMethodResponseMapper $paymentMethodResponseMapper;

    public function __construct(
        PaymentMethodProvider $paymentMethodProvider,
        PaymentMethodResponseMapper $paymentMethodResponseMapper
    ) {
        $this->paymentMethodProvider = $paymentMethodProvider;
        $this->paymentMethodResponseMapper = $paymentMethodResponseMapper;
    }

    /**
     * @Route("/payment_methods", methods="GET", name="payment_methods_get")
     * @ParamConverter("searchRequest", converter="querystring")

     * @OA\Tag(name="PaymentMethod")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=PaymentMethodSearchRequest::class)))
     * @OA\Response(
     *     response=200, description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=PaymentMethodDto::class))))
     * )
     */
    public function getPaymentMethods(PaymentMethodSearchRequest $searchRequest): Response
    {
        $paymentMethods = $this->paymentMethodProvider->search($searchRequest);
        $count = $this->paymentMethodProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->paymentMethodResponseMapper->mapMultiple($paymentMethods),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @Route("/payment_methods/{paymentMethodId}", methods="GET", name="payment_methods_get_by_id")
     *
     * @OA\Tag(name="PaymentMethod")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=PaymentMethodDto::class)))
     */
    public function getPaymentMethodById(string $paymentMethodId): Response
    {
        $paymentMethod = $this->paymentMethodProvider->get(Uuid::fromString($paymentMethodId));

        return new ApiJsonResponse(Response::HTTP_OK, $this->paymentMethodResponseMapper->map($paymentMethod));
    }
}
