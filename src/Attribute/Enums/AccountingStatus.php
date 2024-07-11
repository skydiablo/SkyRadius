<?php
declare(strict_types=1);

namespace SkyDiablo\SkyRadius\Attribute\Enums;

enum AccountingStatus: int
{

    case Start = 1;
    case Stop = 2;
    case InterimUpdate = 3;
    case AccountingOn = 7;
    case AccountingOff = 8;
//       9-14   Reserved for Tunnel Accounting
    case Failed = 15;

}
