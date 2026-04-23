<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model\Verification;

use Inseead\Einvoicing\Api\VerifierInterface;
use Magento\Framework\Exception\LocalizedException;

class VerifierPool
{
    public function __construct(private readonly array $verifiers = []) {}

    public function getForCountry(string $countryCode): VerifierInterface
    {
        foreach ($this->verifiers as $verifier) {
            if ($verifier->supports($countryCode)) return $verifier;
        }
        throw new LocalizedException(__("No company verifier configured for country \"%1\".", $countryCode));
    }

    public function hasVerifierFor(string $countryCode): bool
    {
        foreach ($this->verifiers as $verifier) {
            if ($verifier->supports($countryCode)) return true;
        }
        return false;
    }
}
