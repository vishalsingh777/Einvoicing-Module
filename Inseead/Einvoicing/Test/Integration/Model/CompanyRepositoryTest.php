<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Test\Integration\Model;

use Inseead\Einvoicing\Api\CompanyRepositoryInterface;
use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Inseead\Einvoicing\Model\CompanyFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CompanyRepositoryTest extends TestCase
{
    private CompanyRepositoryInterface $repository;
    private CompanyFactory $companyFactory;

    protected function setUp(): void
    {
        $om = Bootstrap::getObjectManager();
        $this->repository    = $om->get(CompanyRepositoryInterface::class);
        $this->companyFactory = $om->get(CompanyFactory::class);
    }

    /** @test */
    public function saveAndGetById(): void
    {
        $company = $this->companyFactory->create();
        $company->setCompanyName('Test SAS')->setCountry('FR')
                ->setSiret('73282932000074')->setSiren('732829320')
                ->setVerificationStatus(CompanyInterface::STATUS_PENDING);
        $saved = $this->repository->save($company);
        $this->assertGreaterThan(0, $saved->getCompanyId());
        $loaded = $this->repository->getById($saved->getCompanyId());
        $this->assertEquals('Test SAS', $loaded->getCompanyName());
        $this->assertEquals('73282932000074', $loaded->getSiret());
    }

    /** @test */
    public function getByIdThrowsForMissing(): void
    {
        $this->expectException(NoSuchEntityException::class);
        $this->repository->getById(999999);
    }

    /** @test */
    public function getByVatAndCountry(): void
    {
        $company = $this->companyFactory->create();
        $company->setCompanyName('EU GmbH')->setCountry('DE')
                ->setVatNumber('DE123456789')->setVerificationStatus(CompanyInterface::STATUS_VERIFIED);
        $this->repository->save($company);
        $found = $this->repository->getByVatAndCountry('DE123456789', 'DE');
        $this->assertEquals('EU GmbH', $found->getCompanyName());
    }

    /** @test */
    public function findOrCreateBySiretExtractsSiren(): void
    {
        $company = $this->repository->findOrCreateBySiret('73282932000074');
        $this->assertNull($company->getCompanyId());
        $this->assertEquals('732829320', $company->getSiren());
        $this->assertEquals('FR', $company->getCountry());
    }

    /** @test */
    public function vatCountryUniqueConstraint(): void
    {
        $this->expectException(CouldNotSaveException::class);
        $c1 = $this->companyFactory->create();
        $c1->setCompanyName('A')->setCountry('DE')->setVatNumber('DE111111111')->setVerificationStatus(CompanyInterface::STATUS_PENDING);
        $this->repository->save($c1);
        $c2 = $this->companyFactory->create();
        $c2->setCompanyName('B')->setCountry('DE')->setVatNumber('DE111111111')->setVerificationStatus(CompanyInterface::STATUS_PENDING);
        $this->repository->save($c2);
    }
}
