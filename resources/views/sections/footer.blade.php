<footer class="bg-gray-900 text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Main grid: 4 columns --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            {{-- Col 1: Brand --}}
            <div class="md:col-span-1">
                {{-- White logo card --}}
                <div class="bg-white rounded-xl px-4 py-3 inline-flex items-center gap-3 mb-5">
                    <img src="{{ asset('assets/logo-pmi.png') }}" alt="PMI" class="h-12 w-auto object-contain">
                    <div class="w-px h-7 bg-gray-200"></div>
                    <div class="leading-tight">
                        <p class="font-bold text-sm text-gray-900">Griya PMI</p>
                        <p class="text-[10px] text-gray-500 tracking-wide">SURAKARTA</p>
                    </div>
                </div>

                <p class="text-sm text-gray-400 leading-relaxed">
                    Memberikan pelayanan terbaik untuk ODGJ dan Lansia dengan penuh kasih sayang dan profesionalisme.
                </p>
            </div>

            {{-- Col 2: Tautan Cepat --}}
            <div>
                <h4 class="font-semibold text-sm text-white mb-5">Tautan Cepat</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="{{ route('beranda') }}" class="hover:text-white transition">Beranda</a></li>
                    <li><a href="{{ route('donasi.index') }}" class="hover:text-white transition">Donasi</a></li>
                    <li><a href="{{ route('kunjungan.index') }}" class="hover:text-white transition">Kunjungan</a></li>
                    <li><a href="{{ route('cek-status.index') }}" class="hover:text-white transition">Cek Status</a></li>
                </ul>
            </div>

            {{-- Col 3: Kontak --}}
            <div>
                <h4 class="font-semibold text-sm text-white mb-5">Kontak</h4>
                <ul class="space-y-4 text-sm text-gray-400">
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot text-red-500 mt-0.5 shrink-0"></i>
                        <span>Jalan Sumbing Raya No. 6, Mojosongo, Kecamatan Jebres, Kota Surakarta.</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-red-500 shrink-0"></i>
                        <span>info@griyapmi.id</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-phone text-red-500 shrink-0"></i>
                        <span>(022) 1234-5678</span>
                    </li>
                </ul>
            </div>

            {{-- Col 4: Ikuti Kami --}}
            <div>
                <h4 class="font-semibold text-sm text-white mb-5">Ikuti Kami</h4>
                <a href="#"
                   class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-gray-600 transition">
                    <i class="fa-brands fa-instagram text-white text-lg"></i>
                </a>
            </div>

        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-gray-800 mt-10 pt-6 flex flex-col md:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-500">&copy; {{ date('Y') }} Griya PMI. Semua hak dilindungi.</p>
            <div class="flex items-center gap-5 text-xs text-gray-500">
                <a href="#" class="hover:text-gray-300 transition">Kebijakan Privasi</a>
                <a href="#" class="hover:text-gray-300 transition">Syarat & Ketentuan</a>
            </div>
        </div>

    </div>
</footer>
