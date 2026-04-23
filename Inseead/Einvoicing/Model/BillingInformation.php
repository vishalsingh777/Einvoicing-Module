<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model;

use Inseead\Einvoicing\Api\Data\BillingInformationInterface;
use Magento\Framework\DataObject;

class BillingInformation extends DataObject implements BillingInformationInterface
{
    public function getFinancingProfile(): string { return (string)$this->getData(self::FINANCING_PROFILE); }
    public function setFinancingProfile(string $v): self { return $this->setData(self::FINANCING_PROFILE, $v); }
    public function getCountryOfResidence(): string { return (string)$this->getData(self::COUNTRY_OF_RESIDENCE); }
    public function setCountryOfResidence(string $v): self { return $this->setData(self::COUNTRY_OF_RESIDENCE, $v); }
    public function getSectorOfActivity(): ?string { return $this->getData(self::SECTOR_OF_ACTIVITY); }
    public function setSectorOfActivity(?string $v): self { return $this->setData(self::SECTOR_OF_ACTIVITY, $v); }
    public function getStreet(): ?string { return $this->getData(self::STREET); }
    public function setStreet(?string $v): self { return $this->setData(self::STREET, $v); }
    public function getStreet2(): ?string { return $this->getData(self::STREET2); }
    public function setStreet2(?string $v): self { return $this->setData(self::STREET2, $v); }
    public function getCity(): ?string { return $this->getData(self::CITY); }
    public function setCity(?string $v): self { return $this->setData(self::CITY, $v); }
    public function getState(): ?string { return $this->getData(self::STATE); }
    public function setState(?string $v): self { return $this->setData(self::STATE, $v); }
    public function getPostcode(): ?string { return $this->getData(self::POSTCODE); }
    public function setPostcode(?string $v): self { return $this->setData(self::POSTCODE, $v); }
    public function getTaxRegistrationStatus(): ?string { return $this->getData(self::TAX_REGISTRATION_STATUS); }
    public function setTaxRegistrationStatus(?string $v): self { return $this->setData(self::TAX_REGISTRATION_STATUS, $v); }
    public function getInvoiceRecipientFirstname(): ?string { return $this->getData(self::INVOICE_RECIPIENT_FIRSTNAME); }
    public function setInvoiceRecipientFirstname(?string $v): self { return $this->setData(self::INVOICE_RECIPIENT_FIRSTNAME, $v); }
    public function getInvoiceRecipientLastname(): ?string { return $this->getData(self::INVOICE_RECIPIENT_LASTNAME); }
    public function setInvoiceRecipientLastname(?string $v): self { return $this->setData(self::INVOICE_RECIPIENT_LASTNAME, $v); }
    public function getInvoiceEmail(): ?string { return $this->getData(self::INVOICE_EMAIL); }
    public function setInvoiceEmail(?string $v): self { return $this->setData(self::INVOICE_EMAIL, $v); }
    public function getCompanyFullLegalName(): ?string { return $this->getData(self::COMPANY_FULL_LEGAL_NAME); }
    public function setCompanyFullLegalName(?string $v): self { return $this->setData(self::COMPANY_FULL_LEGAL_NAME, $v); }
    public function getCommercialCompanyName(): ?string { return $this->getData(self::COMMERCIAL_COMPANY_NAME); }
    public function setCommercialCompanyName(?string $v): self { return $this->setData(self::COMMERCIAL_COMPANY_NAME, $v); }
    public function getOrganisationType(): ?string { return $this->getData(self::ORGANISATION_TYPE); }
    public function setOrganisationType(?string $v): self { return $this->setData(self::ORGANISATION_TYPE, $v); }
    public function getCompanyStreet(): ?string { return $this->getData(self::COMPANY_STREET); }
    public function setCompanyStreet(?string $v): self { return $this->setData(self::COMPANY_STREET, $v); }
    public function getCompanyStreet2(): ?string { return $this->getData(self::COMPANY_STREET2); }
    public function setCompanyStreet2(?string $v): self { return $this->setData(self::COMPANY_STREET2, $v); }
    public function getCompanyCity(): ?string { return $this->getData(self::COMPANY_CITY); }
    public function setCompanyCity(?string $v): self { return $this->setData(self::COMPANY_CITY, $v); }
    public function getCompanyState(): ?string { return $this->getData(self::COMPANY_STATE); }
    public function setCompanyState(?string $v): self { return $this->setData(self::COMPANY_STATE, $v); }
    public function getCompanyPostcode(): ?string { return $this->getData(self::COMPANY_POSTCODE); }
    public function setCompanyPostcode(?string $v): self { return $this->setData(self::COMPANY_POSTCODE, $v); }
    public function getBusinessRegistrationType(): ?string { return $this->getData(self::BUSINESS_REGISTRATION_TYPE); }
    public function setBusinessRegistrationType(?string $v): self { return $this->setData(self::BUSINESS_REGISTRATION_TYPE, $v); }
    public function getSiret(): ?string { return $this->getData(self::SIRET); }
    public function setSiret(?string $v): self { return $this->setData(self::SIRET, $v); }
    public function getSiren(): ?string { return $this->getData(self::SIREN); }
    public function setSiren(?string $v): self { return $this->setData(self::SIREN, $v); }
    public function getVatIntracommunityNumber(): ?string { return $this->getData(self::VAT_INTRACOMMUNITY_NUMBER); }
    public function setVatIntracommunityNumber(?string $v): self { return $this->setData(self::VAT_INTRACOMMUNITY_NUMBER, $v); }
    public function getCertificateId(): ?string { return $this->getData(self::CERTIFICATE_ID); }
    public function setCertificateId(?string $v): self { return $this->setData(self::CERTIFICATE_ID, $v); }
    public function getDunsNumber(): ?string { return $this->getData(self::DUNS_NUMBER); }
    public function setDunsNumber(?string $v): self { return $this->setData(self::DUNS_NUMBER, $v); }
    public function getPoNumber(): ?string { return $this->getData(self::PO_NUMBER); }
    public function setPoNumber(?string $v): self { return $this->setData(self::PO_NUMBER, $v); }
    public function getRoutingAddress(): ?string { return $this->getData(self::ROUTING_ADDRESS); }
    public function setRoutingAddress(?string $v): self { return $this->setData(self::ROUTING_ADDRESS, $v); }
}
