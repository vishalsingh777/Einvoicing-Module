<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Ui\Component\Listing\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
class CompanyActions extends Column
{
    public function __construct(
        ContextInterface $context, UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        array $components = [], array $data = []
    ) { parent::__construct($context, $uiComponentFactory, $components, $data); }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) return $dataSource;
        foreach ($dataSource['data']['items'] as &$item) {
            $id = $item['company_id'] ?? null;
            if (!$id) continue;
            $item[$this->getData('name')] = [
                'edit'   => ['href' => $this->urlBuilder->getUrl('einvoicing/company/edit',   ['id' => $id]), 'label' => __('Edit')],
                'delete' => ['href' => $this->urlBuilder->getUrl('einvoicing/company/delete', ['id' => $id]), 'label' => __('Delete'), 'confirm' => ['title' => __('Delete Company'), 'message' => __('Are you sure?')], 'post' => true],
            ];
        }
        return $dataSource;
    }
}
