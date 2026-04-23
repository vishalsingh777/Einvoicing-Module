<?php
declare(strict_types=1);
namespace Inseead\Einvoicing\Controller\Frontend\Verify;

use Inseead\Einvoicing\Model\CompanyFactory;
use Inseead\Einvoicing\Model\Verification\VerifierPool;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;

class Company implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public function __construct(
        private readonly JsonFactory      $jsonFactory,
        private readonly RequestInterface $request,
        private readonly VerifierPool     $verifierPool,
        private readonly CompanyFactory   $companyFactory,
        private readonly LoggerInterface  $logger
    ) {}

    public function execute(): mixed
    {
        $result = $this->jsonFactory->create();

        if (!$this->request->isPost() || !$this->request->isAjax()) {
            return $result->setData(['success' => false, 'message' => (string)__('Invalid request.')]);
        }

        $siret   = trim((string)$this->request->getPost('siret', ''));
        $vat     = trim((string)$this->request->getPost('vat_number', ''));
        $country = strtoupper(trim((string)$this->request->getPost('country', 'FR')));

        try {
            $company = $this->companyFactory->create();
            $company->setCountry($country);

            if ($country === 'FR' && $siret) {
                if (strlen($siret) !== 14 || !ctype_digit($siret)) {
                    return $result->setData(['success' => false, 'message' => (string)__('SIRET must be exactly 14 digits.')]);
                }
                $company->setSiret($siret)->setSiren(substr($siret, 0, 9));
            } elseif ($vat) {
                $company->setVatNumber($vat);
            } else {
                return $result->setData(['success' => false, 'message' => (string)__('Please provide a SIRET or VAT number.')]);
            }

            if (!$this->verifierPool->hasVerifierFor($country)) {
                return $result->setData(['success' => false, 'message' => (string)__('Verification not available for country "%1".', $country)]);
            }

            $verifier = $this->verifierPool->getForCountry($country);
            if (!$verifier->verify($company)) {
                return $result->setData(['success' => false, 'message' => $verifier->getLastError() ?? (string)__('Verification failed.')]);
            }

            return $result->setData([
                'success'      => true,
                'company_name' => $company->getCompanyName(),
                'siren'        => $company->getSiren(),
                'apen_number'  => $company->getApenNumber(),
                'message'      => (string)__('Company verified successfully.'),
            ]);

        } catch (\Throwable $e) {
            $this->logger->error('[Einvoicing] Verify error: ' . $e->getMessage());
            return $result->setData(['success' => false, 'message' => (string)__('Verification service error.')]);
        }
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException { return null; }
    public function validateForCsrf(RequestInterface $request): ?bool { return true; }
}
