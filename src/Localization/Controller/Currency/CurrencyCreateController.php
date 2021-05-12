<?php

namespace App\Localization\Controller\Currency;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\CurrencyDto;
use App\Localization\Request\CurrencyRequest;
use App\Localization\ResponseMapper\CurrencyResponseMapper;
use App\Localization\Service\CurrencyService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CurrencyCreateController extends AbstractController
{
    private CurrencyService $currencyService;

    private CurrencyResponseMapper $currencyResponseMapper;

    public function __construct(
        CurrencyService $currencyService,
        CurrencyResponseMapper $currencyResponseMapper
    ) {
        $this->currencyService = $currencyService;
        $this->currencyResponseMapper = $currencyResponseMapper;
    }

    /**
     * @Route("/currencies", methods="POST", name="currencies_create")
     *
     * @ParamConverter("currencyRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Currency")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=CurrencyRequest::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Creates a new currency",
     *     @OA\JsonContent(ref=@Model(type=CurrencyDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function create(
        CurrencyRequest $currencyRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $currency = $this->currencyService->create($currencyRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->currencyResponseMapper->map($currency)
        );
    }
}
