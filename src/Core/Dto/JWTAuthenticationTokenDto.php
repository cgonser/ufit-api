<?php

namespace App\Core\Dto;

class JWTAuthenticationTokenDto
{
    public ?string $token;

    public ?string $refresh_token;
}