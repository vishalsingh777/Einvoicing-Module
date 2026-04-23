<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model;

use Inseead\Einvoicing\Api\BillingInformationManagementInterface;
use Inseead\Einvoicing\Api\CompanyRepositoryInterface;
use Inseead\Einvoicing\Api\Data\BillingInformationInterface;
use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Inseead\Einvoicing\Model\Verification\VerifierPool;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Psr\Log\LoggerInterface;

class BillingInformationManagement implements BillingInformationManagementInterface
{
    public function __construct(
        private readonly CartRepositoryInterface    $cartRepository,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly VerifierPool               $verifierPool,
        private readonly Config                     $config,
        private readonly CheckoutSession            $checkoutSession,
        private readonly CustomerSession            $customerSession,
        private readonly LoggerInterface            $logger
    ) {}

    public function save(int $cartId, BillingInformationInterface $billingInformation): array
    {
        try {
            $quote = $this->cartRepository->get($cartId);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not load quote: %1', $e->getMessage()), $e);
        }

        $profile = $billingInformation->getFinancingProfile();
        $country = strtoupper($billingInformation->getCountryOfResidence());

        $this->saveBillingAddress($quote, $billingInformation, $country);

        $quote->setData('einv_financing_profile', $profile);
        $quote->setData('einv_tax_registration_status', $billingInformation->getTaxRegistrationStatus());
        $quote->setData('einv_invoice_recipient_firstname', $billingInformation->getInvoiceRecipientFirstname());
        $quote->setData('einv_invoice_recipient_lastname', $billingInformation->getInvoiceRecipientLastname());
        $quote->setData('einv_invoice_email', $billingInformation->getInvoiceEmail());
        $quote->setData('einv_po_number', $billingInformation->getPoNumber());
        $quote->setData('einv_routing_address', $billingInformation->getRoutingAddress());

        if ($profile === BillingInformationInterface::FINANCING_PROFILE_SPONSORED) {
            $result = $this->handleB2bFlow($quote, $billingInformation, $country);
            if (!$result['success']) return $result;
        } else {
            $quote->setData('einv_verification_status', null);
            $quote->setData('einv_company_id', null);
        }

        try {
            $this->cartRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save billing information: %1', $e->getMessage()), $e);
        }

        return ['success' => true, 'redirect' => 'checkout', 'message' => (string)__('Billing information saved.')];
    }

    private function handleB2bFlow(CartInterface $quote, BillingInformationInterface $billing, string $country): array
    {
        $siret     = $billing->getSiret();
        $vatNumber = $billing->getVatIntracommunityNumber();

        if ($country === 'FR' && $siret) {
            $company = $this->companyRepository->findOrCreateBySiret($siret);
        } elseif ($vatNumber) {
            $company = $this->companyRepository->findOrCreate($vatNumber, $country);
        } else {
            return [
                'success'  => false,
                'redirect' => 'einvoicing/billing/index',
                'message'  => (string)__('Please provide a SIRET (France) or VAT Intracommunity Number.'),
            ];
        }

        $this->populateCompany($company, $billing, $country);

        if ($this->config->isB2bVerificationEnabled()
            && $company->getVerificationStatus() !== CompanyInterface::STATUS_VERIFIED
        ) {
            $verResult = $this->runVerification($company, $country);
            if (!$verResult['success']) return $verResult;
        }

        try {
            $company = $this->companyRepository->save($company);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save company data.'), $e);
        }

        $quote->setData('einv_company_id', $company->getCompanyId());
        $quote->setData('einv_verification_status', $company->getVerificationStatus());

        $vatId = $country === 'FR' ? $siret : $vatNumber;
        if ($vatId) $quote->getBillingAddress()->setVatId($vatId);

        return ['success' => true, 'redirect' => 'checkout', 'message' => ''];
    }

    private function runVerification(CompanyInterface $company, string $country): array
    {
        if (!$this->verifierPool->hasVerifierFor($country)) {
            $this->logger->warning(sprintf('[Einvoicing] No verifier for country "%s" — skipping.', $country));
            $company->setVerificationStatus(CompanyInterface::STATUS_PENDING);
            return ['success' => true, 'redirect' => 'checkout', 'message' => ''];
        }

        $verifier = $this->verifierPool->getForCountry($country);
        $verified = $verifier->verify($company);

        if (!$verified) {
            $company->setVerificationStatus(CompanyInterface::STATUS_FAILED);
            try { $this->companyRepository->save($company); } catch (\Exception) {}
            return [
                'success'  => false,
                'redirect' => 'einvoicing/billing/index',
                'message'  => $verifier->getLastError() ?? (string)__('Company verification failed.'),
            ];
        }

        return ['success' => true, 'redirect' => 'checkout', 'message' => ''];
    }

    private function saveBillingAddress(CartInterface $quote, BillingInformationInterface $billing, string $country): void
    {
        $address = $quote->getBillingAddress();
        $isB2B   = $billing->getFinancingProfile() === BillingInformationInterface::FINANCING_PROFILE_SPONSORED;

        if ($isB2B) {
            $address->setFirstname($billing->getInvoiceRecipientFirstname() ?? '');
            $address->setLastname($billing->getInvoiceRecipientLastname() ?? '');
            $address->setEmail($billing->getInvoiceEmail() ?? $quote->getCustomerEmail() ?? '');
            $address->setCompany($billing->getCompanyFullLegalName() ?? '');
            $address->setStreet(array_filter([$billing->getCompanyStreet(), $billing->getCompanyStreet2()]));
            $address->setCity($billing->getCompanyCity() ?? '');
            $address->setRegion($billing->getCompanyState() ?? '');
            $address->setPostcode($billing->getCompanyPostcode() ?? '');
        } else {
            $customer = $this->customerSession->getCustomerData();
            if ($customer) {
                $address->setFirstname($customer->getFirstname());
                $address->setLastname($customer->getLastname());
                $address->setEmail($customer->getEmail());
            }
            $address->setStreet(array_filter([$billing->getStreet(), $billing->getStreet2()]));
            $address->setCity($billing->getCity() ?? '');
            $address->setRegion($billing->getState() ?? '');
            $address->setPostcode($billing->getPostcode() ?? '');
        }

        $address->setCountryId($country);
        $address->setShouldIgnoreValidation(true);
    }

    private function populateCompany(CompanyInterface $company, BillingInformationInterface $billing, string $country): void
    {
        if ($billing->getCompanyFullLegalName()) $company->setCompanyName($billing->getCompanyFullLegalName());
        $company->setCommercialName($billing->getCommercialCompanyName())
                ->setCountry($country)
                ->setOrganisationType($billing->getOrganisationType())
                ->setSectorOfActivity($billing->getSectorOfActivity())
                ->setTaxRegistrationStatus($billing->getTaxRegistrationStatus())
                ->setBusinessRegistrationType($billing->getBusinessRegistrationType())
                ->setCertificateId($billing->getCertificateId())
                ->setDunsNumber($billing->getDunsNumber())
                ->setVatNumber($billing->getVatIntracommunityNumber());

        if ($billing->getSiret()) {
            $company->setSiret($billing->getSiret())->setSiren(substr($billing->getSiret(), 0, 9));
        }
    }
}
