<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Api;

use Inseead\Einvoicing\Api\Data\CompanyInterface;

interface VerifierInterface
{
    public function verify(CompanyInterface $company): bool;
    public function supports(string $countryCode): bool;
    public function getLastError(): ?string;
}
