<?php
namespace TsujiTaisuke\Utils\Command;

use TsujiTaisuke\Utils\Command\Wc\File;

class Wc {
    const SHORT_OPTS = 'clwm';
    const LONG_OPTS  = ['bytes', 'chars', 'lines', 'words', 'help', 'version'];
    const OPTION_SET = ['c' => 'bytes', 'l' => 'lines', 'w' => 'words', 'm' => 'chars'];
    const TOTAL = 'total';

    private $options;
    private $max_digit = 0;
    /** @var array */
    private $container = [];

    public function __construct(array $options = [])
    {
        $this->container[self::TOTAL] = new File(self::TOTAL);
        $this->options = $options;
    }

    /**
     * ファイルオブジェクトをセットする
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->container[] = $file;

        $file_digit = strlen($file->getByteCount());
        if ($this->max_digit < $file_digit) {
            $this->max_digit = $file_digit;
        }

        /** @var File $total */
        $total = $this->container[self::TOTAL];
        $total->setByteCount($total->getByteCount() + $file->getByteCount());
        $total->setLineCount($total->getLineCount() + $file->getLineCount());
        $total->setWordCount($total->getWordCount() + $file->getWordCount());
        $total->setMultiCount($total->getMultiCount() + $file->getMultiCount());

        if (count($this->container) > 2) {
            $total_digit = strlen($total->getByteCount());
            if ($this->max_digit < $total_digit) {
                $this->max_digit = $total_digit;
            }
        }
    }

    public function getResult(): String
    {
        $result = '';

        /** @var File $file */
        foreach ($this->container as $k => $file) {
            if ($k === self::TOTAL) { continue; }
            $result .= $file->formatResult($this->max_digit, $this->options);
        }

        if (count($this->container) > 2) {
            /** @var File $total */
            $total = $this->container[self::TOTAL];
            $result .= $total->formatResult($this->max_digit, $this->options);
        }

        return $result;
    }
}