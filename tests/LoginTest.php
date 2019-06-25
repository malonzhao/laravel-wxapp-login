<?php
namespace XiaohuiLam\Laravel\WechatAppLogin\Test;

use App\Models\User;
use Illuminate\Support\Str;
use XiaohuiLam\Laravel\WechatAppLogin\Facade;
use XiaohuiLam\Laravel\WechatAppLogin\Test\AbstractTest\AbstractTest;

class LoginTest extends AbstractTest
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLogin()
    {
        $code = Str::random();
        $openid = Str::random();
        $user = User::create([
            'name' => $openid,
            'openid' => $openid,
            'email' => $openid . '@wechat.com',
            'password' => $openid,
        ]);

        Facade::shouldReceive('login')
            ->with($code)
            ->once()
            ->andReturn([
                'openid' => $openid,
            ]);

        $response = $this->post('/api/login', ['code' => $code]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(Str::contains($response->getContent(), 'token'));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLoginFail()
    {
        $code = Str::random();

        Facade::shouldReceive('login')
            ->with($code)
            ->once()
            ->andReturn([
                'code' => -1,
            ]);

        $response = $this->post('/api/login', ['code' => $code]);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertFalse(Str::contains($response->getContent(), 'token'));
        $this->assertTrue(Str::contains($response->getContent(), 'bad code'));
    }
}
