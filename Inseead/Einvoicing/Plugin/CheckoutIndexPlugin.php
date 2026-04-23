<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Plugin;

use Inseead\Einvoicing\Model\Config;
use Inseead\Einvoicing\Model\Quote\ReadinessChecker;
use Magento\Checkout\Controller\Index\Index as CheckoutIndex;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Controller\Result\RedirectFactory;

class CheckoutIndexPlugin
{
    public function __construct(
        private readonly Config           $config,
        private readonly CheckoutSession  $checkoutSession,
        private readonly ReadinessChecker $readinessChecker,
        private readonly RedirectFactory  $redirectFactory
    ) {}

    public function aroundExecute(CheckoutIndex $subject, callable $proceed): mixed
    {
        if (!$this->config->isEnabled()) return $proceed();

        $quote = $this->checkoutSession->getQuote();
        if (!$this->readinessChecker->isReady($quote)) {
            return $this->redirectFactory->create()->setPath($this->readinessChecker->getBillingUrl());
        }

        return $proceed();
    }
}
