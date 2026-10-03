<?php

namespace Tests\Unit;

use App\Services\GarantiPos;
use PHPUnit\Framework\TestCase;

class GarantiPosTest extends TestCase
{
    private function pos(array $c = []): GarantiPos
    {
        return new GarantiPos(array_merge([
            'mode' => 'TEST', 'merchant_id' => '7000679', 'terminal_id' => '30691297', 'prov_user_id' => 'PROVAUT',
            'prov_password' => '123qweASD/', 'user_id' => 'PROVAUT', 'store_key' => '12345678',
            'security_level' => '3D_OOS_PAY', 'api_version' => '512', 'company_name' => 'TEST',
            'gateway' => ['TEST' => 'https://test', 'PROD' => 'https://prod'],
        ], $c));
    }

    public function test_hashed_password_pads_terminal_id_to_nine_digits(): void
    {
        $this->assertSame(strtoupper(sha1('123qweASD/030691297')), $this->pos()->hashedPassword());
    }

    public function test_request_hash_is_sha512_of_documented_fields(): void
    {
        $pos = $this->pos();
        $expected = strtoupper(hash('sha512', '30691297'.'ORD1'.'10000'.'949'.'https://ok'.'https://fail'.'sales'.''.'12345678'.$pos->hashedPassword()));
        $this->assertSame($expected, $pos->requestHash('ORD1', '10000', '949', 'https://ok', 'https://fail', 'sales', ''));
    }

    public function test_not_configured_without_credentials(): void
    {
        $this->assertFalse($this->pos(['store_key' => null])->isConfigured());
        $this->assertTrue($this->pos()->isConfigured());
    }

    public function test_verify_rejects_missing_or_wrong_hash(): void
    {
        $pos = $this->pos();
        $this->assertFalse($pos->verifyResponse([]));
        $this->assertFalse($pos->verifyResponse(['hashparams' => 'a:b', 'a' => '1', 'b' => '2', 'hash' => 'X']));
        $this->assertTrue($pos->verifyResponse(['hashparams' => 'a:b', 'a' => '1', 'b' => '2', 'hash' => strtoupper(hash('sha512', '1212345678'))]));
        // Eski sürüm: base64(sha1)
        $this->assertTrue($pos->verifyResponse(['hashparams' => 'a:b', 'a' => '1', 'b' => '2', 'hash' => base64_encode(pack('H*', sha1('1212345678')))]));
    }

    public function test_approval_needs_3d_and_provision_success(): void
    {
        $pos = $this->pos();
        $this->assertTrue($pos->isApproved(['mdstatus' => '1', 'procreturncode' => '00']));
        $this->assertFalse($pos->isApproved(['mdstatus' => '0', 'procreturncode' => '00']));
        $this->assertFalse($pos->isApproved(['mdstatus' => '1', 'procreturncode' => '05']));
    }
}
