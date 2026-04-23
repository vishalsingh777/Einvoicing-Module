<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Controller\Adminhtml\Company;
use Inseead\Einvoicing\Api\CompanyRepositoryInterface;
use Inseead\Einvoicing\Model\CompanyFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
class Save extends AbstractAction
{
    public function __construct(
        Context $context,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CompanyFactory $companyFactory,
        private readonly DataPersistorInterface $dataPersistor
    ) { parent::__construct($context); }

    public function execute(): mixed
    {
        $redirect = $this->resultRedirectFactory->create();
        $data     = $this->getRequest()->getPostValue();
        if (!$data) return $redirect->setPath('*/*/');

        $id = (int)($data['company_id'] ?? 0);
        try {
            $company = $id ? $this->companyRepository->getById($id) : $this->companyFactory->create();
            $company->setData(array_merge($company->getData(), $data));
            $this->companyRepository->save($company);
            $this->messageManager->addSuccessMessage(__('Company has been saved.'));
            $this->dataPersistor->clear('einvoicing_company');
            if ($this->getRequest()->getParam('back')) {
                return $redirect->setPath('*/*/edit', ['id' => $company->getCompanyId()]);
            }
            return $redirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving.'));
        }
        $this->dataPersistor->set('einvoicing_company', $data);
        return $redirect->setPath('*/*/edit', ['id' => $id]);
    }
}
