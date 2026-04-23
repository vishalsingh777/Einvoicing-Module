<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Controller\Adminhtml\Company;
use Inseead\Einvoicing\Api\CompanyRepositoryInterface;
use Inseead\Einvoicing\Model\ResourceModel\Company as CompanyResource;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
class Delete extends AbstractAction
{
    public function __construct(
        Context $context,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CompanyResource $companyResource
    ) { parent::__construct($context); }

    public function execute(): mixed
    {
        $redirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('id');
        if (!$id) { $this->messageManager->addErrorMessage(__("Can't find a company to delete.")); return $redirect->setPath('*/*/'); }
        try {
            $company = $this->companyRepository->getById($id);
            $this->companyResource->delete($company);
            $this->messageManager->addSuccessMessage(__('Company has been deleted.'));
        } catch (NoSuchEntityException) {
            $this->messageManager->addErrorMessage(__('Company no longer exists.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error: %1', $e->getMessage()));
        }
        return $redirect->setPath('*/*/');
    }
}
