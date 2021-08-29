<?php

declare(strict_types=1);

namespace App\Payment\Controller\PaymentMethod;

use App\Core\Response\ApiJsonResponse;
use App\Payment\Provider\PaymentMethodProvider;
use App\Payment\Service\PaymentMethodManager;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentMethodDeleteController extends AbstractController
{
    private PaymentMethodManager $paymentMethodManager;

    private PaymentMethodProvider $paymentMethodProvider;

    public function __construct(
        PaymentMethodManager $paymentMethodManager,
        PaymentMethodProvider $paymentMethodProvider
    ) {
        $this->paymentMethodManager = $paymentMethodManager;
        $this->paymentMethodProvider = $paymentMethodProvider;
    }

    /**
     * @Route("/payment_methods/{paymentMethodId}", methods="DELETE", name="payment_methods_delete")
     *
     * @OA\Tag(name="PaymentMethod")
     * @OA\Response(response=204, description="Deleted")
     * @OA\Response(response=404, description="Payment method not found")
     */
    public function delete(string $paymentMethodId): Response
    {
        $paymentMethod = $this->paymentMethodProvider->get(Uuid::fromString($paymentMethodId));

        $this->paymentMethodManager->delete($paymentMethod);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
