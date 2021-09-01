<?php

declare(strict_types=1);

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
    public function __construct(private PaymentRequestManager $paymentRequestManager, private PaymentResponseMapper $paymentResponseMapper)
    {
    }

    /**
     *
     * @OA\Tag(name="Payment")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=PaymentRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=PaymentDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/payments', methods: 'POST', name: 'payments_create')]
    #[ParamConverter(data: 'paymentRequest', converter: 'fos_rest.request_body', options: ['deserializationContext' => ['allow_extra_attributes' => false]])]
    public function create(PaymentRequest $paymentRequest, ConstraintViolationListInterface $constraintViolationList) : ApiJsonResponse
    {
        if ($constraintViolationList->count() > 0) {
            throw new ApiJsonInputValidationException($constraintViolationList);
        }
        $payment = $this->paymentRequestManager->createFromRequest($paymentRequest);
        return new ApiJsonResponse(Response::HTTP_CREATED, $this->paymentResponseMapper->map($payment));
    }
}
