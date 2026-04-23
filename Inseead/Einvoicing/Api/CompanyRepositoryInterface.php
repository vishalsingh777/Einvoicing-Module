<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Api;

use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface CompanyRepositoryInterface
{
    /** @throws NoSuchEntityException */
    public function getById(int $companyId): CompanyInterface;
    /** @throws NoSuchEntityException */
    public function getByVatAndCountry(string $vatNumber, string $country): CompanyInterface;
    /** @throws NoSuchEntityException */
    public function getBySiret(string $siret): CompanyInterface;
    /** @throws CouldNotSaveException */
    public function save(CompanyInterface $company): CompanyInterface;
    public function findOrCreate(string $vatNumber, string $country): CompanyInterface;
    public function findOrCreateBySiret(string $siret): CompanyInterface;
}
