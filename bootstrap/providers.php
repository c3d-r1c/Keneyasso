<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Modules\Patients\Providers\PatientsServiceProvider;

return [
    AppServiceProvider::class,
    PatientsServiceProvider::class,
];
