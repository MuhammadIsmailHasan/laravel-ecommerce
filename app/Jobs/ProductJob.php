<?php

namespace App\Jobs;

use File;
use App\Product;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Imports\ProductImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $category_id;
    protected $filename;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($category_id, $filename)
    {
        $this->category_id = $category_id;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //IMPORT DATA EXCEL TADI YANG SUDAH DISIMPAN DI STORAGE, KEMUDIAN CONVERT MENJADI ARRAY
        $files = (new ProductImport)->toArray(storage_path('app/public/uploads/' . $this->filename));

        foreach ($files[0] as $row) {
          	//FORMATTING URLNYA UNTUK MENGAMBIL FILE-NAMENYA BESERTA EXTENSION
          	//JADI PASTIKAN PADA TEMPLATE MASS UPLOADNYA NANTI PADA BAGIAN URL
          	//HARUS DIAKHIRI DENGAN NAMA FILE YANG LENGKAP DENGAN EXTENSION
            $explodeURL = explode('/', $row[4]);
            $explodeExtension = explode('.', end($explodeURL));
            $filename = time() . Str::random(6) . '.' . end($explodeExtension);
          
          	//DOWNLOAD GAMBAR TERSEBUT DARI URL TERKAIT
            file_put_contents(storage_path('app/public/products') . '/' . $filename, file_get_contents($row[4]));

          	//KEMUDIAN SIMPAN DATANYA DI DATABASE
            Product::create([
                'name' => $row[0],
                'slug' => $row[0],
                'category_id' => $this->category_id,
                'description' => $row[1],
                'price' => $row[2],
                'weight' => $row[3],
                'image' => $filename,
                'status' => true
            ]);
        }

        //JIKA PROSESNYA SUDAH SELESAI MAKA FILE YANG ADA DISTORAGE AKAN DIHAPUS
        File::delete(storage_path('app/public/uploads/' .$this->filename));
    }
}
