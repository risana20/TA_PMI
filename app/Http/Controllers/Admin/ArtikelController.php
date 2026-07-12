<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Exports\ArtikelExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ArtikelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query  = Artikel::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                ->orWhere('Kategori', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('tgl_terbit', 'like', "%{$search}%")
                ->orWhere('user_id', 'like', "%{$search}%");
            });
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
            'gambar'   => 'required|image|max:10048',
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
            'gambar'   => 'nullable|image|max:10048',
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
    public function exportPdf(Request $request)
    {
        $query = Artikel::with('penulis');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('judul', 'like', "%{$request->search}%")
              ->orWhere('kategori', 'like', "%{$request->search}%")
              ->orWhere('status', 'like', "%{$request->search}%")
              ->orWhere('tgl_terbit', 'like', "%{$request->search}%");
        });
        }

        $artikels = $query->latest()->get();

        $pdf = Pdf::loadView('pages.admin.artikel.export_pdf', compact('artikels'));

        return $pdf->download('Laporan artikel.pdf');
    }

    // Export Excel (download .xlsx)

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new ArtikelExport(
                $request->status,
                $request->search
            ),
            'Laporan Artikel.xlsx'
        );
    }
}
