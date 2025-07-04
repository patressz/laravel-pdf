<?php

declare(strict_types=1);

namespace Patressz\LaravelPdf\Enums;

enum Unit: string
{
    case Pixel = 'px';
    case Inch = 'in';
    case Centimeter = 'cm';
    case Millimeter = 'mm';
}
