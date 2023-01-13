<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

enum CoverageDriver
{
    case Xdebug;
    case Pcov;
    case PHPDbg;
}
