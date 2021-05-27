<?php

namespace App\Localization\Controller\Currency;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\CurrencyDto;
use App\Localization\Provider\CurrencyProvider;
use App\Localization\Request\CurrencyRequest;
use App\Localization\ResponseMapper\CurrencyResponseMapper;
use App\Localization\Service\CurrencyService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CurrencyUpdateController extends AbstractController
{
    private CurrencyService $currencyService;

    private CurrencyResponseMapper $currencyResponseMapper;

    private CurrencyProvider $currencyProvider;

    public function __construct(
        CurrencyService $currencyService,
        CurrencyProvider $currencyProvider,
        CurrencyResponseMapper $currencyResponseMapper
    ) {
        $this->currencyService = $currencyService;
        $this->currencyResponseMapper = $currencyResponseMapper;
        $this->currencyProvider = $currencyProvider;
    }

    /**
     * @Route("/currencies/{currencyId}", methods="PUT", name="currencies_update")
     *
     * @ParamConverter("currencyRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Localization / Currency")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=CurrencyRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Updates a currency",
     *     @OA\JsonContent(ref=@Model(type=CurrencyDto::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function update(
        string $currencyId,
        CurrencyRequest $currencyRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $currency = $this->currencyProvider->get(Uuid::fromString($currencyId));

        $this->currencyService->update($currency, $currencyRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->currencyResponseMapper->map($currency)
        );
    }
}
