<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Test\Integration\Model\Verification;

use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Inseead\Einvoicing\Model\CompanyFactory;
use Inseead\Einvoicing\Model\Verification\InseeVerifier;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/** @magentoAppIsolation enabled */
class InseeVerifierTest extends TestCase
{
    private InseeVerifier $verifier;
    private CompanyFactory $companyFactory;

    protected function setUp(): void
    {
        $om = Bootstrap::getObjectManager();
        $this->verifier       = $om->get(InseeVerifier::class);
        $this->companyFactory = $om->get(CompanyFactory::class);
    }

    /** @test */
    public function supportsOnlyFrance(): void
    {
        $this->assertTrue($this->verifier->supports('FR'));
        $this->assertFalse($this->verifier->supports('DE'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store einvoicing/insee/api_url
     */
    public function stubModeSucceedsForValid14DigitSiret(): void
    {
        $company = $this->companyFactory->create();
        $company->setCountry('FR')->setSiret('73282932000074');
        $this->assertTrue($this->verifier->verify($company));
        $this->assertEquals('732829320', $company->getSiren());
        $this->assertEquals(CompanyInterface::STATUS_VERIFIED, $company->getVerificationStatus());
    }

    /** @test */
    public function rejectsShortSiret(): void
    {
        $company = $this->companyFactory->create();
        $company->setCountry('FR')->setSiret('1234');
        $this->assertFalse($this->verifier->verify($company));
        $this->assertStringContainsString('14', $this->verifier->getLastError());
    }

    /** @test */
    public function rejectsNonNumericSiret(): void
    {
        $company = $this->companyFactory->create();
        $company->setCountry('FR')->setSiret('ABCDEFGHIJKLMN');
        $this->assertFalse($this->verifier->verify($company));
    }
}
