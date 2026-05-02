<?php

return [
    'super_admin_phone' => env('ADMIN_SUPER_PHONE'),
    'super_admin_role' => env('ADMIN_SUPER_ROLE', 'super-admin'),
    'employee_role' => env('ADMIN_EMPLOYEE_ROLE', 'employee'),
    'host_role' => env('ADMIN_HOST_ROLE', 'host'),
    'admin_login_permission' => env('ADMIN_LOGIN_PERMISSION', 'admin-panel-access'),
    'content_manage_permission' => env('CONTENT_MANAGE_PERMISSION', 'content-manage'),
];
