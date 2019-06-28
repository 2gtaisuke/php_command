<?php
require_once __DIR__ . '/vendor/autoload.php';

use TsujiTaisuke\Utils\Command\OptionParser;
use TsujiTaisuke\Utils\Command\Wc;
use TsujiTaisuke\Utils\Command\Wc\File;

// エラー時に例外をスローするようにコールバック関数を登録
set_error_handler(function($errno, $errstr, $errfile, $errline){
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

# オプションを設定する
$option_parser = new OptionParser(Wc::SHORT_OPTS, Wc::LONG_OPTS);
$options = $option_parser->parse(Wc::OPTION_SET);

if (array_key_exists('version', $options)) {
    echo 'version: 1 LICENSE: MIT' . PHP_EOL;
    exit(0);
} else if (array_key_exists('help', $options)) {
     echo <<<EOF
Usage:
  wc [-h|-v|-c|-m|-l|-w] [file]
Options:
  --help,     -h    print this
  --version,  -v    print wc version
  --bytes,    -c    count all bytes 
  --chars,    -m    count multi bytes
  --lines,    -l    count lines
  --words,    -w    count words
Description:
   a clone of wc(word count) in php.

EOF;
    exit(0);
}

# オプションキーの並び替え
uksort($options, function($a, $b){
    if ($b === 'l' || $b === 'lines') {
        return 1;
    }
    if ($a === 'l' || $a === 'lines') {
        return -1;
    }
    if ($b === 'w' || $b === 'words') {
        return 1;
    }
    if ($a === 'w' || $a === 'words') {
        return -1;
    }
    if ($b === 'm' || $b === 'chars') {
        return 1;
    }
    if ($a === 'm' || $a === 'chars') {
        return -1;
    }
    if ($b === 'c' || $b === 'bytes') {
        return 1;
    }
    if ($a === 'c' || $b === 'bytes') {
        return -1;
    }
});

$wc = new Wc($options);
$command_args = [];
foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/\A[^-].*\z/', $arg)) {
        $command_args[] = $arg;
    }
}

if (count($command_args) === 0) {
    $file = new File('', File::INPUT_TYPE_STDIN);
    while (!feof(STDIN)) {
        $line = fgets(STDIN);
        $file->addLine($line);
    }
    $wc->setFile($file);
} else {
    foreach ($command_args as $file_name) {
        if (!file_exists($file_name)) {
            fputs(STDERR, "wc: {$file_name}: No such file or directory" . PHP_EOL);
            continue;
        } else if (!is_readable($file_name)) {
            fputs(STDERR, "wc: {$file_name}: Permission denied" . PHP_EOL);
            continue;
        }

        /** @var Wc\File $file */
        $file = new File($file_name);
        try {
            $fp = fopen($file_name, 'r');
            while ($line = fgets($fp)) {
                $file->addLine($line);
            }
            $wc->setFile($file);
        } catch (ErrorException $e) {
            fputs(STDERR, $e->getMessage());
            continue;
        } finally {
            if (isset($fp)) {
                fclose($fp);
            }
        }
    }
}

echo $wc->getResult();