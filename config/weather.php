<?php
// Override these using environment variables when deploying.
return [
    'latitude' => getenv('CLINIC_LATITUDE') !== false ? getenv('CLINIC_LATITUDE') : '14.5995',
    'longitude' => getenv('CLINIC_LONGITUDE') !== false ? getenv('CLINIC_LONGITUDE') : '120.9842',
    'timezone' => 'Asia/Manila',
    'timeout_seconds' => 15,
];
