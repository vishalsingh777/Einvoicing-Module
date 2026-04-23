<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Ui\Component\Listing\Column;
use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Magento\Framework\Data\OptionSourceInterface;
class VerificationStatus implements OptionSourceInterface
{
    public function toOptionArray(): array {
        return [
            ['value' => CompanyInterface::STATUS_PENDING,  'label' => __('Pending')],
            ['value' => CompanyInterface::STATUS_VERIFIED, 'label' => __('Verified')],
            ['value' => CompanyInterface::STATUS_FAILED,   'label' => __('Failed')],
        ];
    }
}
