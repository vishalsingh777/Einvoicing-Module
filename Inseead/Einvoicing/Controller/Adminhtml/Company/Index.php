<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Controller\Adminhtml\Company;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class Index extends AbstractAction
{
    public function __construct(Context $context, private readonly PageFactory $pageFactory)
    { parent::__construct($context); }

    public function execute(): mixed
    {
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Inseead_Einvoicing::company');
        $page->getConfig()->getTitle()->prepend(__('Company Management'));
        return $page;
    }
}
