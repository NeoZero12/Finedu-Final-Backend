<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Simulasi;
use App\Models\TransaksiSimulasi;
use App\Support\SimulationSummary;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransaksiSimulasiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => ['nullable', 'exists:produks,id'],
            'nama_item' => ['nullable', 'string', 'max:255'],
            'nominal' => ['nullable', 'numeric', 'min:1000'],
            'kategori_label' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'arah_transaksi' => ['nullable', Rule::in(['pengeluaran', 'pemasukan'])],
        ]);

        $user = $request->user()->load('profil');

        $simulasi = Simulasi::firstOrCreate(
            [
                'user_id' => $user->id,
                'status' => 'berlangsung',
            ],
            [
                'anggaran_awal' => 0,
                'anggaran_sisa' => 0,
            ]
        );

        $payload = $this->resolveTransactionPayload($validated);
        return DB::transaction(function () use ($simulasi, $payload, $user) {
            $transaksi = TransaksiSimulasi::create([
                'simulasi_id' => $simulasi->id,
                'produk_id' => $payload['produk_id'],
                'nama_item' => $payload['nama_item'],
                'kategori_label' => $payload['kategori_label'],
                'catatan' => $payload['catatan'],
                'arah_transaksi' => $payload['arah_transaksi'],
                'nominal' => $payload['nominal'],
                'pembelian_impulsif' => $payload['pembelian_impulsif'],
            ]);

            $this->applyTransactionImpact($simulasi, $payload['nominal'], $payload['arah_transaksi']);

            return response()->json([
                'message' => $payload['arah_transaksi'] === 'pemasukan'
                    ? 'Budget berhasil ditambahkan.'
                    : 'Transaksi berhasil disimpan.',
                'data' => $transaksi->load('produk'),
                'simulasi' => SimulationSummary::format($simulasi->fresh(), $user->profil),
            ], 201);
        });
    }

    public function destroy(Request $request, TransaksiSimulasi $transaksi)
    {
        $simulasi = $transaksi->simulasi()->with('transaksi.produk')->firstOrFail();
        abort_unless($simulasi->user_id === $request->user()->id, 403);

        if ($simulasi->status !== 'berlangsung') {
            return response()->json([
                'message' => 'Sesi yang sudah selesai tersimpan sebagai laporan dan tidak bisa diubah lagi.',
            ], 422);
        }

        return DB::transaction(function () use ($request, $simulasi, $transaksi) {
            $this->revertTransactionImpact($simulasi, (float) $transaksi->nominal, $transaksi->arah_transaksi ?: 'pengeluaran');
            $transaksi->delete();

            return response()->json([
                'message' => 'Riwayat transaksi dihapus dan budget dikembalikan.',
                'simulasi' => SimulationSummary::format($simulasi->fresh(), $request->user()->profil),
            ]);
        });
    }

    public function update(Request $request, TransaksiSimulasi $transaksi)
    {
        $validated = $request->validate([
            'nama_item' => ['required', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'min:1000'],
            'kategori_label' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'arah_transaksi' => ['required', Rule::in(['pengeluaran', 'pemasukan'])],
        ]);

        $simulasi = $transaksi->simulasi()->with('transaksi.produk')->firstOrFail();
        abort_unless($simulasi->user_id === $request->user()->id, 403);

        if ($simulasi->status !== 'berlangsung') {
            return response()->json([
                'message' => 'Sesi yang sudah selesai tersimpan sebagai laporan dan tidak bisa diubah lagi.',
            ], 422);
        }

        return DB::transaction(function () use ($request, $simulasi, $transaksi, $validated) {
            $this->revertTransactionImpact($simulasi, (float) $transaksi->nominal, $transaksi->arah_transaksi ?: 'pengeluaran');

            $transaksi->update([
                'produk_id' => null,
                'nama_item' => trim($validated['nama_item']),
                'kategori_label' => $validated['kategori_label'] ?? ($validated['arah_transaksi'] === 'pemasukan' ? 'pemasukan' : 'manual'),
                'catatan' => $validated['catatan'] ?? null,
                'arah_transaksi' => $validated['arah_transaksi'],
                'nominal' => (float) $validated['nominal'],
                'pembelian_impulsif' => ($validated['kategori_label'] ?? '') === 'keinginan',
            ]);

            // Hitung ulang saldo dari nilai transaksi terbaru.
            $this->applyTransactionImpact($simulasi, (float) $validated['nominal'], $validated['arah_transaksi']);

            return response()->json([
                'message' => 'Transaksi berhasil diperbarui.',
                'data' => $transaksi->fresh('produk'),
                'simulasi' => SimulationSummary::format($simulasi->fresh(), $request->user()->profil),
            ]);
        });
    }

    public function clear(Request $request, Simulasi $simulasi)
    {
        abort_unless($simulasi->user_id === $request->user()->id, 403);

        if ($simulasi->status !== 'berlangsung') {
            return response()->json([
                'message' => 'Riwayat sesi yang sudah selesai tersimpan sebagai laporan dan tidak bisa dihapus.',
            ], 422);
        }

        return DB::transaction(function () use ($request, $simulasi) {
            $transaksiList = $simulasi->transaksi()->latest()->get();

            foreach ($transaksiList as $transaksi) {
                $this->revertTransactionImpact($simulasi, (float) $transaksi->nominal, $transaksi->arah_transaksi ?: 'pengeluaran');
            }

            $simulasi->transaksi()->delete();

            return response()->json([
                'message' => 'Semua riwayat transaksi dihapus dan budget dikembalikan.',
                'simulasi' => SimulationSummary::format($simulasi->fresh(), $request->user()->profil),
            ]);
        });
    }

    private function resolveTransactionPayload(array $validated): array
    {
        if (! empty($validated['produk_id'])) {
            $produk = Produk::findOrFail($validated['produk_id']);

            return [
                'produk_id' => $produk->id,
                'nama_item' => $produk->nama_produk,
                'kategori_label' => $produk->kategori,
                'catatan' => $validated['catatan'] ?? null,
                'arah_transaksi' => 'pengeluaran',
                'nominal' => (float) $produk->harga,
                'pembelian_impulsif' => $produk->kategori === 'keinginan',
            ];
        }

        $arahTransaksi = $validated['arah_transaksi'] ?? 'pengeluaran';
        $namaItem = trim((string) ($validated['nama_item'] ?? ''));
        $nominal = (float) ($validated['nominal'] ?? 0);

        if ($namaItem === '' || $nominal <= 0) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Nama transaksi dan nominal wajib diisi.',
                ], 422)
            );
        }

        return [
            'produk_id' => null,
            'nama_item' => $namaItem,
            'kategori_label' => $validated['kategori_label']
                ?? ($arahTransaksi === 'pemasukan' ? 'pemasukan' : 'manual'),
            'catatan' => $validated['catatan'] ?? null,
            'arah_transaksi' => $arahTransaksi,
            'nominal' => $nominal,
            'pembelian_impulsif' => false,
        ];
    }

    private function applyTransactionImpact(Simulasi $simulasi, float $nominal, string $arahTransaksi): void
    {
        $anggaranAwal = (float) $simulasi->anggaran_awal;
        $anggaranSisa = (float) $simulasi->anggaran_sisa;

        if ($arahTransaksi === 'pemasukan') {
            $anggaranAwal += $nominal;
            $anggaranSisa += $nominal;
        } else {
            $anggaranSisa -= $nominal;
        }

        $simulasi->update([
            'anggaran_awal' => $anggaranAwal,
            'anggaran_sisa' => $anggaranSisa,
        ]);
    }

    private function revertTransactionImpact(Simulasi $simulasi, float $nominal, string $arahTransaksi): void
    {
        $anggaranAwal = (float) $simulasi->anggaran_awal;
        $anggaranSisa = (float) $simulasi->anggaran_sisa;

        if ($arahTransaksi === 'pemasukan') {
            $anggaranAwal -= $nominal;
            $anggaranSisa -= $nominal;
        } else {
            $anggaranSisa += $nominal;
        }

        $simulasi->update([
            'anggaran_awal' => max(0, $anggaranAwal),
            'anggaran_sisa' => $anggaranSisa,
        ]);
    }
}
