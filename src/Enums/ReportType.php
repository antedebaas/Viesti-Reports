<?php

namespace App\Enums;

enum ReportType: int
{
    case Other = 0;
    case DMARC = 1;
    case STS = 2;
}
