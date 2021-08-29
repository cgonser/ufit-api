<?php

declare(strict_types=1);

namespace App\Core\Service;

use FacebookAds\Api;

class FacebookApiClientFactory
{
    public function __construct(
        private string $facebookAppId,
        private string $facebookAppSecret
    ) {
    }

    public function createInstance(string $accessToken = null): Api
    {
        return Api::init($this->facebookAppId, $this->facebookAppSecret, $accessToken, false);
    }
}
