<?php

namespace App\Core\Controller\PaymentMethod;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Dto\PaymentMethodDto;
use App\Core\Exception\PaymentMethodAlreadyExistsException;
use App\Core\Request\PaymentMethodRequest;
use App\Core\ResponseMapper\PaymentMethodResponseMapper;
use App\Core\Service\PaymentMethodService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class PaymentMethodCreateController extends AbstractController
{
    private PaymentMethodService $paymentMethodService;

    private PaymentMethodResponseMapper $paymentMethodResponseMapper;

    public function __construct(
        PaymentMethodService $paymentMethodService,
        PaymentMethodResponseMapper $paymentMethodResponseMapper
    ) {
        $this->paymentMethodService = $paymentMethodService;
        $this->paymentMethodResponseMapper = $paymentMethodResponseMapper;
    }

    /**
     * @Route("/payment_methods", methods="POST", name="payment_methods_create")
     *
     * @ParamConverter("paymentMethodRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="PaymentMethod")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=PaymentMethodRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new PaymentMethod",
     *     @OA\JsonContent(ref=@Model(type=PaymentMethodDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        PaymentMethodRequest $paymentMethodRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $paymentMethod = $this->paymentMethodService->create($paymentMethodRequest);

            return new ApiJsonResponse(
                Response::HTTP_CREATED,
                $this->paymentMethodResponseMapper->map($paymentMethod)
            );
        } catch (PaymentMethodAlreadyExistsException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
