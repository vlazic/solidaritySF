<?php
namespace App\Service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use InvalidArgumentException;

class IpsQrCodeService
{
    /**
     * Generate the IPS QR string for Serbian payments.
     * @throws InvalidArgumentException if required fields are missing
     */
    public function createIpsQrString(array $data): string
    {
        $required = [
            'identificationCode',
            'version',
            'characterSet',
            'bankAccountNumber',
            'payeeName',
            'amount',
        ];

        foreach ($required as $key) {
            if (empty($data[$key])) {
                throw new InvalidArgumentException("Missing required field: $key");
            }
        }

        $segments = [
            "K:" . $data['identificationCode'],
            "V:" . $data['version'],
            "C:" . $data['characterSet'],
            "R:" . $data['bankAccountNumber'],
            "N:" . $data['payeeName'],
            "I:RSD" . $data['amount'],
        ];

        if (! empty($data['payerName'])) {
            $segments[] = "P:" . $data['payerName'];
        }
        if (! empty($data['paymentCode'])) {
            $segments[] = "SF:" . $data['paymentCode'];
        }
        if (! empty($data['paymentPurpose'])) {
            $segments[] = "S:" . $data['paymentPurpose'];
        }
        if (! empty($data['referenceCode'])) {
            $segments[] = "RO:" . $data['referenceCode'];
        }

        return implode('|', $segments);
    }

    /**
     * Generate a QR code PNG as a data URI for embedding in HTML.
     */
    public function getQrCodeDataUri(string $qrString): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            data: $qrString,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 320,
            margin: 0,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        $result = $builder->build();

        return $result->getDataUri();
    }
}
