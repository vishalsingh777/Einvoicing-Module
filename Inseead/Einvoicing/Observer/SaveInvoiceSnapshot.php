<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Observer;

use Inseead\Einvoicing\Model\Config;
use Inseead\Einvoicing\Model\InvoiceSnapshotFactory;
use Inseead\Einvoicing\Model\ResourceModel\InvoiceSnapshot as InvoiceSnapshotResource;
use Inseead\Einvoicing\Model\ResourceModel\OrderSnapshot as OrderSnapshotResource;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class SaveInvoiceSnapshot implements ObserverInterface
{
    public function __construct(
        private readonly Config                  $config,
        private readonly InvoiceSnapshotFactory  $snapshotFactory,
        private readonly InvoiceSnapshotResource $snapshotResource,
        private readonly OrderSnapshotResource   $orderSnapshotResource,
        private readonly LoggerInterface         $logger
    ) {}

    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled()) return;

        $invoice = $observer->getEvent()->getInvoice();
        if (!$invoice?->getId()) return;

        try {
            $orderSnapshot = $this->orderSnapshotResource->loadByOrderId((int)$invoice->getOrderId());
            if (!$orderSnapshot) return;

            $snapshot = $this->snapshotFactory->create();
            $snapshot->setData('invoice_id', (int)$invoice->getId());
            $snapshot->setData('order_snapshot_id', $orderSnapshot->getData('snapshot_id'));
            $snapshot->setData('company_name', $orderSnapshot->getData('company_name'));
            $snapshot->setData('vat_number', $orderSnapshot->getData('vat_number'));
            $snapshot->setData('siret', $orderSnapshot->getData('siret'));
            $snapshot->setData('financing_profile', $orderSnapshot->getData('financing_profile'));
            $snapshot->setData('invoice_recipient_firstname', $orderSnapshot->getData('invoice_recipient_firstname'));
            $snapshot->setData('invoice_recipient_lastname', $orderSnapshot->getData('invoice_recipient_lastname'));
            $snapshot->setData('invoice_email', $orderSnapshot->getData('invoice_email'));
            $snapshot->setData('po_number', $orderSnapshot->getData('po_number'));

            $this->snapshotResource->save($snapshot);
        } catch (\Throwable $e) {
            $this->logger->error('[Einvoicing] Invoice snapshot failed: ' . $e->getMessage());
        }
    }
}
