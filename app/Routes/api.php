<?php
return [
    'GET /api/v1/reports' => ['Api\V1ReportController', 'index'],
    'GET /api/v1/reports/{id}' => ['Api\V1ReportController', 'show'],
    'POST /api/v1/reports' => ['Api\V1ReportController', 'store'],
];
