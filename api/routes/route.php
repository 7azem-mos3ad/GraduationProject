<?php
NI_Api_route::post('/login', function () {
    NI_Api_controller::run('API\Users@login');
});

NI_Api_route::post('/register', function () {
    NI_Api_controller::run('API\Users@register');
});
