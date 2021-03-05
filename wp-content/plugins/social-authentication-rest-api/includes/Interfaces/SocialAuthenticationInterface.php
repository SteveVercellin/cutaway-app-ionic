<?php

namespace Includes\Interfaces;

interface SocialAuthenticationInterface
{
    public function authenticateUser($request);
}