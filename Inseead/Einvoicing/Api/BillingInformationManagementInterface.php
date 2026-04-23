<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Api;

use Inseead\Einvoicing\Api\Data\BillingInformationInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

interface BillingInformationManagementInterface
{
    /**
     * @return array{success: bool, redirect: string, message: string}
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function save(int $cartId, BillingInformationInterface $billingInformation): array;
}
