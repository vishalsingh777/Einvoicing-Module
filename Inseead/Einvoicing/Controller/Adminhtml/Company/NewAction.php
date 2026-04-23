<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Controller\Adminhtml\Company;
class NewAction extends AbstractAction
{
    public function execute(): mixed
    {
        return $this->resultRedirectFactory->create()->setPath('*/*/edit');
    }
}
