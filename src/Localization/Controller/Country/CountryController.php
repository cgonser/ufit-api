<?php

namespace App\Localization\Controller\Country;

use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\CountryDto;
use App\Localization\Provider\CountryProvider;
use App\Localization\Request\CountrySearchRequest;
use App\Localization\ResponseMapper\CountryResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryController
{
    private CountryProvider $countryProvider;

    private CountryResponseMapper $countryResponseMapper;

    public function __construct(
        CountryProvider $countryProvider,
        CountryResponseMapper $countryResponseMapper
    ) {
        $this->countryProvider = $countryProvider;
        $this->countryResponseMapper = $countryResponseMapper;
    }

    /**
     * @Route("/countries", methods="GET", name="countries_get")
     *
     * @ParamConverter("searchRequest", converter="querystring")
     *
     * @OA\Tag(name="Localization / Country")
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=CountryDto::class))))
     * )
     */
    public function getCountries(CountrySearchRequest $searchRequest): Response
    {
        $countries = $this->countryProvider->search($searchRequest);
        $count = $this->countryProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->countryResponseMapper->mapMultiple($countries),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @Route("/countries/{code}", methods="GET", name="countries_get_by_code")
     *
     * @OA\Tag(name="Localization / Country")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=CountryDto::class)))
     */
    public function getCountryByCode(string $code): Response
    {
        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->countryResponseMapper->map($this->countryProvider->getByCode($code))
        );
    }
}
