<?php

declare(strict_types=1);

namespace App\Payment\Controller\PaymentMethod;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Payment\Dto\PaymentMethodDto;
use App\Payment\Request\PaymentMethodRequest;
use App\Payment\ResponseMapper\PaymentMethodResponseMapper;
use App\Payment\Service\PaymentMethodRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class PaymentMethodCreateController extends AbstractController
{
    public function __construct(
        private PaymentMethodRequestManager $paymentMethodRequestManager,
        private PaymentMethodResponseMapper $paymentMethodResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="PaymentMethod")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=PaymentMethodRequest::class)))
     * @OA\Response(response=201, description="Created", @OA\JsonContent(ref=@Model(type=PaymentMethodDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/payment_methods', name: 'payment_methods_create', methods: 'POST')]
    #[ParamConverter(
        data: 'paymentMethodRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(
        PaymentMethodRequest $paymentMethodRequest,
    ): ApiJsonResponse {
        $paymentMethod = $this->paymentMethodRequestManager->createFromRequest($paymentMethodRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->paymentMethodResponseMapper->map($paymentMethod)
        );
    }
}
