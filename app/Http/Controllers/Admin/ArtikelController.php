<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtikelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query  = Artikel::query();

        if ($search) {
            $query->where('judul', 'like', "%{$search}%");
        }

        $artikels = $query->paginate(10)->withQueryString();
        return view('pages.admin.artikel.index', compact('artikels', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'    => 'required|string|max:255',
            'kategori' => 'nullable|string',
            'konten'   => 'required|string',
            'gambar'   => 'nullable|image|max:2048',
            'status'   => 'required|in:PUBLISHED,DRAFT',
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('artikel', 'public');
        }

        $data['user_id']    = Auth::id();
        $data['tgl_terbit'] = $data['status'] === 'PUBLISHED' ? now()->toDateString() : null;

        Artikel::create($data);
        return back()->with('success', 'Artikel berhasil disimpan.');
    }

    public function update(Request $request, Artikel $artikel)
    {
        $data = $request->validate([
            'judul'    => 'required|string|max:255',
            'kategori' => 'nullable|string',
            'konten'   => 'required|string',
            'gambar'   => 'nullable|image|max:2048',
            'status'   => 'required|in:PUBLISHED,DRAFT',
        ]);

        if ($request->hasFile('gambar')) {
            if ($artikel->gambar) Storage::disk('public')->delete($artikel->gambar);
            $data['gambar'] = $request->file('gambar')->store('artikel', 'public');
        }

        if ($data['status'] === 'PUBLISHED' && !$artikel->tgl_terbit) {
            $data['tgl_terbit'] = now()->toDateString();
        }

        $artikel->update($data);
        return back()->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Artikel $artikel)
    {
        if ($artikel->gambar) Storage::disk('public')->delete($artikel->gambar);
        $artikel->delete();
        return back()->with('success', 'Artikel berhasil dihapus.');
    }
}
