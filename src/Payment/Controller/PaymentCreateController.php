<?php

declare(strict_types=1);

namespace App\Payment\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Payment\Dto\PaymentDto;
use App\Payment\Request\PaymentRequest;
use App\Payment\ResponseMapper\PaymentResponseMapper;
use App\Payment\Service\PaymentRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentCreateController extends AbstractController
{
    public function __construct(
        private PaymentRequestManager $paymentRequestManager,
        private PaymentResponseMapper $paymentResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="Payment")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=PaymentRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=PaymentDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/payments', name: 'payments_create', methods: 'POST')]
    #[ParamConverter(
        data: 'paymentRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(
        PaymentRequest $paymentRequest,
    ): ApiJsonResponse {
        $payment = $this->paymentRequestManager->createFromRequest($paymentRequest);

        return new ApiJsonResponse(Response::HTTP_CREATED, $this->paymentResponseMapper->map($payment));
    }
}
