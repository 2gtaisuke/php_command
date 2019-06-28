<?php
use PHPUnit\Framework\TestCase;
use TsujiTaisuke\Utils\Command\Wc\File;

class FileTest extends TestCase
{

    # TODO: 標準入力の場合のformatとそれぞれ

    /**
     * @dataProvider provideDataForTestFormatLines
     */
    public function testaddLines($file_name, $string, $byte_count, $word_count, $line_count)
    {
        $file = new File($file_name);
        $file->addLine($string);
        $this->assertSame($byte_count, $file->getByteCount());
        $this->assertSame($word_count, $file->getWordCount());
        $this->assertSame($line_count, $file->getLineCount());
    }

    /**
     * @dataProvider provideDataForTestFormatLines
     */
    public function testFileFormatLines($file_name, $string, $byte_count, $word_count, $line_count, $file_format)
    {
        $file = new File($file_name);
        $file->addLine($string);
        $this->assertSame($file_format, $file->formatResult(strlen($byte_count)));
    }

    /**
     * @dataProvider provideDataForTestFormatLines
     */
    public function testStdinFormatLines($file_name, $string, $byte_count, $word_count, $line_count, $file_format, $stdin_format)
    {
        $file = new File($file_name, File::INPUT_TYPE_STDIN);
        $file->addLine($string);
        $this->assertSame($stdin_format, $file->formatResult(strlen($byte_count)));
    }

    public function provideDataForTestFormatLines()
    {
        # 三桁バイトのデータを作成する
        $str_100 = '';
        for ($i = 0; $i < 10; $i++) {
            $str_100 .= 'a         ';
        }
        $str_100 .= "\n";

        return [
            [ 'data1', "a b c\n",          6, 3,  1, "1 3 6 data1\n",       "      1       3       6\n" ],
            [ 'data2', "a　 b\t c d\n",   12, 4,  1, " 1  4 12 data2\n",    "      1       4      12\n" ],
            [ 'data3', "あ  いう e\t　お", 20, 4,  0, " 0  4 20 data3\n",    "      0       4      20\n" ],
            [ 'data4', "",                 0, 0,  0, "0 0 0 data4\n",       "      0       0       0\n" ],
            [ 'data5', "\n",               1, 0,  1, "1 0 1 data5\n",       "      1       0       1\n" ],
            [ 'data6', "あ\n",             4, 1,  1, "1 1 4 data6\n",       "      1       1       4\n" ],
            [ 'data6', $str_100,           101, 10, 1, "  1  10 101 data6\n", "      1      10     101\n" ],
        ];
    }

    public function testAddMultiLines()
    {
        $datas = [
            [ 'data1', "a b c\n",          6, 3,  1, "1 3 6 data1\n",       "      1       3       6\n" ],
            [ 'data2', "a　 b\t c d\n",   12, 4,  1, " 1  4 12 data2\n",    "      1       4      12\n" ],
        ];
        $file = new File('test');
        $byte_count = 0;
        $word_count = 0;
        $line_count = 0;
        foreach ($datas as $data) {
            $file->addLine($data[1]);
            $byte_count += $data[2];
            $word_count += $data[3];
            $line_count += $data[4];
        }

        $this->assertSame($byte_count, $file->getByteCount());
        $this->assertSame($word_count, $file->getWordCount());
        $this->assertSame($line_count, $file->getLineCount());
    }

    /**
     * @dataProvider provideDataForOptionTest
     */
    public function testFileFormatWithOptions($file_name, $string, $byte_count, $word_count, $line_count, $file_format, $stdin_format, $options)
    {
        $file = new File($file_name);
        $file->addLine($string);
        $this->assertSame($file_format, $file->formatResult(strlen($byte_count), $options));
    }

    /**
     * @dataProvider provideDataForOptionTest
     */
    public function testStdInFormatWithOptions($file_name, $string, $byte_count, $word_count, $line_count, $file_format, $stdin_format, $options)
    {
        $file = new File($file_name, FILE::INPUT_TYPE_STDIN);
        $file->addLine($string);
        $this->assertSame($stdin_format, $file->formatResult(strlen($byte_count), $options));
    }


    public function provideDataForOptionTest()
    {
        return [
            [ 'data1', "a b c\n",          6, 3,  1, "1 data1\n",       "1\n", ['l' => false] ],
            [ 'data2', "a b c\n",          6, 3,  1, "3 data2\n",       "3\n", ['w' => false] ],
            [ 'data3', "a b c\n",          6, 3,  1, "6 data3\n",       "6\n", ['c' => false] ],
            [ 'data4', "あ  いう e\t　お", 20, 4,  0, "10 data4\n",      "10\n",   ['m' => false]  ],
            [ 'data5', "a b c\n",          6, 3,  1, "1 data5\n",       "1\n", ['lines' => false] ],
            [ 'data6', "a b c\n",          6, 3,  1, "3 data6\n",       "3\n", ['words' => false] ],
            [ 'data7', "a b c\n",          6, 3,  1, "6 data7\n",       "6\n", ['bytes' => false] ],
            [ 'data8', "あ  いう e\t　お", 20, 4,  0, "10 data8\n",      "10\n", ['chars' => false] ],
            [ 'data9', "a b c\n",          6, 3,  1, "1 3 data9\n",       "      1       3\n", ['l' => false, 'w' => false] ],
            [ 'data10', "a b c\n",          6, 3,  1, "1 3 6 data10\n",       "      1       3       6\n", ['l' => false, 'w' => false, 'bytes' => false] ],
            [ 'data11', "あ  いう e\t　お", 20, 4,  0, " 0  4 10 20 data11\n",    "      0       4      10      20\n", ['lines' => false, 'words' => false, 'chars' => false, 'bytes' => false] ],
        ];
    }
}