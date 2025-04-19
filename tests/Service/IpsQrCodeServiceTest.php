<?php
namespace App\Tests\Service;

use App\Service\IpsQrCodeService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

// Adapted from: https://github.com/ArtBIT/ips-qr-code/blob/master/lib/ips.test.js

class IpsQrCodeServiceTest extends TestCase
{
    private IpsQrCodeService $service;

    protected function setUp(): void
    {
        $this->service = new IpsQrCodeService();
    }

    public function testThrowsOnMissingRequiredArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->createIpsQrString([]);
    }

    public function testWorksWithRequiredArguments(): void
    {
        $args = [
            'identificationCode' => 'PR',
            'version'            => '01',
            'characterSet'       => '1',
            'bankAccountNumber'  => '123456789012345611',
            'payeeName'          => 'JEST Ltd., Test',
            'amount'             => '1295,',
        ];
        $expected = 'K:PR|V:01|C:1|R:123456789012345611|N:JEST Ltd., Test|I:RSD1295,';
        $this->assertSame($expected, $this->service->createIpsQrString($args));
    }

    public function testWorksWithOptionalArguments(): void
    {
        $args = [
            'identificationCode' => 'PR',
            'version'            => '01',
            'characterSet'       => '1',
            'bankAccountNumber'  => '123456789012345611',
            'payeeName'          => 'JEST Ltd., Test',
            'amount'             => '1295,',
            'paymentCode'        => '123',
            'paymentPurpose'     => 'Testing',
        ];
        $expected = 'K:PR|V:01|C:1|R:123456789012345611|N:JEST Ltd., Test|I:RSD1295,|SF:123|S:Testing';
        $this->assertSame($expected, $this->service->createIpsQrString($args));
    }

    public function testWorksWithReferenceCode(): void
    {
        $args = [
            'identificationCode' => 'PR',
            'version'            => '01',
            'characterSet'       => '1',
            'bankAccountNumber'  => '123456789012345611',
            'payeeName'          => 'JEST Ltd., Test',
            'amount'             => '1295,',
            'paymentCode'        => '123',
            'paymentPurpose'     => 'Testing',
            'referenceCode'      => '972012345',
        ];
        $expected = 'K:PR|V:01|C:1|R:123456789012345611|N:JEST Ltd., Test|I:RSD1295,|SF:123|S:Testing|RO:972012345';
        $this->assertSame($expected, $this->service->createIpsQrString($args));
    }
}
