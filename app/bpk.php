<?php

namespace App;

class Bpk
{
    public $keterangan;
    public $jumlah;
    public $nominal;

    public function __construct($keterangan, $jumlah, $nominal)
    {
        $this->keterangan = $keterangan;
        $this->jumlah = $jumlah;
        $this->nominal = $nominal;
    }
}
