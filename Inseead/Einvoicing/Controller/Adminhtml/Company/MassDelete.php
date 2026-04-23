<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Controller\Adminhtml\Company;
use Inseead\Einvoicing\Model\ResourceModel\Company as CompanyResource;
use Inseead\Einvoicing\Model\ResourceModel\Company\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
class MassDelete extends AbstractAction
{
    public function __construct(
        Context $context,
        private readonly Filter $filter,
        private readonly CollectionFactory $collectionFactory,
        private readonly CompanyResource $companyResource
    ) { parent::__construct($context); }

    public function execute(): mixed
    {
        $redirect = $this->resultRedirectFactory->create();
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $count = 0;
            foreach ($collection->getItems() as $company) { $this->companyResource->delete($company); $count++; }
            $this->messageManager->addSuccessMessage(__('Deleted %1 company record(s).', $count));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $redirect->setPath('*/*/');
    }
}
