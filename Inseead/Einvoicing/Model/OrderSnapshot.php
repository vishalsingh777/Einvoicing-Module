<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model;

use Inseead\Einvoicing\Api\Data\OrderSnapshotInterface;
use Magento\Framework\Model\AbstractModel;

class OrderSnapshot extends AbstractModel implements OrderSnapshotInterface
{
    protected function _construct(): void { $this->_init(ResourceModel\OrderSnapshot::class); }

    public function getSnapshotId(): ?int { $v = $this->getData(self::SNAPSHOT_ID); return $v ? (int)$v : null; }
    public function getOrderId(): int { return (int)$this->getData(self::ORDER_ID); }
    public function setOrderId(int $v): self { return $this->setData(self::ORDER_ID, $v); }
    public function getCompanyId(): ?int { $v = $this->getData(self::COMPANY_ID); return $v ? (int)$v : null; }
    public function setCompanyId(?int $v): self { return $this->setData(self::COMPANY_ID, $v); }
    public function getFinancingProfile(): ?string { return $this->getData(self::FINANCING_PROFILE); }
    public function setFinancingProfile(?string $v): self { return $this->setData(self::FINANCING_PROFILE, $v); }
    public function getCompanyName(): ?string { return $this->getData(self::COMPANY_NAME); }
    public function setCompanyName(?string $v): self { return $this->setData(self::COMPANY_NAME, $v); }
    public function getCommercialName(): ?string { return $this->getData(self::COMMERCIAL_NAME); }
    public function setCommercialName(?string $v): self { return $this->setData(self::COMMERCIAL_NAME, $v); }
    public function getVatNumber(): ?string { return $this->getData(self::VAT_NUMBER); }
    public function setVatNumber(?string $v): self { return $this->setData(self::VAT_NUMBER, $v); }
    public function getSiren(): ?string { return $this->getData(self::SIREN); }
    public function setSiren(?string $v): self { return $this->setData(self::SIREN, $v); }
    public function getSiret(): ?string { return $this->getData(self::SIRET); }
    public function setSiret(?string $v): self { return $this->setData(self::SIRET, $v); }
    public function getCompanyCountry(): ?string { return $this->getData(self::COMPANY_COUNTRY); }
    public function setCompanyCountry(?string $v): self { return $this->setData(self::COMPANY_COUNTRY, $v); }
    public function getOrganisationType(): ?string { return $this->getData(self::ORGANISATION_TYPE); }
    public function setOrganisationType(?string $v): self { return $this->setData(self::ORGANISATION_TYPE, $v); }
    public function getTaxRegistrationStatus(): ?string { return $this->getData(self::TAX_REGISTRATION_STATUS); }
    public function setTaxRegistrationStatus(?string $v): self { return $this->setData(self::TAX_REGISTRATION_STATUS, $v); }
    public function getVerificationStatus(): ?string { return $this->getData(self::VERIFICATION_STATUS); }
    public function setVerificationStatus(?string $v): self { return $this->setData(self::VERIFICATION_STATUS, $v); }
    public function getInvoiceRecipientFirstname(): ?string { return $this->getData(self::INVOICE_RECIPIENT_FIRSTNAME); }
    public function setInvoiceRecipientFirstname(?string $v): self { return $this->setData(self::INVOICE_RECIPIENT_FIRSTNAME, $v); }
    public function getInvoiceRecipientLastname(): ?string { return $this->getData(self::INVOICE_RECIPIENT_LASTNAME); }
    public function setInvoiceRecipientLastname(?string $v): self { return $this->setData(self::INVOICE_RECIPIENT_LASTNAME, $v); }
    public function getInvoiceEmail(): ?string { return $this->getData(self::INVOICE_EMAIL); }
    public function setInvoiceEmail(?string $v): self { return $this->setData(self::INVOICE_EMAIL, $v); }
    public function getPoNumber(): ?string { return $this->getData(self::PO_NUMBER); }
    public function setPoNumber(?string $v): self { return $this->setData(self::PO_NUMBER, $v); }
    public function getRoutingAddress(): ?string { return $this->getData(self::ROUTING_ADDRESS); }
    public function setRoutingAddress(?string $v): self { return $this->setData(self::ROUTING_ADDRESS, $v); }
}
