<?php
// エラー時に例外をスローするようにコールバック関数を登録
set_error_handler(function($errno, $errstr, $errfile, $errline){
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

require_once __DIR__ . '/../vendor/autoload.php';