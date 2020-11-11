<?php

namespace App\Customer\Controller;

use App\Core\Exception\ApiJsonException;
use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerInvalidPasswordException;
use App\Customer\Request\CustomerPasswordChangeRequest;
use App\Customer\Service\CustomerService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerPasswordController extends AbstractController
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @Route("/customers/{customerId}/password", methods="PUT", name="customer_password_change")
     *
     * @ParamConverter("customerPasswordChangeRequest", converter="fos_rest.request_body")
     *
     * @OA\Tag(name="Customer")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=CustomerPasswordChangeRequest::class))
     * )
     * @OA\Response(
     *     response=204,
     *     description="Updates the current customer"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     */
    public function changePassword(
        string $customerId,
        CustomerPasswordChangeRequest $customerPasswordChangeRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        try {
            if ($validationErrors->count() > 0) {
                throw new ApiJsonInputValidationException($validationErrors);
            }

            if ('current' == $customerId) {
                /** @var Customer $customer */
                $customer = $this->getUser();
            } else {
                // customer fetching not implemented yet; requires also authorization
                throw new ApiJsonException(Response::HTTP_UNAUTHORIZED);
            }

            $this->customerService->changePassword($customer, $customerPasswordChangeRequest);

            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        } catch (CustomerInvalidPasswordException $e) {
            throw new ApiJsonException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
