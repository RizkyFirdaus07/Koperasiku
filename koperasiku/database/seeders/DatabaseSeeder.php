<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun awal. GANTI password sebelum aplikasi dipakai sungguhan.
        $admin = User::updateOrCreate(
            ['email' => 'admin@koperasi.test'],
            ['name' => 'Administrator', 'password' => 'password', 'role' => 'admin', 'is_active' => true]
        );

        User::updateOrCreate(
            ['email' => 'pengurus@koperasi.test'],
            ['name' => 'Budi Pengurus', 'password' => 'password', 'role' => 'pengurus', 'is_active' => true]
        );

        $bendahara = User::updateOrCreate(
            ['email' => 'bendahara@koperasi.test'],
            ['name' => 'Siti Bendahara', 'password' => 'password', 'role' => 'bendahara', 'is_active' => true]
        );

        // Data contoh anggota
        $data = [
            ['Ahmad Fauzi',      'L', 'Bandung',    '1988-03-12', 'Jl. Merdeka No. 12, Bandung',        '081234567801'],
            ['Siti Nurhaliza',   'P', 'Jakarta',    '1991-07-25', 'Jl. Kenanga No. 5, Jakarta Selatan', '081234567802'],
            ['Budi Santoso',     'L', 'Semarang',   '1985-11-02', 'Jl. Pemuda No. 77, Semarang',        '081234567803'],
            ['Dewi Lestari',     'P', 'Yogyakarta', '1993-01-18', 'Jl. Malioboro No. 3, Yogyakarta',    '081234567804'],
            ['Rudi Hartono',     'L', 'Surabaya',   '1979-09-30', 'Jl. Darmo No. 41, Surabaya',         '081234567805'],
            ['Rina Marlina',     'P', 'Bogor',      '1990-05-09', 'Jl. Pajajaran No. 18, Bogor',        '081234567806'],
            ['Agus Setiawan',    'L', 'Malang',     '1987-12-21', 'Jl. Ijen No. 9, Malang',             '081234567807'],
            ['Nur Aini',         'P', 'Medan',      '1995-04-14', 'Jl. Gatot Subroto No. 22, Medan',    '081234567808'],
            ['Hendra Wijaya',    'L', 'Palembang',  '1983-08-07', 'Jl. Sudirman No. 60, Palembang',     '081234567809'],
            ['Maya Puspita',     'P', 'Makassar',   '1992-02-27', 'Jl. Pettarani No. 14, Makassar',     '081234567810'],
            ['Eko Prasetyo',     'L', 'Solo',       '1986-06-16', 'Jl. Slamet Riyadi No. 8, Surakarta', '081234567811'],
            ['Lilis Suryani',    'P', 'Cirebon',    '1989-10-03', 'Jl. Siliwangi No. 33, Cirebon',      '081234567812'],
        ];

        foreach ($data as $i => [$nama, $jk, $tempat, $lahir, $alamat, $hp]) {
            $n = $i + 1;

            $anggota = Anggota::updateOrCreate(
                ['no_anggota' => sprintf('AGT-2026-%04d', $n)],
                [
                    'nik' => sprintf('3273%012d', 100000000000 + $n * 7919),
                    'nama' => $nama,
                    'jenis_kelamin' => $jk,
                    'tempat_lahir' => $tempat,
                    'tanggal_lahir' => $lahir,
                    'alamat' => $alamat,
                    'no_hp' => $hp,
                    'email' => Str::slug($nama, '.') . '@contoh.test',
                    'tanggal_bergabung' => now()->subMonths(12 - $i)->toDateString(),
                    // anggota terakhir nonaktif agar filter status bisa dicoba
                    'status' => $n === count($data) ? 'nonaktif' : 'aktif',
                ]
            );

            // Simpanan contoh hanya dibuat sekali (saat belum ada transaksi).
            if ($anggota->simpanan()->exists()) {
                continue;
            }

            $this->buatSimpanan($anggota, 'pokok', 500000, $anggota->tanggal_bergabung, 'terverifikasi', $admin, $bendahara);

            foreach ([2, 1] as $bulanLalu) {
                $this->buatSimpanan(
                    $anggota, 'wajib', 100000,
                    now()->subMonths($bulanLalu)->startOfMonth()->addDays(4),
                    'terverifikasi', $admin, $bendahara,
                    'Simpanan wajib ' . now()->subMonths($bulanLalu)->format('m/Y')
                );
            }

            // Variasi status agar fitur verifikasi/filter bisa langsung dicoba.
            if ($n % 3 === 0) {
                $this->buatSimpanan($anggota, 'sukarela', 250000 + $n * 10000, now()->subDays($n), 'menunggu', $admin);
            } elseif ($n % 4 === 0) {
                $this->buatSimpanan($anggota, 'sukarela', 150000, now()->subDays($n), 'terverifikasi', $admin, $bendahara);
            }
        }
    }

    private function buatSimpanan(
        Anggota $anggota,
        string $jenis,
        int|float $nominal,
        $tanggal,
        string $status,
        User $pembuat,
        ?User $verifikator = null,
        ?string $keterangan = null,
    ): void {
        $terverifikasi = $status === 'terverifikasi';

        Simpanan::create([
            'anggota_id' => $anggota->id,
            'kode_transaksi' => 'SMP-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5)),
            'jenis' => $jenis,
            'nominal' => $nominal,
            'tanggal_transaksi' => $tanggal,
            'status' => $status,
            'keterangan' => $keterangan,
            'dibuat_oleh' => $pembuat->id,
            'diverifikasi_oleh' => $terverifikasi ? $verifikator?->id : null,
            'diverifikasi_pada' => $terverifikasi ? now() : null,
        ]);
    }
}
