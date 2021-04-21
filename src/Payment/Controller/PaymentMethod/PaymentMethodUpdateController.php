<?php

namespace App\Payment\Controller\PaymentMethod;

use App\Core\Response\ApiJsonResponse;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Payment\Dto\PaymentMethodDto;
use App\Payment\Provider\PaymentMethodProvider;
use App\Payment\Request\PaymentMethodRequest;
use App\Payment\ResponseMapper\PaymentMethodResponseMapper;
use App\Payment\Service\PaymentMethodRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class PaymentMethodUpdateController extends AbstractController
{
    private PaymentMethodRequestManager $paymentMethodRequestManager;

    private PaymentMethodResponseMapper $paymentMethodResponseMapper;

    private PaymentMethodProvider $paymentMethodProvider;

    public function __construct(
        PaymentMethodRequestManager $paymentMethodRequestManager,
        PaymentMethodProvider $paymentMethodProvider,
        PaymentMethodResponseMapper $paymentMethodResponseMapper
    ) {
        $this->paymentMethodRequestManager = $paymentMethodRequestManager;
        $this->paymentMethodResponseMapper = $paymentMethodResponseMapper;
        $this->paymentMethodProvider = $paymentMethodProvider;
    }

    /**
     * @Route("/payment_methods/{paymentMethodId}", methods="PUT", name="payment_methods_update")
     * @ParamConverter("paymentMethodRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="PaymentMethod")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=PaymentMethodRequest::class)))
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=PaymentMethodDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    public function update(
        string $paymentMethodId,
        PaymentMethodRequest $paymentMethodRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $paymentMethod = $this->paymentMethodProvider->get(Uuid::fromString($paymentMethodId));

        $this->paymentMethodRequestManager->updateFromRequest($paymentMethod, $paymentMethodRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->paymentMethodResponseMapper->map($paymentMethod)
        );
    }
}
