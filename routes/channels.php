<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('notifications', function () {
    return true;
});
