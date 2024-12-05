<?php

namespace App\Security;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;


class RgflyCustomProvider implements OAuthAwareUserProviderInterface
{

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        return $response->getUsername();
    }
}