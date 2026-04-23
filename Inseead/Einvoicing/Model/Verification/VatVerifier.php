<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model\Verification;

use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Inseead\Einvoicing\Api\VerifierInterface;
use Magento\Customer\Model\Vat;
use Psr\Log\LoggerInterface;

class VatVerifier implements VerifierInterface
{
    private const EU_COUNTRIES = [
        'AT','BE','BG','CY','CZ','DE','DK','EE','EL','ES',
        'FI','GB','HR','HU','IE','IT','LT','LU','LV','MT',
        'NL','PL','PT','RO','SE','SI','SK'
    ];

    private ?string $lastError = null;

    public function __construct(
        private readonly Vat             $vatValidator,
        private readonly LoggerInterface $logger
    ) {}

    public function supports(string $countryCode): bool
    {
        return in_array(strtoupper($countryCode), self::EU_COUNTRIES, true);
    }

    public function getLastError(): ?string { return $this->lastError; }

    public function verify(CompanyInterface $company): bool
    {
        $this->lastError = null;
        $vatNumber = $company->getVatNumber();
        $country   = strtoupper((string)$company->getCountry());

        if (!$vatNumber) {
            $this->lastError = (string)__("VAT Intracommunity Number is required for EU company verification.");
            return false;
        }

        $cleanVat = $this->stripPrefix($vatNumber, $country);

        try {
            $result = $this->vatValidator->checkVatNumber($country, $cleanVat);
            if (!$result->getIsValid()) {
                $this->lastError = (string)__("VAT number \"%1\" could not be verified for country \"%2\".", $vatNumber, $country);
                $company->setVerificationStatus(CompanyInterface::STATUS_FAILED);
                return false;
            }
            $company->setVerificationStatus(CompanyInterface::STATUS_VERIFIED)->setVerifiedAt(date('Y-m-d H:i:s'));
            return true;
        } catch (\Throwable $e) {
            $this->logger->error('[Einvoicing] VIES error: ' . $e->getMessage());
            $this->lastError = (string)__("VAT validation service is temporarily unavailable.");
            return false;
        }
    }

    private function stripPrefix(string $vat, string $country): string
    {
        $v = strtoupper(trim($vat));
        return str_starts_with($v, $country) ? substr($v, strlen($country)) : $v;
    }
}
