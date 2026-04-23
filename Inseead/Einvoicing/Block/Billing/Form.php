<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Block\Billing;

use Inseead\Einvoicing\Api\Data\BillingInformationInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Directory\Model\Config\Source\Country as CountrySource;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Form extends Template
{
    public function __construct(
        Context $context,
        private readonly CheckoutSession $checkoutSession,
        private readonly CountrySource   $countrySource,
        array $data = []
    ) { parent::__construct($context, $data); }

    public function getQuote(): \Magento\Quote\Api\Data\CartInterface { return $this->checkoutSession->getQuote(); }
    public function getSaveUrl(): string   { return $this->getUrl('einvoicing/billing/save'); }
    public function getVerifyUrl(): string { return $this->getUrl('einvoicing/verify/company'); }
    public function getBackUrl(): string   { return $this->getUrl('checkout/cart'); }
    public function getCheckoutUrl(): string { return $this->getUrl('checkout'); }

    public function getCountryOptions(): array { return $this->countrySource->toOptionArray(false); }
    public function getCurrentFinancingProfile(): string { return (string)($this->getQuote()->getData('einv_financing_profile') ?? ''); }
    public function getCurrentCountry(): string { $b = $this->getQuote()->getBillingAddress(); return $b ? (string)$b->getCountryId() : ''; }
    public function getQuoteField(string $field): string { return (string)($this->getQuote()->getData('einv_' . $field) ?? ''); }

    public function getSectorOptions(): array {
        return [
            ['value' => '',            'label' => __('Select an Option')],
            ['value' => 'education',   'label' => __('Education')],
            ['value' => 'healthcare',  'label' => __('Healthcare')],
            ['value' => 'finance',     'label' => __('Finance & Banking')],
            ['value' => 'technology',  'label' => __('Technology')],
            ['value' => 'manufacturing','label' => __('Manufacturing')],
            ['value' => 'retail',      'label' => __('Retail')],
            ['value' => 'government',  'label' => __('Government & Public Sector')],
            ['value' => 'nonprofit',   'label' => __('Non-profit')],
            ['value' => 'other',       'label' => __('Other')],
        ];
    }

    public function getOrganisationTypeOptions(): array {
        return [
            ['value' => '',      'label' => __('Select an Option')],
            ['value' => 'sa',    'label' => __('SA – Société Anonyme')],
            ['value' => 'sas',   'label' => __('SAS – Société par Actions Simplifiée')],
            ['value' => 'sarl',  'label' => __('SARL – Société à Responsabilité Limitée')],
            ['value' => 'ei',    'label' => __('EI – Entreprise Individuelle')],
            ['value' => 'assoc', 'label' => __('Association')],
            ['value' => 'other', 'label' => __('Other')],
        ];
    }

    public function getTaxRegistrationStatusOptions(): array {
        return [
            ['value' => BillingInformationInterface::TAX_STATUS_REGISTERED,    'label' => __('Tax Registered')],
            ['value' => BillingInformationInterface::TAX_STATUS_NOT_REGISTERED, 'label' => __('Not Tax Registered')],
            ['value' => BillingInformationInterface::TAX_STATUS_EXEMPT,         'label' => __('Tax Exempt')],
        ];
    }
}
