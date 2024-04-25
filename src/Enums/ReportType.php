<?php

namespace App\Enums;

enum ReportType
{
    case Unknown;
    case DMARC;
    case STS;
}
