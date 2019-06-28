<?php
use PHPUnit\Framework\TestCase;

use TsujiTaisuke\Utils\Command\Wc;
use TsujiTaisuke\Utils\Command\Wc\File;

class WcTest extends TestCase
{
    public function testSingleFileFormat()
    {
        $file = new File('data1');
        $file->addLine("a b c\n");
        $wc   = new Wc();
        $wc->setFile($file);
        $this->assertSame("1 3 6 data1\n", $wc->getResult());
    }

    public function testMultiFileFormat()
    {
        $datas = [
            ['data1', "a b c\n",          6, 3,  1, " 1  3  6 data1\n"],
            ['data2', "a　 b\t c d\n",   12, 4,  1, " 1  4 12 data2\n"],
            ['data3', "あ  いう e\t　お", 20, 4,  0, " 0  4 20 data3\n"],
        ];

        $wc = new Wc();
        $byte_count = 0;
        $word_count = 0;
        $line_count = 0;
        $result     = '';

        foreach ($datas as $data) {
            $file = new File($data[0]);
            $file->addLine($data[1]);
            $wc->setFile($file);

            $byte_count += $data[2];
            $word_count += $data[3];
            $line_count += $data[4];

            $result .= $data[5];
        }

        $result .= sprintf("%2d %2d %2d %s", $line_count, $word_count, $byte_count, 'total') . PHP_EOL;

        $this->assertSame($result, $wc->getResult());
    }
}