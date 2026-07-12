@extends('layout.app')

@section('title', 'Artikel Kegiatan')

@section('content')

@include('sections.page-header', ['title' => 'Artikel Kegiatan', 'subtitle' => 'Kelola artikel dan publikasi kegiatan'])

<div class="flex flex-wrap items-center gap-3 mb-4">
    <form method="GET" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'artikel.index') }}" id="filterForm" class="flex items-center gap-3 flex-1">

        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text"
                id="searchInput"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari judul artikel..."
                class="pl-9 pr-4 py-2 border border-gray-200 rounded-full text-sm w-64 focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>
    </form>
    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3 mt-4">
        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'artikel.export.pdf', request()->query()) }}"
            class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm font-medium flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-download"></i> Ekspor PDF
        </a>
        <a href="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'artikel.export.excel', request()->query())  }}"
            class="border border-red-500 text-red-600 hover:bg-red-50 rounded-lg px-4 py-2 text-sm font-medium flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-file-excel"></i> Ekspor Excel
        </a>
    <button onclick="document.getElementById('modal-buat').classList.remove('hidden')"
        class="bg-red-600 text-white rounded-full px-5 py-2 font-semibold text-sm hover:bg-red-700 flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Buat Artikel
    </button>
    
</div>

<div class="bg-white rounded-xl shadow overflow-hidden border border-gray-90 w-full">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Gambar</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Judul</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Kategori</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Penulis</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-400">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($artikels as $artikel)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    @if($artikel->gambar)
                        <img src="{{ Storage::url($artikel->gambar) }}" alt="" class="w-12 h-8 object-cover rounded">
                    @else
                        <div class="w-12 h-8 bg-gray-100 rounded flex items-center justify-center">
                            <i class="fa-solid fa-image text-gray-300 text-xs"></i>
                        </div>
                    @endif
                </td>
                <td class="px-4 py-3 font-medium text-gray-900 max-w-xs truncate">{{ $artikel->judul }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $artikel->kategori ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $artikel->penulis?->name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $artikel->tgl_terbit?->format('d M Y') ?? '-' }}</td>
                <td class="px-4 py-3">@include('components.badge-status', ['status' => $artikel->status])</td>
                <td class="px-4 py-3">
                    {{-- Preview --}}
                    <button 
                        onclick='openPreview(
                            @json($artikel->judul),
                            @json($artikel->konten),
                            @json($artikel->gambar ? Storage::url($artikel->gambar) : "")
                        )'
                        class="text-gray-400 hover:text-blue-500 mr-2">
                        <i class="fa-solid fa-eye"></i>
                    </button>

                    {{-- Edit --}}
                    <button 
                        onclick='openEditArtikel(
                            {{ $artikel->id }},
                            @json($artikel->judul),
                            @json($artikel->kategori),
                            @json($artikel->status),
                            @json($artikel->konten),
                            @json(route((auth()->user()->hasRole("superadmin") ? "superadmin." : "admin.") . "artikel.update", $artikel))
                        )'
                        class="text-gray-400 hover:text-yellow-500 mr-2">
                        <i class="fa-solid fa-pen"></i>
                    </button>

                    {{-- Delete --}}
                    <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'artikel.destroy', $artikel) }}" class="inline"
                        onsubmit="return confirm('Hapus artikel ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>

                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada artikel</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $artikels->links() }}</div>
</div>

{{-- Modal Buat Artikel --}}
<div id="modal-buat" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Buat Artikel</h3>
            <button onclick="document.getElementById('modal-buat').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route((auth()->user()->hasRole('superadmin') ? 'superadmin.' : 'admin.') . 'artikel.store') }}" enctype="multipart/form-data" onsubmit="document.getElementById('konten-buat').value = quilBuat.root.innerHTML">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                <input type="text" name="judul" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="Kegiatan">Kegiatan</option>
                        <option value="Berita">Berita</option>
                        <option value="Pengumuman">Pengumuman</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="DRAFT">DRAFT</option>
                        <option value="PUBLISHED">PUBLISHED</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Utama</label>
                <input type="file" name="gambar" accept="image/*"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konten</label>
                <div id="editor-buat"></div>
                <input type="hidden" name="konten" id="konten-buat">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-buat').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 rounded-lg px-4 py-2 text-sm">Batal</button>
                <button type="submit" onclick="document.getElementById('konten-buat').value = quilBuat.root.innerHTML"
                    class="bg-red-600 text-white font-semibold rounded-lg px-4 py-2 text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Artikel --}}
<div id="modal-edit" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Edit Artikel</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="form-edit" method="POST" enctype="multipart/form-data" onsubmit="document.getElementById('konten-edit').value = quilEdit.root.innerHTML">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                <input type="text" id="edit-judul" name="judul" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select id="edit-kategori" name="kategori" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="Kegiatan">Kegiatan</option>
                        <option value="Berita">Berita</option>
                        <option value="Pengumuman">Pengumuman</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="edit-status" name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="DRAFT">DRAFT</option>
                        <option value="PUBLISHED">PUBLISHED</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (kosongkan jika tidak diubah)</label>
                <input type="file" name="gambar" accept="image/*"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konten</label>
                <div id="editor-edit"></div>
                <input type="hidden" name="konten" id="konten-edit">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="border border-gray-300 text-gray-600 rounded-lg px-4 py-2 text-sm">Batal</button>
                <button type="submit" onclick="document.getElementById('konten-edit').value = quilEdit.root.innerHTML"
                    class="bg-red-600 text-white font-semibold rounded-lg px-4 py-2 text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- modal show --}}
<div id="modal-preview" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl p-6 max-h-screen overflow-y-auto">

        <div class="flex items-center justify-between mb-4">
            <h3 id="preview-judul" class="font-bold text-lg"></h3>

            <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        {{-- Gambar --}}
        <img id="preview-gambar" 
             class="w-full h-60 object-cover rounded-lg mb-4 hidden">

        <hr class="mb-4">

        {{-- Konten --}}
        <div id="preview-konten" class="prose max-w-none text-gray-700"></div>

    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.2.6/dist/purify.min.js"></script>
<script>
const quilBuat = new Quill('#editor-buat', { theme: 'snow', placeholder: 'Tulis konten artikel...' });
const quilEdit = new Quill('#editor-edit', { theme: 'snow' });

function openEditArtikel(id, judul, kategori, status, konten, updateUrl) {
    document.getElementById('edit-judul').value = judul;
    document.getElementById('edit-kategori').value = kategori;
    document.getElementById('edit-status').value = status;

    quilEdit.root.innerHTML = konten;

    document.getElementById('form-edit').action = updateUrl;
    document.getElementById('modal-edit').classList.remove('hidden');
}
function openPreview(judul, konten, gambar) {

    document.getElementById('preview-judul').innerText = judul;
    document.getElementById('preview-konten').innerHTML = konten;

    const img = document.getElementById('preview-gambar');

    if (gambar) {
        img.src = gambar;
        img.classList.remove('hidden');
    } else {
        img.classList.add('hidden');
    }

    document.getElementById('modal-preview').classList.remove('hidden');
}

function closePreview() {
    document.getElementById('modal-preview').classList.add('hidden');
}
let timer;

document.getElementById('searchInput').addEventListener('keyup', function () {

    clearTimeout(timer);

    timer = setTimeout(function () {
        document.getElementById('filterForm').submit();
    }, 500);

});
document.getElementById('konten-buat').value =
DOMPurify.sanitize(quilBuat.root.innerHTML,{
    ALLOWED_TAGS:[
        'p','br',
        'strong','b',
        'em','i',
        'u',
        'ul','ol','li',
        'h1','h2','h3','h4',
        'blockquote',
        'a',
        'img'
    ],
    ALLOWED_ATTR:[
        'href',
        'src',
        'alt'
    ]
});

</script>
@endpush
