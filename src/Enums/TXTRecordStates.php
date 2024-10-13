<?php

namespace App\Enums;

enum TXTRecordStates : int
{
    case Fail = 0;
    case Warn = 1;
    case Good = 2;
}
