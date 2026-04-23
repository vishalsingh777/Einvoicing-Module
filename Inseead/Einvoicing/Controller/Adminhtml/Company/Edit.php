<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Controller\Adminhtml\Company;
use Inseead\Einvoicing\Api\CompanyRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
class Edit extends AbstractAction
{
    public function __construct(
        Context $context,
        private readonly PageFactory $pageFactory,
        private readonly CompanyRepositoryInterface $companyRepository
    ) { parent::__construct($context); }

    public function execute(): mixed
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try { $this->companyRepository->getById($id); }
            catch (NoSuchEntityException) {
                $this->messageManager->addErrorMessage(__('This company no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Inseead_Einvoicing::company');
        $page->getConfig()->getTitle()->prepend($id ? __('Edit Company') : __('New Company'));
        return $page;
    }
}
