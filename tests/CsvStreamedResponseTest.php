<?php

namespace Kalibora\HttpFoundation;

use Goodby\CSV\Export\Standard\ExporterConfig;
use PHPUnit\Framework\TestCase;

class CsvStreamedResponseTest extends TestCase
{
    public function testConstruct()
    {
        $response = new CsvStreamedResponse([
            ['a', 'b'],
            ['c', 'd'],
        ], new ExporterConfig());

        $this->expectOutputString("a,b\r\nc,d\r\n");

        $response->send();
    }

    public function testBuilder()
    {
        $response = CsvStreamedResponse::builder()
            ->setRowsFromArray([
                ['foo', 'bar'],
                ['b@z', "ho\tge"],
            ])
            ->setCsvDelimiter("\t")
            ->setCsvEnclosure('@')
            ->setCsvNewline("\n")
            ->setCsvColumnHeaders(['h1', 'h2'])
            ->build()
        ;

        $this->expectOutputString("h1\th2\nfoo\tbar\n@b@@z@\t@ho\tge@\n");

        $response->send();
    }

    public function testCharset()
    {
        $response = CsvStreamedResponse::builder()
            ->setRowsFromArray([
                ['UTF-8の', '日本語'],
            ])
            ->setCsvToCharset('sjis-win')
            ->build()
        ;

        $this->expectOutputString(
            mb_convert_encoding('UTF-8の', 'sjis-win', 'utf-8') .
            ',' .
            mb_convert_encoding('日本語', 'sjis-win', 'utf-8') .
            "\r\n"
        );

        $response->send();
    }
}
