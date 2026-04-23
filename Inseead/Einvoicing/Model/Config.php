<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_ENABLED          = 'einvoicing/general/enabled';
    private const XML_B2B_VERIFY       = 'einvoicing/general/b2b_verification_enabled';
    private const XML_INSEE_URL        = 'einvoicing/insee/api_url';
    private const XML_INSEE_KEY        = 'einvoicing/insee/api_key';
    private const XML_INSEE_TIMEOUT    = 'einvoicing/insee/timeout';
    private const XML_USE_MAGENTO_VAT  = 'einvoicing/eu_vat/use_magento_vat';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface   $encryptor
    ) {}

    public function isEnabled(?string $scope = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_ENABLED, ScopeInterface::SCOPE_STORE, $scope);
    }

    public function isB2bVerificationEnabled(?string $scope = null): bool
    {
        return $this->isEnabled($scope)
            && $this->scopeConfig->isSetFlag(self::XML_B2B_VERIFY, ScopeInterface::SCOPE_STORE, $scope);
    }

    public function getInseeApiUrl(?string $scope = null): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_INSEE_URL, ScopeInterface::SCOPE_STORE, $scope);
    }

    public function getInseeApiKey(?string $scope = null): string
    {
        $enc = (string)$this->scopeConfig->getValue(self::XML_INSEE_KEY, ScopeInterface::SCOPE_STORE, $scope);
        return $enc ? $this->encryptor->decrypt($enc) : '';
    }

    public function getInseeTimeout(?string $scope = null): int
    {
        return (int)($this->scopeConfig->getValue(self::XML_INSEE_TIMEOUT, ScopeInterface::SCOPE_STORE, $scope) ?: 10);
    }
}
