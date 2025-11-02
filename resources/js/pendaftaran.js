document.addEventListener('DOMContentLoaded', () => {
    const tipe = document.getElementById('tipe_pendaftaran');
    const formIndividu = document.getElementById('form_individu');
    const formTim = document.getElementById('form_tim');
    const anggotaList = document.getElementById('anggota_list');
    const addBtn = document.getElementById('add_member');

    const radioDurasi = document.querySelector('input[name="tipe_periode"][value="durasi"]');
    const radioTanggal = document.querySelector('input[name="tipe_periode"][value="tanggal"]');
    const durasiWrap = document.getElementById('durasi-wrapper');
    const tanggalWrap = document.getElementById('tanggal-wrapper');

    let count = typeof initialCount !== 'undefined' ? initialCount : 1;

    if (tipe) {
        function toggleTipe() {
            if (tipe.value === 'tim') {
                formTim.classList.remove('hidden');
                formIndividu.classList.add('hidden');
            } else {
                formIndividu.classList.remove('hidden');
                formTim.classList.add('hidden');
            }
        }

        tipe.addEventListener('change', toggleTipe);
        toggleTipe();
    }

    if (addBtn && anggotaList) {
        addBtn.addEventListener('click', () => {
            anggotaList.insertAdjacentHTML('beforeend', `
                <div class="border rounded p-3 bg-white space-y-2 mb-2">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-semibold text-gray-600">Peserta ${count + 1}</div>
                        <button type="button" class="text-[11px] text-red-500 remove-anggota">Hapus</button>
                    </div>
                    <input type="text" name="anggota[${count}][nama]" placeholder="Nama Lengkap"
                           class="w-full border rounded p-2 text-sm" required>
                    <input type="text" name="anggota[${count}][nim]" placeholder="NIM / NIS"
                           class="w-full border rounded p-2 text-sm">
                    <input type="email" name="anggota[${count}][email]" placeholder="Email"
                           class="w-full border rounded p-2 text-sm">
                    <input type="text" name="anggota[${count}][no_hp]" placeholder="No HP"
                           class="w-full border rounded p-2 text-sm">
                </div>
            `);
            count++;
        });

        anggotaList.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-anggota')) {
                e.target.closest('div.border').remove();
            }
        });
    }

    if (radioDurasi && radioTanggal) {
        radioDurasi.addEventListener('change', () => {
            durasiWrap.style.display = '';
            tanggalWrap.style.display = 'none';
        });
        radioTanggal.addEventListener('change', () => {
            durasiWrap.style.display = 'none';
            tanggalWrap.style.display = '';
        });
    }
});