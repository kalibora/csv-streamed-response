<?php

namespace Kalibora\HttpFoundation;

use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvStreamedResponse extends StreamedResponse
{
    public function __construct(
        iterable $rows,
        ExporterConfig $config,
        int $status = 200,
        array $headers = []
    ) {
        $callback = function () use ($rows, $config) {
            $exporter = new Exporter($config);

            if (!is_countable($rows)) {
                $exporter->unstrict();
            }

            $exporter->export('php://output', $rows);
        };

        parent::__construct($callback, $status, array_merge($headers, [
            'Content-Type' => 'text/csv',
        ]));
    }

    public static function builder() : CsvStreamedResponseBuilder
    {
        return new CsvStreamedResponseBuilder();
    }
}
