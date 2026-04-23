<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Controller\Frontend\Billing;

use Inseead\Einvoicing\Model\Config;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;

class Index implements HttpGetActionInterface
{
    public function __construct(
        private readonly PageFactory      $pageFactory,
        private readonly RedirectFactory  $redirectFactory,
        private readonly CheckoutSession  $checkoutSession,
        private readonly Config           $config,
        private readonly RequestInterface $request
    ) {}

    public function execute(): mixed
    {
        if (!$this->config->isEnabled()) {
            return $this->redirectFactory->create()->setPath('checkout');
        }

        $quote = $this->checkoutSession->getQuote();
        if (!$quote || !$quote->hasItems()) {
            return $this->redirectFactory->create()->setPath('checkout/cart');
        }

        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set(__('Billing Information'));
        return $page;
    }
}
