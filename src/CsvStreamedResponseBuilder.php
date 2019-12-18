<?php

namespace Kalibora\HttpFoundation;

use Doctrine\ORM\QueryBuilder;
use Goodby\CSV\Export\Standard\Collection\CallbackCollection;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Kalibora\ChunkGenerator\ChunkGeneratorBuilder;

class CsvStreamedResponseBuilder
{
    /**
     * @var iterable
     */
    private $rows;

    /**
     * @var ExporterConfig
     */
    private $csvConfig;

    /**
     * @var int
     */
    private $httpStatus = 200;

    /**
     * @var array
     */
    private $httpHeaders = [];

    public function __construct(
        iterable $rows = null,
        ExporterConfig $csvConfig = null,
        int $httpStatus = 200,
        array $httpHeaders = []
    ) {
        $this->rows = $rows ?? [];
        $this->csvConfig = $csvConfig ?? new ExporterConfig();
        $this->httpStatus = $httpStatus;
        $this->httpHeaders = $httpHeaders;
    }

    public function setRowsFromArray(array $rows) : self
    {
        $this->rows = $rows;

        return $this;
    }

    public function setRowsFromDoctrineQueryBuilder(
        QueryBuilder $qb,
        callable $extractRowCallback = null,
        bool $fetchJoinCollection = false,
        int $chunkSize = 100
    ) : self {
        $gen = ChunkGeneratorBuilder::fromDoctrineQueryBuilder($qb, $specifiedIds = [], $fetchJoinCollection)
            ->setChunkSize($chunkSize)
            ->build()
        ;

        if ($extractRowCallback) {
            $rows = new CallbackCollection($gen(), $extractRowCallback);
        } else {
            $rows = $gen();
        }

        $this->rows = $rows;

        return $this;
    }

    public function setCsvDelimiter(string $delimiter) : self
    {
        $this->csvConfig->setDelimiter($delimiter);

        return $this;
    }

    public function setCsvEnclosure(string $enclosure) : self
    {
        $this->csvConfig->setEnclosure($enclosure);

        return $this;
    }

    public function setCsvEscape(string $escape) : self
    {
        $this->csvConfig->setEscape($escape);

        return $this;
    }

    public function setCsvNewline(string $newline) : self
    {
        $this->csvConfig->setNewline($newline);

        return $this;
    }

    public function setCsvFromCharset(string $fromCharset) : self
    {
        $this->csvConfig->setFromCharset($fromCharset);

        return $this;
    }

    public function setCsvToCharset(string $toCharset) : self
    {
        $this->csvConfig->setToCharset($toCharset);

        return $this;
    }

    public function setCsvFileMode(string $fileMode) : self
    {
        $this->csvConfig->setFileMode($fileMode);

        return $this;
    }

    public function setCsvColumnHeaders(array $columnHeaders) : self
    {
        $this->csvConfig->setColumnHeaders($columnHeaders);

        return $this;
    }

    public function setHttpStatus(int $httpStatus) : self
    {
        $this->httpStatus = $httpStatus;

        return $this;
    }

    public function setHttpHeaders(array $httpHeaders) : self
    {
        $this->httpHeaders = $httpHeaders;

        return $this;
    }

    public function build() : CsvStreamedResponse
    {
        return new CsvStreamedResponse(
            $this->rows,
            $this->csvConfig,
            $this->httpStatus,
            $this->httpHeaders
        );
    }
}
