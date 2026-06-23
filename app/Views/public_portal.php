<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventraZ - Portal Awam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root { --maroon: #8a0028; --maroon-dark: #520018; --gold: #ffc20e; --ink: #231f20; }
        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at 12% 12%, rgba(255, 194, 14, .22), transparent 28%),
                radial-gradient(circle at 85% 14%, rgba(138, 0, 40, .16), transparent 30%),
                linear-gradient(135deg, #fffaf0 0%, #f8eef2 48%, #fff8df 100%);
            color: var(--ink);
        }
        .sidebar {
            background: linear-gradient(160deg, rgba(82, 0, 24, .92), rgba(138, 0, 40, .82));
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            border-right: 1px solid rgba(255,255,255,.25);
            box-shadow: 24px 0 60px rgba(82, 0, 24, .22);
            backdrop-filter: blur(24px) saturate(160%);
            -webkit-backdrop-filter: blur(24px) saturate(160%);
        }
        .active-tab { background-color: #ffc20e !important; color: #520018 !important; }
        .brand-logo {
            width: 172px; background: #fff; border-radius: 28px; padding: 6px;
            filter: drop-shadow(0 14px 20px rgba(82, 0, 24, .16));
        }
        .glass-card {
            background: rgba(255,255,255,.58);
            border: 1px solid rgba(255,255,255,.82);
            box-shadow: 0 24px 58px rgba(82, 0, 24, .12), inset 0 1px 0 rgba(255,255,255,.9);
            backdrop-filter: blur(26px) saturate(160%);
            -webkit-backdrop-filter: blur(26px) saturate(160%);
        }
        .eventraz-field { background: rgba(255,255,255,.58) !important; border-color: rgba(138,0,40,.15) !important; }
        .eventraz-field:focus { box-shadow: 0 0 0 3px rgba(255,194,14,.28); }
        .program-select, .program-select option { background: #fff !important; color: #111827 !important; }
        .eventraz-btn { background: linear-gradient(135deg, var(--maroon), var(--maroon-dark)); box-shadow: 0 18px 36px rgba(138,0,40,.2); }
        .eventraz-btn:hover { filter: brightness(1.08); }
        #subProgramRow {
            overflow: hidden; max-height: 0; opacity: 0;
            transition: max-height 0.35s ease, opacity 0.3s ease;
        }
        #subProgramRow.visible { max-height: 120px; opacity: 1; }
        .select-loading { opacity: 0.5; pointer-events: none; }

        /* Page sections */
        .page-section { display: none; }
        .page-section.active { display: block; }

        /* Modal */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 50;
            background: rgba(0,0,0,.5); backdrop-filter: blur(4px);
            align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }

        /* PIC Info Card */
        .pic-info-card {
            background: linear-gradient(135deg, #fff8e7, #fff5d6);
            border: 1px solid #ffc20e;
            border-radius: 16px;
            padding: 16px 20px;
            margin-top: 12px;
        }
        .pic-info-card .icon {
            color: #8a0028;
            width: 20px;
            text-align: center;
        }

        @media (max-width: 900px) {
            body.flex { display: block; }
            .sidebar { position: relative; width: 100%; height: auto; }
            .ml-\[280px\] { margin-left: 0 !important; }
            .grid-cols-2 { grid-template-columns: repeat(1, minmax(0, 1fr)) !important; }
        }
    </style>
</head>
<body class="flex">

    <!-- ══════════════════════════ SIDEBAR ══════════════════════════ -->
    <div class="sidebar p-6 flex flex-col justify-between text-white shadow-2xl z-10">
        <div>
            <div class="mb-8 border-b border-white/15 pb-4 text-center">
                <img src="<?= base_url('assets/eventraz-logo.jpeg') ?>" alt="EventraZ" class="brand-logo mx-auto mb-3">
                <h1 class="text-lg font-black text-white tracking-wider">EventraZ Public</h1>
                <?php if (session('logged_in')): ?>
                    <p class="text-[10px] text-white mt-2 font-semibold"><?= esc(session('name')) ?></p>
                    <p class="text-[9px] text-yellow-100"><?= esc(session('email')) ?></p>
                <?php else: ?>
                    <p class="text-[9px] text-yellow-200 uppercase mt-1">Pendaftaran Terbuka</p>
                <?php endif; ?>
            </div>
            <nav class="space-y-3">
                <button onclick="tunjukSeksyen('daftar')" id="menuDaftar"
                    class="w-full text-left p-4 text-xs font-bold active-tab flex items-center gap-3 rounded-xl transition-all shadow-md">
                    <i class="fa-solid fa-file-signature"></i> DAFTAR PROGRAM
                </button>
                <?php if (session('logged_in')): ?>
                <button onclick="tunjukSeksyen('saya')" id="menuSaya"
                    class="w-full text-left p-4 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-clipboard-list"></i> PENDAFTARAN SAYA
                </button>
                <?php endif; ?>
                <button onclick="window.location.href='<?= base_url('public/events') ?>'" 
                    class="w-full text-left p-4 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-calendar-days"></i> ACARA
                </button>
            </nav>
        </div>
        <?php $isLoggedIn = (bool) session('logged_in'); ?>
        <a href="<?= base_url($isLoggedIn ? 'logout' : '/') ?>"
            class="text-xs text-yellow-100 font-bold p-3 flex items-center gap-2 hover:text-white hover:bg-white/10 rounded-xl transition-all">
            <i class="fa-solid <?= $isLoggedIn ? 'fa-power-off' : 'fa-arrow-left' ?>"></i>
            <?= $isLoggedIn ? 'LOG KELUAR PORTAL' : 'KEMBALI KE LOGIN' ?>
        </a>
    </div>

    <!-- ══════════════════════════ MAIN CONTENT ══════════════════════════ -->
    <div class="ml-[280px] w-full p-10 flex justify-center items-start min-h-screen py-12">

        <!-- ── SECTION: DAFTAR ── -->
        <div id="seksyenDaftar" class="page-section active w-full max-w-3xl">
            <div class="glass-card p-10 rounded-3xl">
                <h2 class="text-2xl font-bold text-[#520018] mb-2 uppercase">Pendaftaran Orang Awam</h2>
                <p class="text-xs text-slate-400 mb-8 border-b pb-4">Sila lengkapkan maklumat di bawah untuk mendaftar program.</p>

                <form id="formPublic" class="space-y-6">

                    <!-- MAIN PROGRAM -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Program Utama *</label>
                        <select name="mainProgramId" id="mainProgList"
                            class="program-select eventraz-field w-full p-4 border rounded-2xl outline-none text-sm transition-all"
                            onchange="onMainProgramChange()" required>
                            <option value="">-- Sila Pilih Program Utama --</option>
                        </select>
                    </div>

                    <!-- PIC INFO DISPLAY -->
                    <div id="picInfoContainer" style="display:none;" class="pic-info-card">
                        <div class="flex items-center gap-3 text-sm">
                            <i class="fa-solid fa-user-tie icon"></i>
                            <span class="font-bold text-slate-600">PIC / Penganjur:</span>
                            <span id="displayPicNama" class="font-semibold text-[#520018]">-</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm mt-2">
                            <i class="fa-solid fa-phone icon"></i>
                            <span class="font-bold text-slate-600">No. Telefon PIC:</span>
                            <span id="displayPicTel" class="font-semibold text-[#520018]">-</span>
                        </div>
                    </div>

                    <!-- SUB PROGRAM -->
                    <div id="subProgramRow">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Sub Program *</label>
                        <select name="subProgramId" id="subProgList"
                            class="program-select eventraz-field w-full p-4 border rounded-2xl outline-none text-sm transition-all"
                            onchange="onSubProgramChange()">
                            <option value="">-- Sila Pilih Sub Program --</option>
                        </select>
                    </div>

                    <!-- PENDAFTAR INFO -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Nama Penuh Pemohon *</label>
                        <input type="text" name="namaPenuh" placeholder="Masukkan nama penuh anda"
                            class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">No. Kad Pengenalan *</label>
                            <input type="text" name="noIC" placeholder="Contoh: 95010111XXXX"
                                class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">No. Telefon *</label>
                            <input type="text" name="telAwam" placeholder="Contoh: 013XXXXXXX"
                                class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Emel *</label>
                        <input type="email" name="email" placeholder="Contoh: nama@emel.com"
                            class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                    </div>

                    <!-- FAMILY MEMBERS -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Bilangan Ahli Keluarga / Peserta Turut Serta (Maks 10) *</label>
                        <input type="number" name="bilAhli" id="bilAhli" min="1" max="10"
                            oninput="janaBorangAhli()"
                            placeholder="Taip bilangan ahli keluarga yang akan turut serta"
                            class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                    </div>

                    <div id="boxAhli" class="space-y-3"></div>

                    <button type="button" onclick="hantarBorang()"
                        class="eventraz-btn w-full text-white font-bold py-4 rounded-2xl uppercase tracking-widest text-xs transition-all active:scale-[0.98]">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Hantar Pendaftaran
                    </button>
                </form>
            </div>
        </div>

        <!-- ── SECTION: PENDAFTARAN SAYA ── -->
        <?php if (session('logged_in')): ?>
        <div id="seksyenSaya" class="page-section w-full max-w-3xl">
            <div class="glass-card p-10 rounded-3xl">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-[#520018] uppercase">Pendaftaran Saya</h2>
                        <p class="text-xs text-slate-400 mt-1">Senarai program yang telah anda daftarkan.</p>
                    </div>
                    <button onclick="muatPendaftaranSaya()"
                        class="text-xs text-[#8a0028] font-bold hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-rotate-right"></i> Muat Semula
                    </button>
                </div>
                <div id="senaraiPendaftaranSaya">
                    <p class="text-center text-slate-400 text-sm py-12">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuatkan...
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- end main -->

    <!-- ══════════════════════════ AHLI DETAIL MODAL ══════════════════════════ -->
    <div id="modalAhli" class="modal-overlay">
        <div class="glass-card w-full max-w-lg mx-4 rounded-3xl p-8 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-[#520018]"><i class="fa-solid fa-users mr-2"></i> Senarai Ahli / Peserta</h3>
                <button onclick="tutupModalAhli()" class="text-slate-400 hover:text-[#8a0028] text-2xl leading-none">&times;</button>
            </div>
            <div id="kandunganModalAhli"></div>
        </div>
    </div>

    <script>
        var hasSubPrograms = false;

        // ── Page section switching ──
        function tunjukSeksyen(seksyen) {
            document.querySelectorAll('.page-section').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('nav button').forEach(btn => {
                btn.classList.remove('active-tab');
                btn.classList.add('text-yellow-100', 'hover:bg-white/10');
            });

            if (seksyen === 'daftar') {
                document.getElementById('seksyenDaftar').classList.add('active');
                var btn = document.getElementById('menuDaftar');
                btn.classList.add('active-tab');
                btn.classList.remove('text-yellow-100', 'hover:bg-white/10');
            } else if (seksyen === 'saya') {
                document.getElementById('seksyenSaya').classList.add('active');
                var btn = document.getElementById('menuSaya');
                btn.classList.add('active-tab');
                btn.classList.remove('text-yellow-100', 'hover:bg-white/10');
                muatPendaftaranSaya();
            }
        }

        // ── Load main programs ──
        window.onload = function () {
            muatProgramUtama();
            window.addEventListener('focus', muatProgramUtama);
            setInterval(muatProgramUtama, 30000);
        };

        async function muatProgramUtama() {
            try {
                const res  = await fetch('<?= base_url('public/programs') ?>?t=' + Date.now(), { cache: 'no-store' });
                const list = await res.json();
                var sel      = document.getElementById('mainProgList');
                var selected = sel.value;
                sel.innerHTML = '<option value="">-- Sila Pilih Program Utama --</option>';
                list.forEach(p => {
                    var opt = document.createElement('option');
                    opt.value = p.id; 
                    opt.textContent = p.nama;
                    opt.dataset.picNama = p.pic_nama || '-';
                    opt.dataset.picTel = p.pic_tel || '-';
                    sel.appendChild(opt);
                });
                if ([...sel.options].some(o => o.value === selected)) sel.value = selected;
                if (sel.value) {
                    onMainProgramChange();
                }
            } catch (err) { console.error('Gagal memuatkan senarai program utama', err); }
        }

        // ── Sub programs ──
        async function onMainProgramChange() {
            var mainId = document.getElementById('mainProgList').value;
            var subRow = document.getElementById('subProgramRow');
            var subSel = document.getElementById('subProgList');
            var picContainer = document.getElementById('picInfoContainer');
            var picNamaDisplay = document.getElementById('displayPicNama');
            var picTelDisplay = document.getElementById('displayPicTel');
            
            subSel.innerHTML = '<option value="">-- Sila Pilih Sub Program --</option>';
            subRow.classList.remove('visible');
            hasSubPrograms = false;
            picContainer.style.display = 'none';
            
            if (!mainId) return;
            
            // Display PIC info for main program
            try {
                const res = await fetch('<?= base_url('public/program-details') ?>/' + mainId + '?t=' + Date.now(), { cache: 'no-store' });
                const result = await res.json();
                if (result.success && result.program) {
                    picNamaDisplay.textContent = result.program.pic_nama || '-';
                    picTelDisplay.textContent = result.program.pic_tel || '-';
                    picContainer.style.display = 'block';
                }
            } catch (err) {
                console.error('Gagal memuatkan maklumat PIC', err);
            }
            
            try {
                subSel.classList.add('select-loading');
                const res = await fetch('<?= base_url('public/subprograms') ?>/' + mainId + '?t=' + Date.now(), { cache: 'no-store' });
                const list = await res.json();
                subSel.classList.remove('select-loading');
                if (list.length > 0) {
                    list.forEach(p => {
                        var opt = document.createElement('option');
                        opt.value = p.id; 
                        opt.textContent = p.nama;
                        opt.dataset.picNama = p.pic_nama || '-';
                        opt.dataset.picTel = p.pic_tel || '-';
                        subSel.appendChild(opt);
                    });
                    subRow.classList.add('visible');
                    hasSubPrograms = true;
                }
            } catch (err) {
                subSel.classList.remove('select-loading');
                console.error('Gagal memuatkan sub program', err);
            }
        }

        // ── Sub program change handler ──
        function onSubProgramChange() {
            var subSel = document.getElementById('subProgList');
            var selectedOption = subSel.options[subSel.selectedIndex];
            var picContainer = document.getElementById('picInfoContainer');
            var picNamaDisplay = document.getElementById('displayPicNama');
            var picTelDisplay = document.getElementById('displayPicTel');
            
            if (selectedOption && selectedOption.value) {
                picNamaDisplay.textContent = selectedOption.dataset.picNama || '-';
                picTelDisplay.textContent = selectedOption.dataset.picTel || '-';
                picContainer.style.display = 'block';
            } else {
                // If no sub selected, show main program PIC
                var mainSel = document.getElementById('mainProgList');
                var mainOption = mainSel.options[mainSel.selectedIndex];
                if (mainOption && mainOption.value) {
                    picNamaDisplay.textContent = mainOption.dataset.picNama || '-';
                    picTelDisplay.textContent = mainOption.dataset.picTel || '-';
                    picContainer.style.display = 'block';
                }
            }
        }

        // ── Generate family member fields ──
        function janaBorangAhli() {
            var bil = parseInt(document.getElementById('bilAhli').value) || 0;
            if (bil > 10) { bil = 10; document.getElementById('bilAhli').value = 10; }
            var box = document.getElementById('boxAhli');
            box.innerHTML = '';
            for (var i = 0; i < bil; i++) {
                box.innerHTML += `
                    <div class="grid grid-cols-2 gap-4 p-4 border border-yellow-100 rounded-2xl bg-white/35">
                        <div>
                            <label class="block text-[9px] font-bold text-[#8a0028] uppercase mb-1">Nama Penuh Ahli / Peserta ${i + 1}</label>
                            <input type="text" name="namaAhli_${i}" placeholder="Nama penuh"
                                class="eventraz-field w-full p-3 border rounded-xl text-xs outline-none" required>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-[#8a0028] uppercase mb-1">No. MyKid / IC Ahli ${i + 1}</label>
                            <input type="text" name="icAhli_${i}" placeholder="Tanpa tanda (-)"
                                class="eventraz-field w-full p-3 border rounded-xl text-xs outline-none" required>
                        </div>
                    </div>`;
            }
        }

        // ── Submit form ──
        async function hantarBorang() {
            var mainId = document.getElementById('mainProgList').value;
            var subId  = document.getElementById('subProgList').value;

            if (!mainId) {
                Swal.fire({ icon: 'warning', title: 'Program Utama diperlukan', text: 'Sila pilih Program Utama terlebih dahulu.' });
                return;
            }
            if (hasSubPrograms && !subId) {
                Swal.fire({ icon: 'warning', title: 'Sub Program diperlukan', text: 'Sila pilih Sub Program untuk program ini.' });
                return;
            }

            var form   = document.getElementById('formPublic');
            var inputs = form.querySelectorAll('[required]');
            for (var inp of inputs) {
                if (inp.name === 'subProgramId' && !hasSubPrograms) continue;
                if (!inp.value.trim()) {
                    Swal.fire({ icon: 'warning', title: 'Borang tidak lengkap', text: 'Sila isi semua medan yang bertanda *.' });
                    inp.focus(); return;
                }
            }

            Swal.fire({ title: 'Sila tunggu...', text: 'Sedang merekodkan pendaftaran...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            var data = new FormData(form);
            var bil  = parseInt(document.getElementById('bilAhli').value) || 0;
            data.append('bilAhli', bil);
            if (!hasSubPrograms) data.set('subProgramId', '');

            try {
                const res    = await fetch('<?= base_url('public/daftar') ?>', { method: 'POST', body: data });
                const result = await res.json();
                Swal.close();
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Pendaftaran Berjaya!', text: 'Maklumat anda telah selamat disimpan.' });
                    form.reset();
                    document.getElementById('boxAhli').innerHTML = '';
                    document.getElementById('subProgList').innerHTML = '<option value="">-- Sila Pilih Sub Program --</option>';
                    document.getElementById('subProgramRow').classList.remove('visible');
                    document.getElementById('picInfoContainer').style.display = 'none';
                    hasSubPrograms = false;
                } else {
                    Swal.fire({ icon: 'error', title: 'Ralat', text: result.message });
                }
            } catch (err) {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Ralat', text: 'Sila cuba sebentar lagi.' });
            }
        }

        // ── Load My Registrations ──
        async function muatPendaftaranSaya() {
            var box = document.getElementById('senaraiPendaftaranSaya');
            box.innerHTML = '<p class="text-center text-slate-400 text-sm py-12"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuatkan...</p>';

            try {
                const res    = await fetch('<?= base_url('public/my-registrations') ?>?t=' + Date.now());
                const result = await res.json();

                if (!result.success) {
                    box.innerHTML = '<p class="text-center text-slate-400 text-sm py-12">Gagal memuatkan data.</p>';
                    return;
                }

                if (!result.data || result.data.length === 0) {
                    box.innerHTML = `
                        <div class="text-center py-12">
                            <i class="fa-solid fa-clipboard text-4xl text-slate-300 mb-3"></i>
                            <p class="text-slate-400 text-sm">Tiada pendaftaran ditemui.</p>
                            <p class="text-slate-300 text-xs mt-1">Daftar program melalui tab "Daftar Program".</p>
                        </div>`;
                    return;
                }

                var html = '';
                result.data.forEach(reg => {
                    var tarikh = '';
                    if (reg.start_date) {
                        tarikh = reg.start_date === reg.end_date
                            ? formatTarikh(reg.start_date)
                            : formatTarikh(reg.start_date) + ' – ' + formatTarikh(reg.end_date);
                    } else {
                        tarikh = '<span class="italic text-slate-300">Tarikh belum ditetapkan</span>';
                    }

                    var badgeProg = reg.prog_status === 'AKTIF'
                        ? '<span class="bg-green-100 text-green-700 text-[9px] font-bold px-2 py-0.5 rounded-full">AKTIF</span>'
                        : '<span class="bg-red-100 text-red-700 text-[9px] font-bold px-2 py-0.5 rounded-full">TIDAK AKTIF</span>';

                    var badgeHadir = reg.status_hadir === 'Hadir'
                        ? '<span class="bg-blue-100 text-blue-700 text-[9px] font-bold px-2 py-0.5 rounded-full">✓ Hadir</span>'
                        : '<span class="bg-yellow-100 text-yellow-700 text-[9px] font-bold px-2 py-0.5 rounded-full">Belum Hadir</span>';

                    var ahliCount = reg.ahli ? reg.ahli.length : 0;
                    var ahliJson  = JSON.stringify(reg.ahli || []).replace(/'/g, "\\'");

                    html += `
                    <div class="border border-white/60 rounded-2xl p-5 mb-4 bg-white/40 hover:bg-white/60 transition-all">
                        <div class="flex justify-between items-start flex-wrap gap-2 mb-3">
                            <p class="font-bold text-[#520018] text-sm flex-1">${escHtml(reg.program_name)}</p>
                            <div class="flex gap-2 flex-wrap">${badgeProg} ${badgeHadir}</div>
                        </div>
                        <div class="space-y-1 text-xs text-slate-500">
                            <p><i class="fa-solid fa-calendar-days w-4 text-[#8a0028]"></i> ${tarikh}</p>
                            <p><i class="fa-solid fa-user w-4 text-[#8a0028]"></i> ${escHtml(reg.nama)}</p>
                            <p><i class="fa-solid fa-id-card w-4 text-[#8a0028]"></i> ${escHtml(reg.ic)}</p>
                            <p><i class="fa-solid fa-phone w-4 text-[#8a0028]"></i> ${escHtml(reg.tel)}</p>
                            <p><i class="fa-solid fa-envelope w-4 text-[#8a0028]"></i> ${escHtml(reg.email)}</p>
                            <div class="mt-2 pt-2 border-t border-dashed border-yellow-200">
                                <p class="text-[10px] text-slate-500">
                                    <i class="fa-solid fa-user-tie w-4 text-[#8a0028]"></i> 
                                    <span class="font-semibold">PIC:</span> ${escHtml(reg.pic_nama || '-')}
                                    <span class="mx-2 text-slate-300">|</span>
                                    <i class="fa-solid fa-phone w-4 text-[#8a0028]"></i> 
                                    <span class="font-semibold">Tel:</span> ${escHtml(reg.pic_tel || '-')}
                                </p>
                            </div>
                        </div>
                        ${ahliCount > 0 ? `
                        <div class="mt-3 pt-3 border-t border-white/50">
                            <button onclick='bukaModalAhli(${ahliJson})'
                                class="text-[10px] font-bold text-[#8a0028] hover:underline flex items-center gap-1">
                                <i class="fa-solid fa-users"></i> Lihat ${ahliCount} Ahli / Peserta Turut Serta
                            </button>
                        </div>` : ''}
                        <p class="text-[9px] text-slate-300 mt-3">Didaftar: ${escHtml(reg.timestamp)}</p>
                    </div>`;
                });

                box.innerHTML = html;
            } catch (err) {
                box.innerHTML = '<p class="text-center text-red-400 text-sm py-12">Ralat memuatkan data.</p>';
                console.error(err);
            }
        }

        // ── Family member modal ──
        function bukaModalAhli(ahliList) {
            var html = '<div class="space-y-2">';
            ahliList.forEach((ahli, i) => {
                html += `
                <div class="flex items-center gap-3 p-3 bg-white/50 rounded-xl border border-white/60">
                    <span class="w-7 h-7 rounded-full bg-[#8a0028] text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0">${i + 1}</span>
                    <div>
                    <p class="text-xs font-bold text-[#520018]">${escHtml(ahli.nama_ahli)}</p>
                    <p class="text-[10px] text-slate-400">IC / MyKid: ${escHtml(ahli.ic_ahli)}</p>
                    </div>
                </div>`;
            });
            html += '</div>';
            document.getElementById('kandunganModalAhli').innerHTML = html;
            document.getElementById('modalAhli').classList.add('open');
        }

        function tutupModalAhli() {
            document.getElementById('modalAhli').classList.remove('open');
        }

        // ── Helpers ──
        function formatTarikh(dateStr) {
            if (!dateStr) return '-';
            var d = new Date(dateStr);
            return d.toLocaleDateString('ms-MY', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        function escHtml(str) {
            if (!str) return '-';
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        // Close modal when clicking outside
        document.getElementById('modalAhli').addEventListener('click', function(e) {
            if (e.target === this) tutupModalAhli();
        });
    </script>
</body>
</html>