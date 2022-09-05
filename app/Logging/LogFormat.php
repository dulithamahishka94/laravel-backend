<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;

class LogFormat
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(
                new LineFormatter("[%datetime%] %level_name%: %message% %context%\n", "Y-m-d H:i:s", true)
            );
        }
    }
}
