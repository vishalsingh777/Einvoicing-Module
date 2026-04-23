<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model;

use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Magento\Framework\Model\AbstractModel;

class Company extends AbstractModel implements CompanyInterface
{
    protected function _construct(): void { $this->_init(ResourceModel\Company::class); }

    public function getCompanyId(): ?int { $v = $this->getData(self::COMPANY_ID); return $v !== null ? (int)$v : null; }
    public function setCompanyId(int $v): self { return $this->setData(self::COMPANY_ID, $v); }
    public function getCompanyName(): ?string { return $this->getData(self::COMPANY_NAME); }
    public function setCompanyName(string $v): self { return $this->setData(self::COMPANY_NAME, $v); }
    public function getCommercialName(): ?string { return $this->getData(self::COMMERCIAL_NAME); }
    public function setCommercialName(?string $v): self { return $this->setData(self::COMMERCIAL_NAME, $v); }
    public function getVatNumber(): ?string { return $this->getData(self::VAT_NUMBER); }
    public function setVatNumber(?string $v): self { return $this->setData(self::VAT_NUMBER, $v); }
    public function getSiren(): ?string { return $this->getData(self::SIREN); }
    public function setSiren(?string $v): self { return $this->setData(self::SIREN, $v); }
    public function getSiret(): ?string { return $this->getData(self::SIRET); }
    public function setSiret(?string $v): self { return $this->setData(self::SIRET, $v); }
    public function getApenNumber(): ?string { return $this->getData(self::APEN_NUMBER); }
    public function setApenNumber(?string $v): self { return $this->setData(self::APEN_NUMBER, $v); }
    public function getCountry(): ?string { return $this->getData(self::COUNTRY); }
    public function setCountry(string $v): self { return $this->setData(self::COUNTRY, $v); }
    public function getOrganisationType(): ?string { return $this->getData(self::ORGANISATION_TYPE); }
    public function setOrganisationType(?string $v): self { return $this->setData(self::ORGANISATION_TYPE, $v); }
    public function getSectorOfActivity(): ?string { return $this->getData(self::SECTOR_OF_ACTIVITY); }
    public function setSectorOfActivity(?string $v): self { return $this->setData(self::SECTOR_OF_ACTIVITY, $v); }
    public function getTaxRegistrationStatus(): ?string { return $this->getData(self::TAX_REGISTRATION_STATUS); }
    public function setTaxRegistrationStatus(?string $v): self { return $this->setData(self::TAX_REGISTRATION_STATUS, $v); }
    public function getBusinessRegistrationType(): ?string { return $this->getData(self::BUSINESS_REGISTRATION_TYPE); }
    public function setBusinessRegistrationType(?string $v): self { return $this->setData(self::BUSINESS_REGISTRATION_TYPE, $v); }
    public function getCertificateId(): ?string { return $this->getData(self::CERTIFICATE_ID); }
    public function setCertificateId(?string $v): self { return $this->setData(self::CERTIFICATE_ID, $v); }
    public function getDunsNumber(): ?string { return $this->getData(self::DUNS_NUMBER); }
    public function setDunsNumber(?string $v): self { return $this->setData(self::DUNS_NUMBER, $v); }
    public function getVerificationStatus(): string { return (string)($this->getData(self::VERIFICATION_STATUS) ?? self::STATUS_PENDING); }
    public function setVerificationStatus(string $v): self { return $this->setData(self::VERIFICATION_STATUS, $v); }
    public function getVerifiedAt(): ?string { return $this->getData(self::VERIFIED_AT); }
    public function setVerifiedAt(?string $v): self { return $this->setData(self::VERIFIED_AT, $v); }
}
