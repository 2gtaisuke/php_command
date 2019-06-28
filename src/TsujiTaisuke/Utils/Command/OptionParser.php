<?php
namespace TsujiTaisuke\Utils\Command;

class OptionParser
{
    private $short_opts;
    private $long_opts;

    public function __construct(String $short_opts, array $long_opts)
    {
        $this->short_opts = $short_opts;
        $this->long_opts  = $long_opts;
    }

    /**
     * @param array $option_set 同一のオプションを２次元配列で渡す(ex. [['b', 'batch'], ['c', 'char']])
     * @return array
     */
    public function parse(array $option_set = []): array
    {
        $options = getopt($this->short_opts, $this->long_opts);
        foreach ($option_set as $short_opt => $long_opt) {
            if (array_key_exists($short_opt, $options) && array_key_exists($long_opt, $options)) {
                unset($options[$long_opt]);
            }
        }

        return $options;
    }
}