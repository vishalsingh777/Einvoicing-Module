<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model\ResourceModel\Company;
use Inseead\Einvoicing\Model\Company;
use Inseead\Einvoicing\Model\ResourceModel\Company as CompanyResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
class Collection extends AbstractCollection
{
    protected function _construct(): void { $this->_init(Company::class, CompanyResource::class); }
}
