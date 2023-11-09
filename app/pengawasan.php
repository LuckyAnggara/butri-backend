<?php

namespace App;

class Pengawasan
{
    public $name;
    public $jumlah;

    public function __construct($name, $jumlah)
    {
        $this->name = $name;
        $this->jumlah = $jumlah;
    }
}
