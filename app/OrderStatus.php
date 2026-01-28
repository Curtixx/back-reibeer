<?php

namespace App;

enum OrderStatus: string
{
    case Open = 'open';
    case Closed = 'closed';
}
