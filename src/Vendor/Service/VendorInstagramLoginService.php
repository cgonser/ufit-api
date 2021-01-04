<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;

class VendorInstagramLoginService
{
    private VendorProvider $vendorProvider;

    private VendorService $vendorService;

    public function __construct(
        VendorProvider $vendorProvider,
        VendorService $vendorService
    ) {
        $this->vendorProvider = $vendorProvider;
        $this->vendorService = $vendorService;
    }

    public function prepareVendorFromInstagramCode(string $code): Vendor
    {
        try {
            $data = $this->GetToken($code);

            $url = 'https://graph.instagram.com/me/?fields=id,username,email,account_type,media_count&access_token='.$data['access_token'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $data = json_decode(curl_exec($ch), true);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            dump($data);

            exit;
//
//            $vendor = $this->vendorProvider->findOneByEmail($graphUser->getEmail());
//
//            if (!$vendor) {
//                $vendor = $this->createVendorFromGraphUser($graphUser);
//            }

//            return $vendor;
        } catch (FacebookResponseException | FacebookSDKException $e) {
            throw new VendorFacebookLoginFailedException();
        }
    }

    public function GetToken($code)
    {
        $client_id = '1063810574125577';
        $redirect_uri = 'https://local.ufit.io/instagram-login.html';
        $client_secret = '648493dd6a14c2e27166f37c9c33949d';

        $url = 'https://api.instagram.com/oauth/access_token';

        $urlPost = 'client_id='.$client_id.'&redirect_uri='.$redirect_uri.'&client_secret='.$client_secret.'&code='.$code.'&grant_type=authorization_code';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlPost);
        $data = json_decode(curl_exec($ch), true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $data;
    }
}
