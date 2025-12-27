<?php
declare(strict_types=1);

namespace App\Service;

//TODO: Implement TreatmentService
class TreatmentService
{
    public function __construct()
    {
    }

    public function getAll(): array
    {
        return [
            ['id' => 1, name => "Email marketing", 'purpose' => 'Promotions'],
            ['id' => 2, name => "Email marketing", 'purpose' => 'Promotions'],
            ['id' => 3, name => "Email marketing", 'purpose' => 'Promotions'],
        ];
    }

}