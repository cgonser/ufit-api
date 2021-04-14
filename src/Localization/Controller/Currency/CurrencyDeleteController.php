<?php

namespace App\Localization\Controller\Currency;

use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\CurrencyDto;
use App\Localization\Provider\CurrencyProvider;
use App\Localization\Service\CurrencyService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrencyDeleteController extends AbstractController
{
    private CurrencyService $currencyService;

    private CurrencyProvider $currencyProvider;

    public function __construct(
        CurrencyService $currencyService,
        CurrencyProvider $currencyProvider
    ) {
        $this->currencyService = $currencyService;
        $this->currencyProvider = $currencyProvider;
    }

    /**
     * @Route("/currencies/{currencyId}", methods="DELETE", name="currencies_delete")
     *
     * @OA\Tag(name="Currency")
     * @OA\Response(
     *     response=200,
     *     description="Deletes a currency",
     *     @OA\JsonContent(ref=@Model(type=CurrencyDto::class))
     * )
     * @OA\Response(
     *     response=404,
     *     description="Currency not found"
     * )
     */
    public function delete(string $currencyId): Response
    {
        $currency = $this->currencyProvider->get(Uuid::fromString($currencyId));

        $this->currencyService->delete($currency);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
