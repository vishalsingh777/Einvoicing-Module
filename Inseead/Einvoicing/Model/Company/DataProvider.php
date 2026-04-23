<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Model\Company;

use Inseead\Einvoicing\Model\ResourceModel\Company\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private array $loadedData = [];

    public function __construct(
        string $name, string $primaryFieldName, string $requestFieldName,
        CollectionFactory $collectionFactory,
        private readonly DataPersistorInterface $dataPersistor,
        array $meta = [], array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    public function getData(): array
    {
        if ($this->loadedData) return $this->loadedData;
        foreach ($this->collection->getItems() as $company) {
            $this->loadedData[$company->getCompanyId()] = $company->getData();
        }
        $persisted = $this->dataPersistor->get('einvoicing_company');
        if (!empty($persisted)) {
            $id = $persisted['company_id'] ?? null;
            $this->loadedData[$id] = $persisted;
            $this->dataPersistor->clear('einvoicing_company');
        }
        return $this->loadedData;
    }
}
