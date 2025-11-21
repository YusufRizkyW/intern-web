document.addEventListener('DOMContentLoaded', () => {
    // support both select (create) and radio (edit)
    const tipeSelect = document.getElementById('tipe_pendaftaran');
    const tipeRadios = Array.from(document.querySelectorAll('input[name="tipe_pendaftaran"][type="radio"]'));
    const formIndividu = document.getElementById('form_individu');
    const formTim = document.getElementById('form_tim');
    const anggotaList = document.getElementById('anggota_list');
    const addBtn = document.getElementById('add_member');

    const radioDurasi = document.querySelector('input[name="tipe_periode"][value="durasi"]');
    const radioTanggal = document.querySelector('input[name="tipe_periode"][value="tanggal"]');
    const durasiWrap = document.getElementById('durasi-wrapper');
    const tanggalWrap = document.getElementById('tanggal-wrapper');

    let count = typeof initialCount !== 'undefined' ? initialCount : 1;

    function getTipeValue() {
        if (tipeSelect) return tipeSelect.value;
        const checked = tipeRadios.find(r => r.checked);
        return checked ? checked.value : 'individu';
    }

    function onTipeChange(fn) {
        if (tipeSelect) {
            tipeSelect.addEventListener('change', fn);
        }
        if (tipeRadios.length) {
            tipeRadios.forEach(r => r.addEventListener('change', fn));
        }
    }

    function setDisabled(container, disabled) {
        if (!container) return;
        container.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    function toggleTipe() {
        const tipe = getTipeValue();

        if (tipe === 'tim') {
            if (formTim) {
                formTim.classList.remove('hidden');
                setDisabled(formTim, false);
            }
            if (formIndividu) {
                formIndividu.classList.add('hidden');
                setDisabled(formIndividu, true);
            }
        } else {
            if (formIndividu) {
                formIndividu.classList.remove('hidden');
                setDisabled(formIndividu, false);
            }
            if (formTim) {
                formTim.classList.add('hidden');
                setDisabled(formTim, true);
            }
        }
    }

    // Bind change on whichever control exists
    onTipeChange(toggleTipe);

    // initial toggle on load
    toggleTipe();

    // anggota dynamic
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

    // periode toggle
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

    // debug listener for submit (optional)
    const form = document.querySelector('form[method="POST"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Submitting form. tipe_pendaftaran =', getTipeValue());
            const agency = document.querySelector('input[name="agency"]');
            console.log('Agency present:', !!agency, agency ? agency.value : null);
        });
    }
});
