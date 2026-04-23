<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model;

use Inseead\Einvoicing\Api\CompanyRepositoryInterface;
use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Inseead\Einvoicing\Model\ResourceModel\Company as CompanyResource;
use Inseead\Einvoicing\Model\ResourceModel\Company\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function __construct(
        private readonly CompanyFactory    $companyFactory,
        private readonly CompanyResource   $companyResource,
        private readonly CollectionFactory $collectionFactory
    ) {}

    public function getById(int $companyId): CompanyInterface
    {
        $company = $this->companyFactory->create();
        $this->companyResource->load($company, $companyId);
        if (!$company->getCompanyId()) {
            throw new NoSuchEntityException(__("Company with ID \"%1\" does not exist.", $companyId));
        }
        return $company;
    }

    public function getByVatAndCountry(string $vatNumber, string $country): CompanyInterface
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('vat_number', ['eq' => $vatNumber])
                   ->addFieldToFilter('country', ['eq' => strtoupper($country)])
                   ->setPageSize(1);
        $company = $collection->getFirstItem();
        if (!$company->getCompanyId()) {
            throw new NoSuchEntityException(__("No company found for VAT \"%1\" in country \"%2\".", $vatNumber, $country));
        }
        return $company;
    }

    public function getBySiret(string $siret): CompanyInterface
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('siret', ['eq' => $siret])->setPageSize(1);
        $company = $collection->getFirstItem();
        if (!$company->getCompanyId()) {
            throw new NoSuchEntityException(__("No company found for SIRET \"%1\".", $siret));
        }
        return $company;
    }

    public function save(CompanyInterface $company): CompanyInterface
    {
        try {
            $this->companyResource->save($company);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__("Could not save company: %1", $e->getMessage()), $e);
        }
        return $company;
    }

    public function findOrCreate(string $vatNumber, string $country): CompanyInterface
    {
        try {
            return $this->getByVatAndCountry($vatNumber, $country);
        } catch (NoSuchEntityException) {
            $company = $this->companyFactory->create();
            $company->setVatNumber($vatNumber)->setCountry(strtoupper($country))
                    ->setVerificationStatus(CompanyInterface::STATUS_PENDING);
            return $company;
        }
    }

    public function findOrCreateBySiret(string $siret): CompanyInterface
    {
        try {
            return $this->getBySiret($siret);
        } catch (NoSuchEntityException) {
            $company = $this->companyFactory->create();
            $company->setSiret($siret)->setSiren(substr($siret, 0, 9))
                    ->setCountry('FR')->setVerificationStatus(CompanyInterface::STATUS_PENDING);
            return $company;
        }
    }
}
