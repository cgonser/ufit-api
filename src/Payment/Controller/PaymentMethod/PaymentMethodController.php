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
    public function __construct(private PaymentMethodProvider $paymentMethodProvider, private PaymentMethodResponseMapper $paymentMethodResponseMapper)
    {
    }

    /**
     * @OA\Tag(name="PaymentMethod")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=PaymentMethodSearchRequest::class)))
     * @OA\Response(
     *     response=200, description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array",@OA\Items(ref=@Model(type=PaymentMethodDto::class))))
     * )
     */
    #[Route(path: '/payment_methods', methods: 'GET', name: 'payment_methods_get')]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    public function getPaymentMethods(PaymentMethodSearchRequest $paymentMethodSearchRequest) : ApiJsonResponse
    {
        $paymentMethods = $this->paymentMethodProvider->search($paymentMethodSearchRequest);
        $count = $this->paymentMethodProvider->count($paymentMethodSearchRequest);
        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->paymentMethodResponseMapper->mapMultiple($paymentMethods),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     *
     * @OA\Tag(name="PaymentMethod")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=PaymentMethodDto::class)))
     */
    #[Route(path: '/payment_methods/{paymentMethodId}', methods: 'GET', name: 'payment_methods_get_by_id')]
    public function getPaymentMethodById(string $paymentMethodId) : ApiJsonResponse
    {
        $paymentMethod = $this->paymentMethodProvider->get(Uuid::fromString($paymentMethodId));
        return new ApiJsonResponse(Response::HTTP_OK, $this->paymentMethodResponseMapper->map($paymentMethod));
    }
}
