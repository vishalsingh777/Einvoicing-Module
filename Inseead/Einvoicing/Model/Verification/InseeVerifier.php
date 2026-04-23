<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model\Verification;

use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Inseead\Einvoicing\Api\VerifierInterface;
use Inseead\Einvoicing\Model\Config;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

class InseeVerifier implements VerifierInterface
{
    private ?string $lastError = null;

    public function __construct(
        private readonly Config              $config,
        private readonly Curl               $curl,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface    $logger
    ) {}

    public function supports(string $countryCode): bool { return strtoupper($countryCode) === 'FR'; }
    public function getLastError(): ?string { return $this->lastError; }

    public function verify(CompanyInterface $company): bool
    {
        $this->lastError = null;
        $siret = $company->getSiret();

        if (!$siret || strlen($siret) !== 14 || !ctype_digit($siret)) {
            $this->lastError = (string)__("Invalid SIRET number. Must be exactly 14 digits.");
            return false;
        }

        $company->setSiren(substr($siret, 0, 9));

        try {
            $data = $this->callApi($siret);
            if ($data === null) {
                $this->lastError = (string)__("SIRET \"%1\" could not be validated. Please check the number.", $siret);
                return false;
            }
            $this->enrichCompany($company, $data);
            $company->setVerificationStatus(CompanyInterface::STATUS_VERIFIED)->setVerifiedAt(date('Y-m-d H:i:s'));
            return true;
        } catch (\Throwable $e) {
            $this->logger->error('[Einvoicing] INSEE error: ' . $e->getMessage());
            $this->lastError = (string)__("Company verification service is temporarily unavailable.");
            return false;
        }
    }

    private function callApi(string $siret): ?array
    {
        $baseUrl = rtrim($this->config->getInseeApiUrl(), '/');
        if (!$baseUrl) {
            $this->logger->warning('[Einvoicing] INSEE URL not configured — stub mode.');
            return $this->stubResponse($siret);
        }
        $this->curl->setTimeout($this->config->getInseeTimeout());
        $this->curl->addHeader('Authorization', 'Bearer ' . $this->config->getInseeApiKey());
        $this->curl->addHeader('Accept', 'application/json');
        $this->curl->get(sprintf('%s/siret/%s', $baseUrl, $siret));
        $status = $this->curl->getStatus();
        if ($status === 404) return null;
        if ($status !== 200) throw new \RuntimeException(sprintf('INSEE returned HTTP %d', $status));
        $decoded = $this->serializer->unserialize($this->curl->getBody());
        return $decoded['etablissement'] ?? null;
    }

    private function enrichCompany(CompanyInterface $company, array $data): void
    {
        $ul = $data['uniteLegale'] ?? [];
        $name = $ul['denominationUniteLegale']
            ?? trim(($ul['prenom1UniteLegale'] ?? '') . ' ' . ($ul['nomUniteLegale'] ?? ''))
            ?: null;
        if ($name && !$company->getCompanyName()) $company->setCompanyName($name);
        $apen = $ul['activitePrincipaleUniteLegale'] ?? $data['activitePrincipaleEtablissement'] ?? null;
        if ($apen) $company->setApenNumber(str_replace('.', '', $apen));
    }

    private function stubResponse(string $siret): array
    {
        return [
            'uniteLegale' => [
                'denominationUniteLegale' => 'STUB COMPANY SAS',
                'activitePrincipaleUniteLegale' => '85.42Z',
            ],
        ];
    }
}
