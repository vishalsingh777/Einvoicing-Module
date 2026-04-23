<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Plugin;

use Ewave\InseadIntegration\Controller\Quote\Populate;
use Inseead\Einvoicing\Model\Config;
use Inseead\Einvoicing\Model\Quote\ReadinessChecker;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\ResponseInterface;

class PopulateControllerPlugin
{
    public function __construct(
        private readonly Config           $config,
        private readonly CheckoutSession  $checkoutSession,
        private readonly ReadinessChecker $readinessChecker
    ) {}

    public function afterExecute(Populate $subject, ResponseInterface $result): ResponseInterface
    {
        if (!$this->config->isEnabled()) return $result;

        try {
            $quote = $this->checkoutSession->getQuote();
            if (!$this->readinessChecker->isReady($quote)) {
                $result->setRedirect('/' . str_replace('/', '/', $this->readinessChecker->getBillingUrl()));
            }
        } catch (\Throwable) {}

        return $result;
    }
}
