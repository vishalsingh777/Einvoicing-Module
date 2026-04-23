<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Plugin;

use Inseead\Einvoicing\Api\Data\BillingInformationInterface;
use Inseead\Einvoicing\Api\Data\CompanyInterface;
use Inseead\Einvoicing\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class OrderValidationPlugin
{
    public function __construct(
        private readonly Config                  $config,
        private readonly CartRepositoryInterface $cartRepository
    ) {}

    public function beforePlaceOrder(CartManagementInterface $subject, int $cartId, mixed $paymentMethod = null): array
    {
        if (!$this->config->isB2bVerificationEnabled()) {
            return $paymentMethod !== null ? [$cartId, $paymentMethod] : [$cartId];
        }

        try {
            $quote   = $this->cartRepository->get($cartId);
            $profile = $quote->getData('einv_financing_profile');

            if ($profile === BillingInformationInterface::FINANCING_PROFILE_SPONSORED) {
                if ($quote->getData('einv_verification_status') !== CompanyInterface::STATUS_VERIFIED) {
                    throw new LocalizedException(
                        __('Your company has not been verified. Please complete the billing information step before placing your order.')
                    );
                }
            }
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Throwable) {}

        return $paymentMethod !== null ? [$cartId, $paymentMethod] : [$cartId];
    }
}
