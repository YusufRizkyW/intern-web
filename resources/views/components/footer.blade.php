{{-- resources/views/components/footer.blade.php --}}
<footer class="bg-[#052d57] text-white mt-12">
  <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- LEFT: logo + kontak -->
    <div class="lg:col-span-6 space-y-4">
      <div class="flex items-center gap-3">
        {{-- ganti src dengan path logo kalian --}}
        <img src="{{ asset('images/logo-bps.png') }}" alt="BPS" class="w-14 h-auto">
        <div class="text-sm">
          <div class="font-semibold">BADAN PUSAT STATISTIK</div>
        </div>
      </div>

      <div class="text-sm text-gray-200 space-y-1">
        <div>Badan Pusat Statistik Kabupaten Gresik (Statistics Gresik)</div>
        <div>Jl. Dr. Wahidin Sudirohusodo No. 364, Gresik</div>
        <div>Jawa Timur</div>
        <div>Telp: (62-31) 3954787</div>
        <div>Faks: (62-31) 3954787</div>
        <div>Mailbox: <a href="mailto:bps3525@bps.go.id" class="underline text-gray-100">bps3525@bps.go.id</a></div>
      </div>
    </div>

    <!-- RIGHT: Google Maps -->
    <div class="lg:col-span-6">
      <div class="bg-white rounded-lg overflow-hidden shadow-lg">
        <div class="bg-gray-100 px-4 py-2 border-b">
          <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
            Lokasi Kami
          </h3>
        </div>
        <div class="relative">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.2684891929737!2d112.65344347590614!3d-7.327012072009788!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7e1c9c5b5b5b5%3A0x1c2c2c2c2c2c2c2c!2sBPS%20Kabupaten%20Gresik!5e0!3m2!1sid!2sid!4v1699999999999!5m2!1sid!2sid"
            width="100%" 
            height="200" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade"
            class="w-full h-48">
          </iframe>
          <div class="absolute top-2 right-2">
            <a href="https://maps.app.goo.gl/242jjeAmS31xY5Dw6" 
               target="_blank"
               class="bg-white text-gray-700 px-2 py-1 rounded shadow-md text-xs hover:bg-gray-50 transition-colors flex items-center gap-1">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/>
              </svg>
              Buka di Maps
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="border-t border-[#174060]">
    <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="text-sm text-gray-300">Hak Cipta © {{ date('Y') }} Badan Pusat Statistik</div>

      <div class="flex items-center gap-3">
        {{-- Facebook --}}
        <a href="https://www.facebook.com/p/BPS-Kabupaten-Gresik-100064599285591/?locale=id_ID" 
           class="p-2 rounded-full bg-blue-600 hover:bg-blue-700 transition-colors" 
           target="_blank">
          <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M22.675 0h-21.35C.6 0 0 .6 0 1.325v21.351C0 23.4.6 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.894-4.788 4.659-4.788 1.325 0 2.463.099 2.794.143v3.24h-1.917c-1.504 0-1.796.715-1.796 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.725 0 1.325-.6 1.325-1.324V1.325C24 .6 23.4 0 22.675 0z"/>
          </svg>
        </a>

        {{-- Instagram --}}
        <a href="https://www.instagram.com/bps_gresik" 
           class="p-2 rounded-full bg-pink-500 hover:bg-pink-600 transition-colors" 
           target="_blank">
          <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.206.057 2.003.24 2.466.403a4.92 4.92 0 0 1 1.675.965 4.92 4.92 0 0 1 .965 1.675c.163.463.346 1.26.403 2.466.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.057 1.206-.24 2.003-.403 2.466a4.92 4.92 0 0 1-.965 1.675 4.92 4.92 0 0 1-1.675.965c-.463.163-1.26.346-2.466.403-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.206-.057-2.003-.24-2.466-.403a4.92 4.92 0 0 1-1.675-.965 4.92 4.92 0 0 1-.965-1.675c-.163-.463-.346-1.26-.403-2.466C2.175 15.584 2.163 15.204 2.163 12s.012-3.584.07-4.85c.057-1.206.24-2.003.403-2.466a4.92 4.92 0 0 1 .965-1.675 4.92 4.92 0 0 1 1.675-.965c.463-.163 1.26-.346 2.466-.403C8.416 2.175 8.796 2.163 12 2.163zm0-2.163C8.741 0 8.332.012 7.052.07 5.773.127 4.802.31 4.042.553a6.92 6.92 0 0 0-2.513 1.45A6.92 6.92 0 0 0 .553 4.042C.31 4.802.127 5.773.07 7.052.012 8.332 0 8.741 0 12c0 3.259.012 3.668.07 4.948.057 1.279.24 2.25.483 3.01a6.92 6.92 0 0 0 1.45 2.513 6.92 6.92 0 0 0 2.513 1.45c.76.243 1.731.426 3.01.483C8.332 23.988 8.741 24 12 24c3.259 0 3.668-.012 4.948-.07 1.279-.057 2.25-.24 3.01-.483a6.92 6.92 0 0 0 2.513-1.45 6.92 6.92 0 0 0 1.45-2.513c.243-.76.426-1.731.483-3.01.058-1.28.07-1.689.07-4.948 0-3.259-.012-3.668-.07-4.948-.057-1.279-.24-2.25-.483-3.01a6.92 6.92 0 0 0-1.45-2.513 6.92 6.92 0 0 0-2.513-1.45c-.76-.243-1.731-.426-3.01-.483C15.668.012 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm7.846-10.405a1.441 1.441 0 1 1-2.883 0 1.441 1.441 0 0 1 2.883 0z"/>
          </svg>
        </a>

        {{-- Twitter/X --}}
        <a href="https://www.twitter.com/bps_statistics" 
           class="p-2 rounded-full bg-black hover:bg-gray-800 transition-colors" 
           target="_blank">
          <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
          </svg>
        </a>

        {{-- YouTube --}}
        <a href="https://www.youtube.com/@bpskabupatengresik8130" 
           class="p-2 rounded-full bg-red-600 hover:bg-red-700 transition-colors" 
           target="_blank">
          <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M23.498 6.186a2.925 2.925 0 0 0-2.056-2.056C19.562 3.65 12 3.65 12 3.65s-7.562 0-9.442.48A2.925 2.925 0 0 0 .502 6.186C0 8.067 0 12 0 12s0 3.933.502 5.814a2.925 2.925 0 0 0 2.056 2.056c1.88.48 9.442.48 9.442.48s7.562 0 9.442-.48a2.925 2.925 0 0 0 2.056-2.056C24 15.933 24 12 24 12s0-3.933-.502-5.814zM9.75 15.568V8.432L15.5 12l-5.75 3.568z"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</footer>
