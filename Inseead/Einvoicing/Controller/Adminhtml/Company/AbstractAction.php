<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Controller\Adminhtml\Company;
use Magento\Backend\App\Action;
abstract class AbstractAction extends Action
{
    public const ADMIN_RESOURCE = 'Inseead_Einvoicing::company';
}
