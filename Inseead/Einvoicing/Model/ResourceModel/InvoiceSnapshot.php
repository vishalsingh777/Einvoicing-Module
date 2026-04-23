<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model\ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
class InvoiceSnapshot extends AbstractDb
{
    protected function _construct(): void { $this->_init('einvoicing_invoice_snapshot', 'snapshot_id'); }
}
