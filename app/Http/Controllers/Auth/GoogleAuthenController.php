<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \ParagonIE\ConstantTime\Base32;
use Crypt;
use Google2FA;
use Cache;
use Session;
use App\User;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA as GQ;
use App\Http\Requests\ValidateSecretRequest;
use PragmaRX\Google2FALaravel\Support\Constants;

class GoogleAuthenController extends Controller
{

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function enableTwoFactor(Request $request)
    {
        $google2fa = new GQ();

        //generate new secret
        $secret = $this->generateSecret();
        // $secret = $google2fa->generateSecretKey();

        //get user
        $user = User::find(1);

        // //encrypt and then save secret
        $user->google2fa_secret = Crypt::encrypt($secret);
        $user->save();

        //generate image for QR barcode
        // $google2fa_url = $google2fa->getQRCodeGoogleUrl(
        //     'App',
        //     $user->email,
        //     $secret
        // );
        $imageDataUri = Google2FA::getQRCodeInline(
            $request->getHttpHost(),
            $user->email,
            $secret,
            200
        );

        return view('google2fa/index', ['image' => $imageDataUri,
            'secret' => $secret]);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function disableTwoFactor(Request $request)
    {
        $user = $request->user();

        //make secret column blank
        $user->google2fa_secret = null;
        $user->save();

        return view('2fa/disableTwoFactor');
    }

    /**
     * Generate a secret key in Base32 format
     *
     * @return string
     */
    private function generateSecret()
    {
        $randomBytes = random_bytes(10);

        return Base32::encodeUpper($randomBytes);
    }

    /**
     *
     * @param  App\Http\Requests\ValidateSecretRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postValidateToken(Request $request)
    {
        $value = $request->get('one_time_password');

        $user = auth()->user();
        $secret = Crypt::decrypt($user->google2fa_secret);

        $result = Google2FA::verifyKey($secret, $value);

        // return Google2FA::verifyKey($secret, $value);

        //get user id and create cache key
        // $userId = $request->session()->pull('2fa:user:id');
        $userId = $request->session()->pull('2fa:user:id');
        $key    = $user->id . ':' . $request->one_time_password;

        //use cache to store token to blacklist
        Cache::add($key, true, 4);

        //login and redirect user
        Auth::loginUsingId($user->id);

        // Session::put('google2fa.auth_passed', true);

        return redirect()->intended('dashboard');
    }

}
