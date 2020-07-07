<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductImport implements WithStartRow, WithChunkReading
{
    use Importable;
    
    public function startRow() : int
    {
        //JADI KITA BATAS DATA YANG AKAN DIGUNAKAN MULAI DARI BARIS KEDUA,
        //KARENA BARIS PERTAMA DIGUNAKAN SEBAGAI HEADING AGAR MEMUDAHKAN ORANG YANG MENGISI DATA PADA FILE EXCEL
        return 2;
    }

    public function chunkSize() : int
    {
        //KEMUDIAN KITA GUNAKAN chunkSize UNTUK MENGONTROL PENGGUNAAN MEMORY DENGAN MEMBATASI LOAD DATA DALAM SEKALI PROSES
        return 100;
    }
}
