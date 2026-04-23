<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Block\Adminhtml\Company\Edit;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
class DeleteButton implements ButtonProviderInterface
{
    public function __construct(private readonly Context $context) {}
    public function getButtonData(): array {
        $id = (int)$this->context->getRequest()->getParam('id');
        if (!$id) return [];
        $url = $this->context->getUrlBuilder()->getUrl('einvoicing/company/delete', ['id' => $id]);
        return ['label' => __('Delete'), 'class' => 'delete', 'sort_order' => 20, 'on_click' => sprintf("deleteConfirm('%s','%s',{data:{}})", __('Are you sure?'), $url)];
    }
}
