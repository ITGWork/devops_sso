<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use HP;

class EncryptCookies extends Middleware
{

    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [];

    public function __construct(EncrypterContract $encrypter)
    {

        $this->encrypter = $encrypter;

        $config = HP::getConfig();
        // "progid" มาจากคุกกี้ที่ i-industry ตั้งให้ตรงๆ (plaintext ธรรมดา ไม่ได้เข้ารหัสแบบ
        // Laravel) - ถ้าไม่ยกเว้นไว้ตรงนี้ EncryptCookies จะพยายาม decrypt แล้ว fail เงียบๆ
        // ทำให้ $request->cookie('progid') คืน null เสมอทั้งที่คุกกี้มีค่าอยู่จริง
        $this->except = [$config->sso_name_cookie_login, 'active_cookie', 'progid'];
    }

}
