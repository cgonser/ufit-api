<?php

namespace App\Core\Controller\Currency;

use App\Core\Dto\CurrencyDto;
use App\Core\Provider\CurrencyProvider;
use App\Core\Response\ApiJsonResponse;
use App\Core\ResponseMapper\CurrencyResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrencyController extends AbstractController
{
    private CurrencyProvider $currencyProvider;

    private CurrencyResponseMapper $currencyResponseMapper;

    public function __construct(
        CurrencyProvider $currencyProvider,
        CurrencyResponseMapper $currencyResponseMapper
    ) {
        $this->currencyProvider = $currencyProvider;
        $this->currencyResponseMapper = $currencyResponseMapper;
    }

    /**
     * @Route("/currencies", methods="GET", name="currencies_get")
     *
     * @OA\Tag(name="Currency")
     * @OA\Response(
     *     response=200,
     *     description="Returns all available currencies",
     *     @OA\JsonContent(ref=@Model(type=CurrencyDto::class))
     * )
     */
    public function getCurrencies(): Response
    {
        $currencies = $this->currencyProvider->findAll();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->currencyResponseMapper->mapMultiple($currencies)
        );
    }

    /**
     * @Route(
     *     "/currencies/{currencyId}",
     *     methods="GET",
     *     name="currencies_get_by_id",
     *     requirements={"currencyId"="%routing.uuid%"}
     * )
     *
     * @OA\Tag(name="Currency")
     * @OA\Response(
     *     response=200,
     *     description="Returns one currency by ID",
     *     @OA\JsonContent(ref=@Model(type=CurrencyDto::class))
     * )
     */
    public function getCurrencyById(string $currencyId): Response
    {
        $currency = $this->currencyProvider->get(Uuid::fromString($currencyId));

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->currencyResponseMapper->map($currency)
        );
    }

    /**
     * @Route(
     *     "/currencies/{currencyCode}",
     *     methods="GET",
     *     name="currencies_get_by_code",
     *     requirements={"currencyCode"="\w+"}
     * )
     *
     * @OA\Tag(name="Currency")
     * @OA\Response(
     *     response=200,
     *     description="Returns one currency by code",
     *     @OA\JsonContent(ref=@Model(type=CurrencyDto::class))
     * )
     */
    public function getCurrencyByCode(string $currencyCode): Response
    {
        $currency = $this->currencyProvider->getByCode($currencyCode);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->currencyResponseMapper->map($currency)
        );
    }
}
