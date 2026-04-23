<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Test\Integration\Plugin;

use Inseead\Einvoicing\Api\Data\BillingInformationInterface;
use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Inseead\Einvoicing\Model\Quote\ReadinessChecker;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 * @magentoConfigFixture current_store einvoicing/general/enabled 1
 * @magentoConfigFixture current_store einvoicing/general/b2b_verification_enabled 1
 */
class CheckoutIndexPluginTest extends TestCase
{
    private CartManagementInterface $cartManagement;
    private CartRepositoryInterface $cartRepository;
    private ReadinessChecker $readinessChecker;

    protected function setUp(): void
    {
        $om = Bootstrap::getObjectManager();
        $this->cartManagement   = $om->get(CartManagementInterface::class);
        $this->cartRepository   = $om->get(CartRepositoryInterface::class);
        $this->readinessChecker = $om->get(ReadinessChecker::class);
    }

    /** @test */
    public function quoteWithoutProfileIsNotReady(): void
    {
        $cartId = (int)$this->cartManagement->createEmptyCart();
        $quote  = $this->cartRepository->get($cartId);
        $this->assertFalse($this->readinessChecker->isReady($quote));
    }

    /** @test */
    public function b2cQuoteWithBillingAddressIsReady(): void
    {
        $cartId = (int)$this->cartManagement->createEmptyCart();
        $quote  = $this->cartRepository->get($cartId);
        $quote->setData('einv_financing_profile', BillingInformationInterface::FINANCING_PROFILE_SELF_FUNDED);
        $quote->getBillingAddress()->setFirstname('John')->setLastname('Doe')->setCountryId('FR');
        $this->cartRepository->save($quote);
        $this->assertTrue($this->readinessChecker->isReady($this->cartRepository->get($cartId)));
    }

    /** @test */
    public function b2bWithPendingVerificationIsNotReady(): void
    {
        $cartId = (int)$this->cartManagement->createEmptyCart();
        $quote  = $this->cartRepository->get($cartId);
        $quote->setData('einv_financing_profile', BillingInformationInterface::FINANCING_PROFILE_SPONSORED);
        $quote->setData('einv_verification_status', CompanyInterface::STATUS_PENDING);
        $quote->getBillingAddress()->setFirstname('Jean')->setLastname('Dupont')->setCountryId('FR');
        $this->cartRepository->save($quote);
        $this->assertFalse($this->readinessChecker->isReady($this->cartRepository->get($cartId)));
    }

    /** @test */
    public function b2bWithVerifiedStatusIsReady(): void
    {
        $cartId = (int)$this->cartManagement->createEmptyCart();
        $quote  = $this->cartRepository->get($cartId);
        $quote->setData('einv_financing_profile', BillingInformationInterface::FINANCING_PROFILE_SPONSORED);
        $quote->setData('einv_verification_status', CompanyInterface::STATUS_VERIFIED);
        $quote->getBillingAddress()->setFirstname('Jean')->setLastname('Dupont')->setCountryId('FR');
        $this->cartRepository->save($quote);
        $this->assertTrue($this->readinessChecker->isReady($this->cartRepository->get($cartId)));
    }

    /**
     * @test
     * @magentoConfigFixture current_store einvoicing/general/enabled 0
     */
    public function whenDisabledAlwaysReady(): void
    {
        $cartId = (int)$this->cartManagement->createEmptyCart();
        $quote  = $this->cartRepository->get($cartId);
        $this->assertTrue($this->readinessChecker->isReady($quote));
    }
}
