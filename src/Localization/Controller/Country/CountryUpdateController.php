<?php

declare(strict_types=1);

namespace App\Localization\Controller\Country;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\CountryDto;
use App\Localization\Provider\CountryProvider;
use App\Localization\Request\CountryRequest;
use App\Localization\ResponseMapper\CountryResponseMapper;
use App\Localization\Service\CountryRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CountryUpdateController extends AbstractController
{
    private CountryRequestManager $countryRequestManager;

    private CountryResponseMapper $countryResponseMapper;

    private CountryProvider $countryProvider;

    public function __construct(
        CountryRequestManager $countryRequestManager,
        CountryProvider $countryProvider,
        CountryResponseMapper $countryResponseMapper
    ) {
        $this->countryRequestManager = $countryRequestManager;
        $this->countryResponseMapper = $countryResponseMapper;
        $this->countryProvider = $countryProvider;
    }

    /**
     * @Route("/countries/{code}", methods="PUT", name="countries_update")
     * @ParamConverter("countryRequest", converter="fos_rest.request_body", options={
     *     "deserializationContext"= {"allow_extra_attributes"=false}
     * })
     *
     * @OA\Tag(name="Localization / Country")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CountryRequest::class)))
     * @OA\Response(response=200, description="Updates a country", @OA\JsonContent(ref=@Model(type=CountryDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    public function update(
        string $code,
        CountryRequest $countryRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        if ($validationErrors->count() > 0) {
            throw new ApiJsonInputValidationException($validationErrors);
        }

        $country = $this->countryProvider->getByCode($code);

        $this->countryRequestManager->updateFromRequest($country, $countryRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->countryResponseMapper->map($country));
    }
}
