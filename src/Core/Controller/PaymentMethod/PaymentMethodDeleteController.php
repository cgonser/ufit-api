<?php

namespace App\Core\Controller\PaymentMethod;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Dto\PaymentMethodDto;
use App\Core\Exception\PaymentMethodNotFoundException;
use App\Core\Provider\PaymentMethodProvider;
use App\Core\ResponseMapper\PaymentMethodResponseMapper;
use App\Core\Service\PaymentMethodService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentMethodDeleteController extends AbstractController
{
    private PaymentMethodService $paymentMethodService;

    private PaymentMethodResponseMapper $paymentMethodResponseMapper;

    private PaymentMethodProvider $paymentMethodProvider;

    public function __construct(
        PaymentMethodService $paymentMethodService,
        PaymentMethodProvider $paymentMethodProvider,
        PaymentMethodResponseMapper $paymentMethodResponseMapper
    ) {
        $this->paymentMethodService = $paymentMethodService;
        $this->paymentMethodResponseMapper = $paymentMethodResponseMapper;
        $this->paymentMethodProvider = $paymentMethodProvider;
    }

    /**
     * @Route("/payment_methods/{paymentMethodId}", methods="DELETE", name="payment_methods_delete")
     *
     * @OA\Tag(name="PaymentMethod")
     * @OA\Response(
     *     response=200,
     *     description="Deletes a payment method",
     *     @OA\JsonContent(ref=@Model(type=PaymentMethodDto::class))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Payment method not found"
     * )
     */
    public function delete(string $paymentMethodId): Response
    {
        try {
            $paymentMethod = $this->paymentMethodProvider->get(Uuid::fromString($paymentMethodId));

            $this->paymentMethodService->delete($paymentMethod);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (PaymentMethodNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
