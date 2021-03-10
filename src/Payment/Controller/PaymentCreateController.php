<?php

namespace App\Payment\Controller;

use App\Core\Exception\ApiJsonInputValidationException;
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
use Symfony\Component\Validator\ConstraintViolationListInterface;

class PaymentCreateController extends AbstractController
{
    private PaymentRequestManager $paymentManager;

    private PaymentResponseMapper $paymentResponseMapper;

    public function __construct(
        PaymentRequestManager $paymentManager,
        PaymentResponseMapper $paymentResponseMapper
    ) {
        $this->paymentResponseMapper = $paymentResponseMapper;
        $this->paymentManager = $paymentManager;
    }

    /**
     * @Route("/payments", methods="POST", name="payments_create")
     * @ParamConverter("paymentRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Payment")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=PaymentRequest::class)))
     * @OA\Response(
     *     response=201,
     *     description="Creates a new payment",
     *     @OA\JsonContent(ref=@Model(type=PaymentDto::class))
     * )
     * @OA\Response(response=400, description="Invalid input")
     */
    public function create(
        PaymentRequest $paymentRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $payment = $this->paymentManager->createFromRequest($paymentRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->paymentResponseMapper->map($payment)
        );
    }
}
