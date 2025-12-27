<?php

namespace App\Controller;

use App\Service\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login(): void
    {
        //TODO: Implement login
        $data = [
            'email' => 'vthuillier@luxferre-code.fr',
            'token' => 'test_token'
        ];
        echo json_encode($data);
    }

    public function register(): void
    {
        //TODO: Implement register
        $data = [
            'message' => 'User registered successfully'
        ];
        echo json_encode($data);
    }

    public function logout(): void
    {
        //TODO: Implement logout
        $data = [
            'message' => 'User logged out successfully'
        ];
        echo json_encode($data);
    }

}