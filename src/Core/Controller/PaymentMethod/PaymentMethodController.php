<?php

namespace App\Core\Controller\PaymentMethod;

use App\Core\Dto\PaymentMethodDto;
use App\Core\Provider\PaymentMethodProvider;
use App\Core\Response\ApiJsonResponse;
use App\Core\ResponseMapper\PaymentMethodResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
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
     *
     * @OA\Tag(name="PaymentMethod")
     * @OA\Response(
     *     response=200,
     *     description="Returns all available payment methods",
     *     @OA\JsonContent(ref=@Model(type=PaymentMethodDto::class))
     * )
     */
    public function getPaymentMethods(): Response
    {
        $paymentMethods = $this->paymentMethodProvider->findAll();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->paymentMethodResponseMapper->mapMultiple($paymentMethods)
        );
    }

    /**
     * @Route("/payment_methods/{paymentMethodId}", methods="GET", name="payment_methods_get_by_id")
     *
     * @OA\Tag(name="PaymentMethod")
     * @OA\Response(
     *     response=200,
     *     description="Returns one PaymentMethod by ID",
     *     @OA\JsonContent(ref=@Model(type=PaymentMethodDto::class))
     * )
     */
    public function getPaymentMethodById(string $paymentMethodId): Response
    {
        $paymentMethod = $this->paymentMethodProvider->get(Uuid::fromString($paymentMethodId));

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->paymentMethodResponseMapper->map($paymentMethod)
        );
    }
}
