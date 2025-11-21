document.addEventListener('DOMContentLoaded', () => {
    // Fix: Gunakan selector untuk radio buttons, bukan getElementById
    const radioIndividu = document.getElementById('tipe_pendaftaran_individu');
    const radioTim = document.getElementById('tipe_pendaftaran_tim');
    const formIndividu = document.getElementById('form_individu');
    const formTim = document.getElementById('form_tim');
    const anggotaList = document.getElementById('anggota_list');
    const addBtn = document.getElementById('add_member');

    const radioDurasi = document.querySelector('input[name="tipe_periode"][value="durasi"]');
    const radioTanggal = document.querySelector('input[name="tipe_periode"][value="tanggal"]');
    const durasiWrap = document.getElementById('durasi-wrapper');
    const tanggalWrap = document.getElementById('tanggal-wrapper');

    let count = typeof initialCount !== 'undefined' ? initialCount : 1;

    function setDisabled(container, disabled) {
        container.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    function toggleTipe() {
        if (radioTim && radioTim.checked) {
            formTim.classList.remove('hidden');
            setDisabled(formTim, false);

            formIndividu.classList.add('hidden');
            setDisabled(formIndividu, true);
        } else {
            formIndividu.classList.remove('hidden');
            setDisabled(formIndividu, false);

            formTim.classList.add('hidden');
            setDisabled(formTim, true);
        }
    }

    if (radioIndividu && radioTim) {
        radioIndividu.addEventListener('change', toggleTipe);
        radioTim.addEventListener('change', toggleTipe);
        toggleTipe(); // Initial call
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

    // Debug: Pastikan form submit tidak diblok
    const form = document.querySelector('form[method="POST"]');
    if (form) {
        console.log('Form found:', form);
        form.addEventListener('submit', function(e) {
            console.log('Form submit triggered!', e);
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            // JANGAN preventDefault() - biarkan form submit normal
        });
    }
});