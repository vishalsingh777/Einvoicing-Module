<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model\Quote;

use Inseead\Einvoicing\Api\Data\BillingInformationInterface;
use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Inseead\Einvoicing\Model\Config;
use Magento\Quote\Api\Data\CartInterface;

class ReadinessChecker
{
    public function __construct(private readonly Config $config) {}

    public function isReady(CartInterface $quote): bool
    {
        if (!$this->config->isEnabled()) return true;

        $billing = $quote->getBillingAddress();
        if (!$billing || !$billing->getFirstname()) return false;

        $profile = $quote->getData('einv_financing_profile');
        if (!$profile) return false;

        if ($profile === BillingInformationInterface::FINANCING_PROFILE_SPONSORED
            && $this->config->isB2bVerificationEnabled()
        ) {
            if ($quote->getData('einv_verification_status') !== CompanyInterface::STATUS_VERIFIED) return false;
        }

        return true;
    }

    public function getBillingUrl(): string { return 'einvoicing/billing/index'; }
}
