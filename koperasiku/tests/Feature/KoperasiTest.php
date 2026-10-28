<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KoperasiTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, bool $aktif = true): User
    {
        return User::factory()->create(['role' => $role, 'is_active' => $aktif]);
    }

    private function anggota(array $override = []): Anggota
    {
        return Anggota::create(array_merge([
            'no_anggota' => 'AGT-001',
            'nik' => '3273000000000001',
            'nama' => 'Tester',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Contoh',
            'no_hp' => '0812',
            'tanggal_bergabung' => now()->toDateString(),
            'status' => 'aktif',
        ], $override));
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('anggotas.index'))->assertRedirect(route('login'));
    }

    public function test_login_dan_logout(): void
    {
        $user = $this->user('admin');

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_akun_nonaktif_tidak_bisa_login(): void
    {
        $user = $this->user('admin', false);

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_pengurus_bisa_tambah_anggota_dengan_validasi_unik(): void
    {
        $this->actingAs($this->user('pengurus'));

        $payload = [
            'no_anggota' => 'AGT-100',
            'nik' => '3273000000000100',
            'nama' => 'Anggota Baru',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jl. Baru',
            'no_hp' => '0813',
            'tanggal_bergabung' => now()->toDateString(),
            'status' => 'aktif',
        ];

        $this->post(route('anggotas.store'), $payload)
            ->assertRedirect(route('anggotas.index'));
        $this->assertDatabaseHas('anggota', ['no_anggota' => 'AGT-100']);

        $this->post(route('anggotas.store'), $payload)
            ->assertSessionHasErrors(['no_anggota', 'nik']);
    }

    public function test_bendahara_tidak_boleh_mengubah_anggota(): void
    {
        $this->actingAs($this->user('bendahara'));

        $this->get(route('anggotas.index'))->assertOk();
        $this->get(route('anggotas.create'))->assertForbidden();
    }

    public function test_anggota_dengan_transaksi_tidak_bisa_dihapus(): void
    {
        $admin = $this->user('admin');
        $anggota = $this->anggota();

        Simpanan::create([
            'anggota_id' => $anggota->id,
            'kode_transaksi' => 'SMP-TEST-1',
            'jenis' => 'pokok',
            'nominal' => 100000,
            'tanggal_transaksi' => now()->toDateString(),
            'dibuat_oleh' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('anggotas.destroy', $anggota))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('anggota', ['id' => $anggota->id]);
    }

    public function test_alur_simpanan_dibuat_diverifikasi_lalu_terkunci(): void
    {
        $bendahara = $this->user('bendahara');
        $anggota = $this->anggota();

        $this->actingAs($bendahara)->post(route('simpanans.store'), [
            'anggota_id' => $anggota->id,
            'jenis' => 'wajib',
            'nominal' => 100000,
            'tanggal_transaksi' => now()->toDateString(),
        ])->assertRedirect();

        $simpanan = Simpanan::firstOrFail();
        $this->assertSame('menunggu', $simpanan->status);

        $this->post(route('simpanans.verifikasi', $simpanan))->assertRedirect();
        $this->assertSame('terverifikasi', $simpanan->fresh()->status);
        $this->assertSame($bendahara->id, $simpanan->fresh()->diverifikasi_oleh);

        // Sudah terverifikasi: tidak boleh diedit, dibatalkan, atau diproses ulang.
        $this->get(route('simpanans.edit', $simpanan))->assertForbidden();
        $this->delete(route('simpanans.destroy', $simpanan))->assertForbidden();
        $this->post(route('simpanans.verifikasi', $simpanan))->assertStatus(422);
    }

    public function test_nominal_harus_positif_dan_anggota_harus_aktif(): void
    {
        $this->actingAs($this->user('bendahara'));
        $nonaktif = $this->anggota(['status' => 'nonaktif']);

        $this->post(route('simpanans.store'), [
            'anggota_id' => $nonaktif->id,
            'jenis' => 'pokok',
            'nominal' => 0,
            'tanggal_transaksi' => now()->toDateString(),
        ])->assertSessionHasErrors(['anggota_id', 'nominal']);
    }

    public function test_penolakan_wajib_ada_alasan(): void
    {
        $bendahara = $this->user('bendahara');
        $anggota = $this->anggota();

        $simpanan = Simpanan::create([
            'anggota_id' => $anggota->id,
            'kode_transaksi' => 'SMP-TEST-2',
            'jenis' => 'wajib',
            'nominal' => 50000,
            'tanggal_transaksi' => now()->toDateString(),
        ]);

        $this->actingAs($bendahara)
            ->post(route('simpanans.tolak', $simpanan), ['alasan_penolakan' => ''])
            ->assertSessionHasErrors('alasan_penolakan');

        $this->post(route('simpanans.tolak', $simpanan), [
            'alasan_penolakan' => 'Bukti pembayaran tidak sesuai',
        ])->assertRedirect();

        $this->assertSame('ditolak', $simpanan->fresh()->status);
    }

    public function test_role_anggota_tidak_bisa_akses_simpanan_dan_user(): void
    {
        $this->actingAs($this->user('anggota'));

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('simpanans.index'))->assertForbidden();
        $this->get(route('users.index'))->assertForbidden();
    }

    public function test_hanya_admin_boleh_kelola_user(): void
    {
        $this->actingAs($this->user('pengurus'))
            ->get(route('users.index'))->assertForbidden();

        $this->actingAs($this->user('admin'))
            ->get(route('users.index'))->assertOk();
    }
}
