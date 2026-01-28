<?php

namespace App;

enum CashierStatus: string
{
    case Open = 'open';
    case Closed = 'closed';
}
