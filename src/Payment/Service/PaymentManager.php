<?php

namespace App\Payment\Service;

use App\Customer\Service\CustomerService;
use App\Payment\Entity\Payment;
//use App\Payment\Message\PaymentCreatedEvent;
//use App\Payment\Message\PaymentDeletedEvent;
//use App\Payment\Message\PaymentUpdatedEvent;
use App\Payment\Repository\PaymentRepository;
use App\Payment\Request\PaymentRequest;
use App\Subscription\Entity\SubscriptionCycle;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\Service\SubscriptionManager;
use App\Vendor\Provider\VendorPlanProvider;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;

class PaymentManager
{
    private PaymentRepository $paymentRepository;

    private CustomerService $customerManager;

    private SubscriptionManager $subscriptionService;

    private MessageBusInterface $messageBus;

    private VendorPlanProvider $vendorPlanProvider;

    public function __construct(
        PaymentRepository $paymentRepository,
        CustomerService $customerManager,
        SubscriptionManager $subscriptionService,
        VendorPlanProvider $vendorPlanProvider,
        MessageBusInterface $messageBus
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->customerManager = $customerManager;
        $this->subscriptionService = $subscriptionService;
        $this->messageBus = $messageBus;
        $this->vendorPlanProvider = $vendorPlanProvider;
    }

    public function createFromRequest(PaymentRequest $paymentRequest): Payment
    {
        $payment = new Payment();

        $this->mapFromRequest($payment, $paymentRequest);

        $this->paymentRepository->save($payment);

//        $this->messageBus->dispatch(new PaymentCreatedEvent($payment->getId()));

        return $payment;
    }

    public function updateFromRequest(Payment $payment, PaymentRequest $paymentRequest)
    {
        $this->mapFromRequest($payment, $paymentRequest);

//        $this->messageBus->dispatch(new PaymentUpdatedEvent($payment->getId()));

        $this->paymentRepository->save($payment);
    }

    public function delete(Payment $payment)
    {
        $this->paymentRepository->delete($payment);

//        $this->messageBus->dispatch(new PaymentDeletedEvent($payment->getId()));
    }

    private function mapFromRequest(Payment $payment, PaymentRequest $paymentRequest)
    {
        if (null !== $paymentRequest->customer) {
            $customer = $this->customerManager->create($paymentRequest->customer);

            $payment->setCustomer($customer);
        }

        if (null === $payment->getCustomer() && null !== $paymentRequest->customerId) {
            $payment->setCustomerId(Uuid::fromString($paymentRequest->customerId));
        }

        if (null !== $paymentRequest->subscriptionCycleId) {
            $payment->setSubscriptionCycleId(Uuid::fromString($paymentRequest->subscriptionCycleId));
        }

        if (null === $payment->getSubscriptionCycle() && null !== $paymentRequest->vendorPlanId) {
            $vendorPlan = $this->vendorPlanProvider->get(Uuid::fromString($paymentRequest->vendorPlanId));

            $subscription = $this->subscriptionService->create($payment->getCustomer(), $vendorPlan);

            $subscriptionCycle = $this->subscriptionService->getCurrentCycle($subscription);
            $payment->setSubscriptionCycle($subscriptionCycle);
        }

        if (null !== $paymentRequest->paymentMethodId) {
            $payment->setPaymentMethodId(Uuid::fromString($paymentRequest->paymentMethodId));
        }

        if (null !== $paymentRequest->paymentDetails) {
            $paymentDetails = $paymentRequest->paymentDetails;
        }
    }
}
