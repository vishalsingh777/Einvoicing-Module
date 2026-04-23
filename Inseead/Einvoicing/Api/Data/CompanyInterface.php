<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Api\Data;

interface CompanyInterface
{
    public const COMPANY_ID = 'company_id';
    public const COMPANY_NAME = 'company_name';
    public const COMMERCIAL_NAME = 'commercial_name';
    public const VAT_NUMBER = 'vat_number';
    public const SIREN = 'siren';
    public const SIRET = 'siret';
    public const APEN_NUMBER = 'apen_number';
    public const COUNTRY = 'country';
    public const ORGANISATION_TYPE = 'organisation_type';
    public const SECTOR_OF_ACTIVITY = 'sector_of_activity';
    public const TAX_REGISTRATION_STATUS = 'tax_registration_status';
    public const BUSINESS_REGISTRATION_TYPE = 'business_registration_type';
    public const CERTIFICATE_ID = 'certificate_id';
    public const DUNS_NUMBER = 'duns_number';
    public const VERIFICATION_STATUS = 'verification_status';
    public const VERIFIED_AT = 'verified_at';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_PENDING  = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_FAILED   = 'failed';

    public function getCompanyId(): ?int;
    public function setCompanyId(int $companyId): self;
    public function getCompanyName(): ?string;
    public function setCompanyName(string $name): self;
    public function getCommercialName(): ?string;
    public function setCommercialName(?string $name): self;
    public function getVatNumber(): ?string;
    public function setVatNumber(?string $vatNumber): self;
    public function getSiren(): ?string;
    public function setSiren(?string $siren): self;
    public function getSiret(): ?string;
    public function setSiret(?string $siret): self;
    public function getApenNumber(): ?string;
    public function setApenNumber(?string $apen): self;
    public function getCountry(): ?string;
    public function setCountry(string $country): self;
    public function getOrganisationType(): ?string;
    public function setOrganisationType(?string $type): self;
    public function getSectorOfActivity(): ?string;
    public function setSectorOfActivity(?string $sector): self;
    public function getTaxRegistrationStatus(): ?string;
    public function setTaxRegistrationStatus(?string $status): self;
    public function getBusinessRegistrationType(): ?string;
    public function setBusinessRegistrationType(?string $type): self;
    public function getCertificateId(): ?string;
    public function setCertificateId(?string $id): self;
    public function getDunsNumber(): ?string;
    public function setDunsNumber(?string $duns): self;
    public function getVerificationStatus(): string;
    public function setVerificationStatus(string $status): self;
    public function getVerifiedAt(): ?string;
    public function setVerifiedAt(?string $date): self;
}
