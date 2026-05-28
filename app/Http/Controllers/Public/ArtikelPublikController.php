<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;

class ArtikelPublikController extends Controller
{
    public function index(Request $request)
    {
        $q         = $request->get('q');
        $kategori  = $request->get('kategori');
        $query     = Artikel::published()->with('penulis')->latest();

        if ($q) {
            $query->where('judul', 'like', "%{$q}%");
        }
        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        $artikels  = $query->paginate(9)->withQueryString();
        $kategoris = Artikel::published()->whereNotNull('kategori')->distinct()->pluck('kategori');

        return view('pages.public.artikel.index', compact('artikels', 'kategoris'));
    }

    public function show(string $slug)
    {
        $artikel       = Artikel::published()->where('slug', $slug)->firstOrFail();
        $artikelTerkait = Artikel::published()->where('id', '!=', $artikel->id)->latest()->limit(4)->get();
        return view('pages.public.artikel.show', compact('artikel', 'artikelTerkait'));
    }
}
