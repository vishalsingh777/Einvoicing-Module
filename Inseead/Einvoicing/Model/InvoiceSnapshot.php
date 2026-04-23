<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model;

use Inseead\Einvoicing\Api\Data\InvoiceSnapshotInterface;
use Magento\Framework\Model\AbstractModel;

class InvoiceSnapshot extends AbstractModel implements InvoiceSnapshotInterface
{
    protected function _construct(): void { $this->_init(ResourceModel\InvoiceSnapshot::class); }

    public function getSnapshotId(): ?int { $v = $this->getData(self::SNAPSHOT_ID); return $v ? (int)$v : null; }
    public function getInvoiceId(): int { return (int)$this->getData(self::INVOICE_ID); }
    public function setInvoiceId(int $v): self { return $this->setData(self::INVOICE_ID, $v); }
    public function getOrderSnapshotId(): ?int { $v = $this->getData(self::ORDER_SNAPSHOT_ID); return $v ? (int)$v : null; }
    public function setOrderSnapshotId(?int $v): self { return $this->setData(self::ORDER_SNAPSHOT_ID, $v); }
    public function getCompanyName(): ?string { return $this->getData(self::COMPANY_NAME); }
    public function setCompanyName(?string $v): self { return $this->setData(self::COMPANY_NAME, $v); }
    public function getVatNumber(): ?string { return $this->getData(self::VAT_NUMBER); }
    public function setVatNumber(?string $v): self { return $this->setData(self::VAT_NUMBER, $v); }
    public function getSiret(): ?string { return $this->getData(self::SIRET); }
    public function setSiret(?string $v): self { return $this->setData(self::SIRET, $v); }
    public function getFinancingProfile(): ?string { return $this->getData(self::FINANCING_PROFILE); }
    public function setFinancingProfile(?string $v): self { return $this->setData(self::FINANCING_PROFILE, $v); }
    public function getInvoiceRecipientFirstname(): ?string { return $this->getData(self::INVOICE_RECIPIENT_FIRSTNAME); }
    public function setInvoiceRecipientFirstname(?string $v): self { return $this->setData(self::INVOICE_RECIPIENT_FIRSTNAME, $v); }
    public function getInvoiceRecipientLastname(): ?string { return $this->getData(self::INVOICE_RECIPIENT_LASTNAME); }
    public function setInvoiceRecipientLastname(?string $v): self { return $this->setData(self::INVOICE_RECIPIENT_LASTNAME, $v); }
    public function getInvoiceEmail(): ?string { return $this->getData(self::INVOICE_EMAIL); }
    public function setInvoiceEmail(?string $v): self { return $this->setData(self::INVOICE_EMAIL, $v); }
    public function getPoNumber(): ?string { return $this->getData(self::PO_NUMBER); }
    public function setPoNumber(?string $v): self { return $this->setData(self::PO_NUMBER, $v); }
}
