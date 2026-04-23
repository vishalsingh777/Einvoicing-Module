<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model\ResourceModel;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
class OrderSnapshot extends AbstractDb
{
    protected function _construct(): void { $this->_init('einvoicing_order_snapshot', 'snapshot_id'); }

    public function loadByOrderId(int $orderId): ?DataObject
    {
        $conn   = $this->getConnection();
        $select = $conn->select()->from($this->getMainTable())->where('order_id = ?', $orderId)->limit(1);
        $data   = $conn->fetchRow($select);
        return $data ? new DataObject($data) : null;
    }
}
