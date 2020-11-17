<?php

namespace App\Core\Controller\Currency;

use App\Core\Dto\CurrencyDto;
use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Exception\CurrencyAlreadyExistsException;
use App\Core\Exception\CurrencyNotFoundException;
use App\Core\Provider\CurrencyProvider;
use App\Core\Request\CurrencyRequest;
use App\Core\Response\ApiJsonResponse;
use App\Core\ResponseMapper\CurrencyResponseMapper;
use App\Core\Service\CurrencyService;
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
     * @ParamConverter("currencyRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Currency")
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
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            $currency = $this->currencyProvider->get(Uuid::fromString($currencyId));

            $this->currencyService->update($currency, $currencyRequest);

            return new ApiJsonResponse(
                Response::HTTP_OK,
                $this->currencyResponseMapper->map($currency)
            );
        } catch (CurrencyNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (CurrencyAlreadyExistsException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
