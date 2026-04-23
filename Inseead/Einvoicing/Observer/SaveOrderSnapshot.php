<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Observer;

use Inseead\Einvoicing\Api\CompanyRepositoryInterface;
use Inseead\Einvoicing\Api\Data\BillingInformationInterface;
use Inseead\Einvoicing\Model\Config;
use Inseead\Einvoicing\Model\OrderSnapshotFactory;
use Inseead\Einvoicing\Model\ResourceModel\OrderSnapshot as OrderSnapshotResource;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class SaveOrderSnapshot implements ObserverInterface
{
    public function __construct(
        private readonly Config                     $config,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly OrderSnapshotFactory       $snapshotFactory,
        private readonly OrderSnapshotResource      $snapshotResource,
        private readonly LoggerInterface            $logger
    ) {}

    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled()) return;

        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        if (!$order?->getId() || !$quote) return;

        $profile = $quote->getData('einv_financing_profile');
        if (!$profile) return;

        try {
            $order->setData('einv_financing_profile', $profile);
            $order->setData('einv_verification_status', $quote->getData('einv_verification_status'));
            $order->setData('einv_company_id', $quote->getData('einv_company_id'));

            $snapshot = $this->snapshotFactory->create();
            $snapshot->setData('order_id', (int)$order->getId());
            $snapshot->setData('financing_profile', $profile);
            $snapshot->setData('verification_status', $quote->getData('einv_verification_status'));
            $snapshot->setData('invoice_recipient_firstname', $quote->getData('einv_invoice_recipient_firstname'));
            $snapshot->setData('invoice_recipient_lastname', $quote->getData('einv_invoice_recipient_lastname'));
            $snapshot->setData('invoice_email', $quote->getData('einv_invoice_email'));
            $snapshot->setData('po_number', $quote->getData('einv_po_number'));
            $snapshot->setData('routing_address', $quote->getData('einv_routing_address'));
            $snapshot->setData('tax_registration_status', $quote->getData('einv_tax_registration_status'));

            $companyId = (int)$quote->getData('einv_company_id');
            if ($companyId && $profile === BillingInformationInterface::FINANCING_PROFILE_SPONSORED) {
                try {
                    $company = $this->companyRepository->getById($companyId);
                    $snapshot->setData('company_id', $companyId);
                    $snapshot->setData('company_name', $company->getCompanyName());
                    $snapshot->setData('commercial_name', $company->getCommercialName());
                    $snapshot->setData('vat_number', $company->getVatNumber());
                    $snapshot->setData('siren', $company->getSiren());
                    $snapshot->setData('siret', $company->getSiret());
                    $snapshot->setData('company_country', $company->getCountry());
                    $snapshot->setData('organisation_type', $company->getOrganisationType());
                } catch (\Exception $e) {
                    $this->logger->warning('[Einvoicing] Company load for snapshot failed: ' . $e->getMessage());
                }
            }

            $this->snapshotResource->save($snapshot);
        } catch (\Throwable $e) {
            $this->logger->error('[Einvoicing] Order snapshot failed: ' . $e->getMessage());
        }
    }
}
