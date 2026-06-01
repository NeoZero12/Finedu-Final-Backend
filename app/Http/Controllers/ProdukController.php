<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    public function index()
    {
        return response()->json(Produk::latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'kategori' => ['required', Rule::in(['kebutuhan', 'keinginan'])],
            'gambar_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $produk = Produk::create($validated);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $produk,
        ], 201);
    }

    public function show(Produk $produk)
    {
        return response()->json($produk);
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'kategori' => ['required', Rule::in(['kebutuhan', 'keinginan'])],
            'gambar_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $produk->update($validated);

        return response()->json([
            'message' => 'Produk berhasil diperbarui.',
            'data' => $produk->fresh(),
        ]);
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus.',
        ]);
    }
}
