<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    private $kategori;

    private $importErrors = [];

    public function __construct()
    {
        // 2. Isi nilai variabel di dalam constructor
        $this->kategori = ["UJIAN","SARPRAS","KOMITE","SPP","SERAGAM",
                        "PEMBANGUNAN","BUKU","EKSKUL"];
    }

    private function logError($line, $column, $message, $data = '') {
        $this->importErrors[] = [
            'baris'   => $line,
            'kolom'   => $column,
            'pesan'   => $message,
            'data_asli' => $data
        ];
    }

    public function pembayaran()
    {
        $filename = 'LogPembayaranToImportKls1.csv';
        
        if (!Storage::disk('local')->exists($filename)) {
            // Debugging tip: This will show you exactly where Laravel is looking
            $fullPath = Storage::disk('local')->path($filename);
            return response()->json([
                'error' => 'File not found',
                'searched_at' => $fullPath
            ], 404);
        }

        $path = Storage::path($filename);

        $file = fopen($path, 'r');

        // 1. Grab the header row
        $header = fgetcsv($file, 0, ';');

        $header = array_map(function($value) {
            return match ($value) {
                " SPP " => 'SPP', 
                " Ekskul " => 'EKSKUL', 
                " Komite " => 'KOMITE', 
                " Sarpras " => 'SARPRAS', 
                " Ujian " => 'UJIAN', 
                " Buku " => 'BUKU', 
                " Infaq Pembangunan " => 'PEMBANGUNAN',
                default => $value, // Tetap gunakan angka jika tidak ada yang cocok
            };
        }, $header);
        
        $lineNumber = 1;
        $nama = [];
        DB::beginTransaction();
        try {
            while (($row = fgetcsv($file, 0, ';')) !== false) {
            $lineNumber++;
            // Check if column counts match to prevent errors
            // if ($lineNumber == 5) {
            //     continue;
            if (count($header) === count($row)) {
                $record = array_combine($header, $row);

                $siswa_tmp = Siswa::selectRaw('id, UPPER(nama) AS nama')
                            ->whereRaw('UPPER(nama) = ?', [strtoupper($record['nama'])])
                            ->whereHas('kelasAktif.kelas', function ($query) use ($record) {
                                $query->where('id', $record['Kelas']);
                            })
                            ->with('kelasAktif.kelas')
                            ->first();

                $nama[]= strtoupper($record['nama']);
                
                $siswa = [
                    'id' => $siswa_tmp->id,
                    'kelas_aktif_id' => $siswa_tmp->kelasAktif->kelas->id,
                ];

                // 
                // update tagihan siswa dengan where siswa dan ketegori
                // kategori didapatkan dari header
                // jika header spp maka kategori adalah SPP //
                $tagihans = TagihanSiswa::where('siswa_id', $siswa['id'])
                            ->whereColumn('nominal_tagihan', '=', 'sisa_pembayaran')
                            ->orderBy('tahun_tagihan', 'asc') // Urutkan tahun dulu
                            ->orderBy('bulan_tagihan', 'asc')->get();
                
                $this->processRecord($record, $siswa, $tagihans);
            }
        // die("Berhenti untuk inspeksi baris ke ".$lineNumber);
        //     }
        }
            fclose($file);
            DB::commit();
            return response()->json(['message' => 'Import Successful!']);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage(), $siswa, $record, $tagihans, $lineNumber, $nama);
            return "Error: " . $th->getMessage();
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    private function processRecord(array $record, array $siswa, $tagihans)
    {
        $total = 0;

        // dd($record['nama']);
        
        // // Convert '0'/'1' or 'yes'/'no' to boolean
        // $isActive = filter_var($record['status'], FILTER_VALIDATE_BOOLEAN);

        // // Convert date format (e.g., from 12/31/2024 to Y-m-d)
        // $birthday = Carbon::parse($record['birthday'])->format('Y-m-d');

        //
        // array:12 [
//   "" => "Yazid"
//   "nama" => "MUHAMMAD YAZID AL FATIH"
//   "Kelas" => "1"
//   "Tanggal" => "21/07/2025"
//   " SPP " => "150000"
//   " Ekskul " => ""
//   " Komite " => ""
//   " Sarpras " => ""
//   " Ujian " => ""
//   " Buku " => ""
//   " Infaq Pembangunan " => ""
//   " metode " => ""
// ]
        // 1. Insert Pembayaran
        //2. Insert Pembayaran Detail
        //3. Update Tagihan//
        foreach ($this->kategori as $kat) {
            if (isset($record[$kat])) {
                $total += floatval($record[$kat]);
            }
        }

        $pembayaranId = DB::table('pembayaran')->insertGetId([
            'tanggal_bayar' => $record['Tanggal'],
            'total_bayar'   => $total,
            'metode'        => $record[' metode '] ? 'transfer' : 'tunai',
            'keterangan'    => NULL,
            'created_at'    => '2025-12-31 23:59:00',
            'updated_at'    => '2025-12-31 23:59:00',
            'siswa_id'      => $siswa['id'],
            'kelas_aktif_id'=> $siswa['kelas_aktif_id'],
        ]);

        foreach ($this->kategori as $kat) {

            if (isset($record[$kat]) && is_numeric($record[$kat]) && $record[$kat] > 0) {
                $jumlahBayar = $record[$kat];
                try {
                $tnew = $tagihans->where('kategori', $kat);
                
                foreach ($tnew as $t) {
                    if ($jumlahBayar <= 0) break; // Berhenti jika uang sudah habis digunakan
                    
                    $sisaTagihan = floatval($t->sisa_pembayaran);
                    
                    if ($jumlahBayar >= $sisaTagihan) {
                        // Kasus A: Uang cukup/lebih untuk melunasi satu bulan ini
                        DB::table('tagihan_siswa')
                            ->where('id', $t->id)
                            ->update([
                                'sisa_pembayaran' => 0,
                                'updated_at' => '2025-12-31 23:59:00'
                            ]);

                        DB::table('pembayaran_detail')->insert([
                            'pembayaran_id'     => $pembayaranId,
                            'tagihan_siswa_id'  => $t->id,
                            'jumlah_bayar'      => $sisaTagihan,
                        ]);
                        
                        $jumlahBayar -= $sisaTagihan; // Kurangi sisa uang
                    }
                }

                // $t = $tagihans->where('kategori', $kat)->first();
                // DB::table('pembayaran_detail')->insert([
                //     'pembayaran_id'     => $pembayaranId,
                //     'tagihan_siswa_id'  => $t->id,
                //     'jumlah_bayar'      => floatval($record[$kat]),
                // ]);

                // DB::table('tagihan_siswa')
                //     ->where('id', $t->id)
                //     ->update([
                //     'sisa_pembayaran'   => floatval($record[$kat]),
                // ]);
                } catch (\Exception $e) {
                    dd($e->getMessage(), $t, $kat, $record);
                }
            }
        }
    }
}
