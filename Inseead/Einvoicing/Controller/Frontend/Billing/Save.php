<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Controller\Frontend\Billing;

use Inseead\Einvoicing\Api\BillingInformationManagementInterface;
use Inseead\Einvoicing\Model\BillingInformationFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public function __construct(
        private readonly JsonFactory                           $jsonFactory,
        private readonly RequestInterface                     $request,
        private readonly CheckoutSession                      $checkoutSession,
        private readonly BillingInformationManagementInterface $billingManagement,
        private readonly BillingInformationFactory            $billingInformationFactory,
        private readonly LoggerInterface                      $logger
    ) {}

    public function execute(): mixed
    {
        $result = $this->jsonFactory->create();

        if (!$this->request->isPost() || !$this->request->isAjax()) {
            return $result->setData(['success' => false, 'message' => (string)__('Invalid request.')]);
        }

        $quote = $this->checkoutSession->getQuote();
        if (!$quote?->getId()) {
            return $result->setData(['success' => false, 'message' => (string)__('No active quote found.')]);
        }

        try {
            $post    = $this->request->getPost()->toArray();
            $billing = $this->billingInformationFactory->create();
            $billing->setFinancingProfile($post['financing_profile'] ?? '')
                    ->setCountryOfResidence($post['country_of_residence'] ?? '')
                    ->setSectorOfActivity($post['sector_of_activity'] ?? null)
                    ->setStreet($post['street'] ?? null)
                    ->setStreet2($post['street2'] ?? null)
                    ->setCity($post['city'] ?? null)
                    ->setState($post['state'] ?? null)
                    ->setPostcode($post['postcode'] ?? null)
                    ->setTaxRegistrationStatus($post['tax_registration_status'] ?? null)
                    ->setInvoiceRecipientFirstname($post['invoice_recipient_firstname'] ?? null)
                    ->setInvoiceRecipientLastname($post['invoice_recipient_lastname'] ?? null)
                    ->setInvoiceEmail($post['invoice_email'] ?? null)
                    ->setCompanyFullLegalName($post['company_full_legal_name'] ?? null)
                    ->setCommercialCompanyName($post['commercial_company_name'] ?? null)
                    ->setOrganisationType($post['organisation_type'] ?? null)
                    ->setCompanyStreet($post['company_street'] ?? null)
                    ->setCompanyStreet2($post['company_street2'] ?? null)
                    ->setCompanyCity($post['company_city'] ?? null)
                    ->setCompanyState($post['company_state'] ?? null)
                    ->setCompanyPostcode($post['company_postcode'] ?? null)
                    ->setBusinessRegistrationType($post['business_registration_type'] ?? null)
                    ->setSiret($post['siret'] ?? null)
                    ->setVatIntracommunityNumber($post['vat_intracommunity_number'] ?? null)
                    ->setCertificateId($post['certificate_id'] ?? null)
                    ->setDunsNumber($post['duns_number'] ?? null)
                    ->setPoNumber($post['po_number'] ?? null)
                    ->setRoutingAddress($post['routing_address'] ?? null);

            $siret = $billing->getSiret();
            if ($siret && strlen($siret) === 14) $billing->setSiren(substr($siret, 0, 9));

            return $result->setData($this->billingManagement->save((int)$quote->getId(), $billing));

        } catch (LocalizedException $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            $this->logger->error('[Einvoicing] Save error: ' . $e->getMessage());
            return $result->setData(['success' => false, 'message' => (string)__('An error occurred. Please try again.')]);
        }
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException { return null; }
    public function validateForCsrf(RequestInterface $request): ?bool { return true; }
}
