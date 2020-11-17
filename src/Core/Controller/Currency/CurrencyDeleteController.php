<?php

namespace App\Core\Controller\Currency;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonResponse;
use App\Core\Dto\CurrencyDto;
use App\Core\Exception\CurrencyNotFoundException;
use App\Core\Provider\CurrencyProvider;
use App\Core\ResponseMapper\CurrencyResponseMapper;
use App\Core\Service\CurrencyService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrencyDeleteController extends AbstractController
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
     * @Route("/currencies/{currencyId}", methods="DELETE", name="currencies_delete")
     *
     * @OA\Tag(name="Currency")
     * @OA\Response(
     *     response=200,
     *     description="Updates a measurement type",
     *     @OA\JsonContent(ref=@Model(type=CurrencyDto::class))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Measurement type not found"
     * )
     */
    public function delete(string $currencyId): Response
    {
        try {
            $currency = $this->currencyProvider->get(Uuid::fromString($currencyId));

            $this->currencyService->delete($currency);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (CurrencyNotFoundException $e) {
            throw new ApiJsonException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }
}
