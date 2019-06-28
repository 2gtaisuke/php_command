<?php
namespace TsujiTaisuke\Utils\Command\Wc;

class File
{
    const INPUT_TYPE_FILE  = 0;
    const INPUT_TYPE_STDIN = 1;

    private $byte_count  = 0;
    private $line_count  = 0;
    private $multi_count = 0;
    private $word_count  = 0;
    private $input_type;

    private $file_name;

    public function __construct(String $file_name = '', int $input_type = self::INPUT_TYPE_FILE)
    {
        $this->file_name = $file_name;
        $this->input_type = $input_type;
    }

    public function getByteCount(): int
    {
        return $this->byte_count;
    }

    public function getLineCount(): int
    {
        return $this->line_count;
    }

    public function getWordCount(): int
    {
        return $this->word_count;
    }

    public function getMultiCount(): int
    {
        return $this->multi_count;
    }

    public function setByteCount(int $byte_count): void
    {
        $this->byte_count = $byte_count;
    }

    public function setLineCount(int $line_count): void
    {
        $this->line_count = $line_count;
    }

    public function setWordCount(int $word_count): void
    {
        $this->word_count = $word_count;
    }

    public function setMultiCount(int $multi_count): void
    {
        $this->multi_count = $multi_count;
    }


    public function addLine(String $line)
    {
        # wcコマンドの仕様上、改行コードがなければカウントしない。
        if (preg_match('/\A.*\n\z/', $line)) {
            $this->line_count++;
        }
        $this->multi_count += mb_strlen($line);
        $this->byte_count += strlen($line);
        # 半角空白、全角空白、タブを正規表現で区切って''を捨ててカウントする
        $this->word_count += count(array_diff(mb_split('(\s|　|\t)', $line), ['']));
    }

    public function formatResult(int $max_digit, array $options = []): String
    {
        $formatted_result = '';
        # オプションが一つの場合、桁揃えなし
        if ($this->input_type === self::INPUT_TYPE_STDIN) {
            count($options) === 1 ? $format = '%d' : $format = '%7d';
        } else {
            $format = "%{$max_digit}d";
        }
        # オプション無しの場合、全て出力する
        if (count($options) === 0) {
            $options = ['l' => false, 'w' => false, 'c' => false];
        }

        $count = 0;
        foreach ($options as $option => $_) {
            $count !== 0 && $formatted_result .= ' ';
            $count++;
            switch ($option) {
                case 'l':
                case 'lines':
                    $formatted_result .= sprintf($format, $this->line_count);
                    break;
                case 'w':
                case 'words':
                    $formatted_result .= sprintf($format, $this->word_count);
                    break;
                case 'm':
                case 'chars':
                    $formatted_result .= sprintf($format, $this->multi_count);
                    break;
                case 'c':
                case 'bytes':
                    $formatted_result .= sprintf($format, $this->byte_count);
                    break;
            }
        }

        if ($this->input_type === self::INPUT_TYPE_STDIN) {
            $formatted_result .= PHP_EOL;
        } else {
            $formatted_result .= sprintf(' %s' . PHP_EOL, $this->file_name);
        }

        return $formatted_result;
    }
}