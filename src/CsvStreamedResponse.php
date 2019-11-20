<?php

namespace Kalibora\HttpFoundation;

use Doctrine\ORM\QueryBuilder;
use Goodby\CSV\Export\Standard\Collection\CallbackCollection;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Kalibora\ChunkGenerator\ChunkGeneratorBuilder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvStreamedResponse extends StreamedResponse
{
    public function __construct(
        iterable $collection,
        array $columnHeaders = [],
        int $status = 200,
        array $headers = []
    ) {
        $callback = function () use ($collection, $columnHeaders) {
            $config = new ExporterConfig();
            $config->setColumnHeaders($columnHeaders);

            $exporter = new Exporter($config);
            $exporter->unstrict();
            $exporter->export('php://output', $collection);
        };

        parent::__construct($callback, $status, array_merge($headers, [
            'Content-Type' => 'text/csv',
        ]));
    }

    public static function createFromDoctrineQueryBuilder(
        QueryBuilder $qb,
        array $columnHeaders = [],
        callable $extractRowCallback = null,
        int $status = 200,
        array $headers = []
    ) : self {
        $gen = ChunkGeneratorBuilder::fromDoctrineQueryBuilder($qb)->build();

        if ($extractRowCallback) {
            $collection = new CallbackCollection($gen(), $extractRowCallback);
        } else {
            $collection = $gen();
        }

        return new static($collection, $columnHeaders, $status, $headers);
    }
}
