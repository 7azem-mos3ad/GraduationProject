<?php

NI_route::get('/', function () {
    NI_view::V_html('welcome');
});
