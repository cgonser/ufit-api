<?php

namespace App\Core\Controller\PaymentMethod;

use App\Core\Dto\PaymentMethodDto;
use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Exception\PaymentMethodAlreadyExistsException;
use App\Core\Exception\PaymentMethodNotFoundException;
use App\Core\Provider\PaymentMethodProvider;
use App\Core\Request\PaymentMethodRequest;
use App\Core\Response\ApiJsonResponse;
use App\Core\ResponseMapper\PaymentMethodResponseMapper;
use App\Core\Service\PaymentMethodService;
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
     * @Route("/payment_methods/{paymentMethodId}", methods="PUT", name="payment_methods_update")
     *
     * @ParamConverter("paymentMethodRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="PaymentMethod")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=PaymentMethodRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a PaymentMethod",
     *     @OA\JsonContent(ref=@Model(type=PaymentMethodDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function update(
        string $paymentMethodId,
        PaymentMethodRequest $paymentMethodRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $paymentMethod = $this->paymentMethodProvider->get(Uuid::fromString($paymentMethodId));

            $this->paymentMethodService->update($paymentMethod, $paymentMethodRequest);

            return new ApiJsonResponse(
                Response::HTTP_OK,
                $this->paymentMethodResponseMapper->map($paymentMethod)
            );
        } catch (PaymentMethodNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (PaymentMethodAlreadyExistsException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
