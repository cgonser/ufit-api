<?php

namespace App\Localization\Controller\Timezone;

use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\TimezoneDto;
use App\Localization\Provider\TimezoneProvider;
use App\Localization\Request\TimezoneSearchRequest;
use App\Localization\ResponseMapper\TimezoneResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TimezoneController
{
    private TimezoneProvider $timezoneProvider;

    private TimezoneResponseMapper $timezoneResponseMapper;

    public function __construct(
        TimezoneProvider $timezoneProvider,
        TimezoneResponseMapper $timezoneResponseMapper
    ) {
        $this->timezoneProvider = $timezoneProvider;
        $this->timezoneResponseMapper = $timezoneResponseMapper;
    }

    /**
     * @Route("/timezones", methods="GET", name="timezones_get")
     *
     * @ParamConverter("searchRequest", converter="querystring")
     *
     * @OA\Tag(name="Localization / Timezone")
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=TimezoneDto::class))))
     * )
     */
    public function getTimezones(TimezoneSearchRequest $searchRequest): Response
    {
        if (null !== $searchRequest->countryCode) {
            $timezones = $this->timezoneProvider->findByCountryCode($searchRequest->countryCode);
        } else {
            $timezones = $this->timezoneProvider->findAll();
        }

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->timezoneResponseMapper->mapMultiple($timezones)
        );
    }
}
