<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventraZ - Portal Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root { --maroon: #8a0028; --maroon-dark: #520018; --gold: #ffc20e; }
        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at 12% 12%, rgba(255,194,14,.22), transparent 28%),
                radial-gradient(circle at 85% 14%, rgba(138,0,40,.16), transparent 30%),
                linear-gradient(135deg, #fffaf0 0%, #f8eef2 48%, #fff8df 100%);
        }
        .sidebar { background: linear-gradient(160deg, rgba(82,0,24,.92), rgba(138,0,40,.82)); width: 280px; height: 100vh; position: fixed; top: 0; left: 0; border-right: 1px solid rgba(255,255,255,.25); box-shadow: 24px 0 60px rgba(82,0,24,.22); backdrop-filter: blur(24px) saturate(160%); }
        .active-tab { background-color: #ffc20e !important; color: #520018 !important; border-radius: 24px !important; }
        .brand-logo { width: 172px; background: #fff; border-radius: 28px; padding: 6px; filter: drop-shadow(0 14px 20px rgba(82,0,24,.16)); }
        .glass-card { background: rgba(255,255,255,.58); border: 1px solid rgba(255,255,255,.82); border-radius: 34px; box-shadow: 0 24px 58px rgba(82,0,24,.12), inset 0 1px 0 rgba(255,255,255,.9); backdrop-filter: blur(26px) saturate(160%); }
        .eventraz-field { background: rgba(255,255,255,.58) !important; border-color: rgba(138,0,40,.15) !important; border-radius: 24px !important; }
        .eventraz-field:focus { box-shadow: 0 0 0 3px rgba(255,194,14,.28); }
        .program-select, .program-select option { background: #fff !important; color: #111827 !important; }
        .eventraz-btn { background: linear-gradient(135deg, var(--maroon), var(--maroon-dark)); border-radius: 24px !important; box-shadow: 0 18px 36px rgba(138,0,40,.2); }
        .eventraz-btn:hover { filter: brightness(1.08); }
        #subProgramRow { overflow: hidden; max-height: 0; opacity: 0; transition: max-height .35s ease, opacity .3s ease; }
        #subProgramRow.visible { max-height: 120px; opacity: 1; }
        .page-section { display: none; }
        .page-section.active { display: block; }
        .modal-overlay { display: none; position: fixed; inset: 0; z-index: 50; background: rgba(0,0,0,.5); backdrop-filter: blur(4px); align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .pic-info-card { background: linear-gradient(135deg, #fff8e7, #fff5d6); border: 1px solid #ffc20e; border-radius: 16px; padding: 16px 20px; margin-top: 12px; }
        .guru-card { background: rgba(255,255,255,.7); border: 1px solid rgba(138,0,40,.12); border-radius: 16px; padding: 16px; position: relative; }
        .murid-card { background: rgba(255,255,255,.7); border: 1px solid rgba(138,0,40,.10); border-radius: 16px; padding: 16px; }
        .capacity-bar { height: 8px; border-radius: 99px; background: #e5e7eb; overflow: hidden; margin-top: 4px; }
        .capacity-fill { height: 100%; border-radius: 99px; transition: width .4s ease; }
        .filter-pill-saya { background: rgba(138,0,40,.06); color: #8a0028; border: 1px solid rgba(138,0,40,.15); cursor: pointer; }
        .filter-pill-saya:hover { background: rgba(138,0,40,.12); }
        .filter-pill-saya.active-filter { background: linear-gradient(135deg, var(--maroon), var(--maroon-dark)); color: #fff; border-color: transparent; box-shadow: 0 10px 20px rgba(138,0,40,.2); }
    </style>
    <?= view('partials/mobile_responsive', ['mobileLayout' => 'sidebar']) ?>
</head>
<body class="flex app-shell">

    <div class="sidebar app-sidebar p-6 flex flex-col justify-between text-white shadow-2xl z-10">
        <div>
            <div class="mb-8 border-b border-white/15 pb-4 text-center">
                <img src="<?= base_url('assets/eventraz-logo.jpeg') ?>" alt="EventraZ" class="brand-logo mx-auto mb-3">
                <h1 class="text-lg font-black text-white tracking-widest">EventraZ Portal</h1>
                <p class="text-[9px] text-yellow-200 font-bold mt-1">Sekolah Terengganu</p>
                <p class="text-[10px] text-white mt-2 font-semibold"><?= esc(session('school_name')) ?></p>
                <p class="text-[9px] text-yellow-100"><?= esc(session('school_code')) ?></p>
            </div>
            <nav class="space-y-3">
                <button onclick="tunjukSeksyen('daftar')" id="menuDaftar"
                    class="w-full text-left p-4 text-xs font-bold active-tab flex items-center gap-3 rounded-xl">
                    <i class="fa-solid fa-file-signature"></i> DAFTAR PROGRAM
                </button>
                <button onclick="tunjukSeksyen('saya')" id="menuSaya"
                    class="w-full text-left p-4 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-clipboard-list"></i> PENDAFTARAN SAYA
                </button>
                <button onclick="tunjukSeksyen('kehadiran')" id="menuKehadiran"
                    class="w-full text-left p-4 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-qrcode"></i> KEHADIRAN
                </button>
                <button onclick="window.location.href='<?= base_url('school/events') ?>'"
                    class="w-full text-left p-4 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-calendar-days"></i> ACARA
                </button>
            </nav>
        </div>
        <a href="<?= base_url('logout') ?>" class="text-xs text-yellow-100 font-bold p-3 flex items-center gap-2 hover:text-white hover:bg-white/10 rounded-xl transition-all">
            <i class="fa-solid fa-power-off"></i> LOG KELUAR PORTAL
        </a>
    </div>

    <div class="app-main ml-[280px] w-full p-10 flex justify-center items-start min-h-screen py-12">

        <!-- ── DAFTAR SECTION ── -->
        <div id="seksyenDaftar" class="page-section active w-full max-w-4xl">
            <div class="glass-card p-10 rounded-3xl">
                <h2 class="text-2xl font-bold text-[#520018] mb-2 uppercase tracking-tight">Borang Pendaftaran Sekolah</h2>
                <p class="text-xs text-slate-400 mb-8 border-b pb-4">Sila lengkapkan semua maklumat yang diperlukan.</p>

                <form id="regFormTRG" class="space-y-6" onsubmit="return false;">

                    <!-- PROGRAM SELECTION -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Program *</label>
                        <select name="programId" id="mainProgList"
                            class="program-select eventraz-field w-full p-4 border rounded-2xl outline-none text-sm transition-all"
                            onchange="onMainProgramChange()" required>
                            <option value="">-- Sila Pilih Program --</option>
                        </select>
                    </div>

                    <!-- Capacity display -->
                    <div id="capacityInfo" style="display:none" class="p-4 bg-blue-50 border border-blue-200 rounded-2xl">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-slate-600">Kapasiti Program</span>
                            <span id="capacityText" class="text-xs font-black text-[#8a0028]"></span>
                        </div>
                        <div class="capacity-bar">
                            <div id="capacityFill" class="capacity-fill bg-green-500" style="width:0%"></div>
                        </div>
                        <div id="capacityFullMsg" style="display:none" class="mt-2 text-xs text-red-600 font-bold">
                            <i class="fa-solid fa-circle-xmark mr-1"></i> Pendaftaran telah ditutup. Kapasiti program telah penuh.
                        </div>
                    </div>

                    <!-- PIC INFO -->
                    <div id="picInfoContainer" style="display:none" class="pic-info-card">
                        <div class="flex items-center gap-3 text-sm">
                            <i class="fa-solid fa-user-tie text-[#8a0028]"></i>
                            <span class="font-bold text-slate-600">PIC / Penganjur:</span>
                            <span id="displayPicNama" class="font-semibold text-[#520018]">-</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm mt-2">
                            <i class="fa-solid fa-phone text-[#8a0028]"></i>
                            <span class="font-bold text-slate-600">No. Telefon:</span>
                            <span id="displayPicTel" class="font-semibold text-[#520018]">-</span>
                        </div>
                    </div>

                    <!-- SUB PROGRAM -->
                    <div id="subProgramRow">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Sub Program</label>
                        <select name="subProgramId" id="subProgList"
                            class="program-select eventraz-field w-full p-4 border rounded-2xl outline-none text-sm transition-all"
                            onchange="onSubProgramChange()">
                            <option value="">-- Pilih Sub Program (jika ada) --</option>
                        </select>
                    </div>

                    <!-- SCHOOL INFO -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Nama Penuh Sekolah *</label>
                        <input type="text" name="namaSekolah" id="namaSekolahInput" placeholder="Nama Sekolah"
                            class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Kod Sekolah *</label>
                            <input type="text" name="kodSekolah" id="kodSekolahInput" placeholder="Contoh: TBA1001"
                                class="eventraz-field p-4 w-full border rounded-2xl text-sm outline-none transition-all" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Emel Rasmi Sekolah *</label>
                            <input type="email" name="emailSekolah" placeholder="sekolah@moe.edu.my"
                                class="eventraz-field p-4 w-full border rounded-2xl text-sm outline-none transition-all" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">No. Telefon Rasmi Sekolah *</label>
                        <input type="text" name="telSekolah" placeholder="Contoh: 09XXXXXXXX"
                            class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                    </div>

                    <!-- ── GURU PENGIRING ── -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase ml-1">Guru Pengiring *</label>
                                <p class="text-[10px] text-slate-400 ml-1 mt-0.5">1 Guru = Maks 10 murid, 2 Guru = 20 murid, dan seterusnya.</p>
                            </div>
                            <button type="button" onclick="tambahGuru()"
                                class="flex items-center gap-2 text-xs font-bold text-[#8a0028] border-2 border-[#8a0028] px-4 py-2 rounded-xl hover:bg-yellow-50 transition-all">
                                <i class="fa-solid fa-plus"></i> Tambah Guru Pengiring
                            </button>
                        </div>
                        <div id="boxGuru" class="space-y-3"></div>
                        <p id="guruKosongMsg" class="text-xs text-red-500 mt-2" style="display:none">Sekurang-kurangnya satu Guru Pengiring diperlukan.</p>
                    </div>

                    <!-- BILANGAN MURID + MURID ENTRIES -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase ml-1">Bilangan Murid Terlibat *</label>
                                <p id="bilMuridInfo" class="text-[10px] text-slate-400 ml-1 mt-0.5"></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <select id="bilMurid" onchange="janaBorangMurid()"
                                    class="program-select eventraz-field p-3 border rounded-2xl text-sm outline-none transition-all min-w-[110px]">
                                    <option value="">-- Bil. Murid --</option>
                                </select>
                            </div>
                        </div>
                        <div id="boxMurid" class="space-y-3"></div>
                        <p id="bilMuridError" class="text-[10px] text-red-500 mt-1 ml-1" style="display:none"></p>
                    </div>

                    <button type="button" id="btnHantar" onclick="hantarFormTRG()"
                        class="eventraz-btn w-full text-white font-bold py-4 rounded-2xl uppercase tracking-widest text-xs transition-all active:scale-[0.98]">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Sahkan &amp; Hantar Pendaftaran
                    </button>
                </form>
            </div>
        </div>

        <!-- ── PENDAFTARAN SAYA ── -->
        <div id="seksyenSaya" class="page-section w-full max-w-4xl">
            <div class="glass-card p-10 rounded-3xl">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-[#520018] uppercase tracking-tight">Pendaftaran Saya</h2>
                        <p class="text-xs text-slate-400 mt-1">Senarai program yang telah didaftarkan.</p>
                    </div>
                    <button onclick="muatPendaftaranSaya()" class="text-xs text-[#8a0028] font-bold hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-rotate-right"></i> Refresh
                    </button>
                </div>

                <!-- Closest upcoming event (always pinned, max 1) -->
                <div id="acaraTerdekatWrap" style="display:none" class="mb-7">
                    <p class="text-[10px] font-bold text-[#8a0028] uppercase tracking-widest mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-star"></i> Acara Paling Terdekat
                    </p>
                    <div id="acaraTerdekat"></div>
                </div>

                <!-- Filter pills -->
                <div class="flex gap-2 mb-5">
                    <button type="button" onclick="tukarFilterSaya('akan_datang')" id="filterAkanDatang"
                        class="filter-pill-saya active-filter text-[10px] font-bold uppercase px-4 py-2 rounded-full transition-all">
                        <i class="fa-solid fa-calendar-plus mr-1"></i> Akan Datang
                    </button>
                    <button type="button" onclick="tukarFilterSaya('lepas')" id="filterLepas"
                        class="filter-pill-saya text-[10px] font-bold uppercase px-4 py-2 rounded-full transition-all">
                        <i class="fa-solid fa-clock-rotate-left mr-1"></i> Lepas
                    </button>
                </div>

                <div id="senaraiPendaftaranSaya">
                    <p class="text-center text-slate-400 text-sm py-12"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuatkan...</p>
                </div>
            </div>
        </div>

        <!-- ── KEHADIRAN SECTION ── -->
        <div id="seksyenKehadiran" class="page-section w-full max-w-md mx-auto">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

            <div class="glass-card p-6">
                <h2 class="text-lg font-black mb-1 text-center" style="color:var(--maroon)">Tandakan Kehadiran</h2>
                <p class="text-xs text-gray-500 text-center mb-5">Imbas kod QR acara atau muat naik dari galeri anda.</p>

                <div class="flex bg-gray-100 rounded-full p-1 mb-5">
                    <button id="attTabScanBtn" onclick="attSwitchTab('scan')"
                        class="flex-1 py-2 rounded-full text-xs font-bold eventraz-btn text-[#ffc20e]">
                        <i class="fa-solid fa-camera mr-1"></i>Imbas QR
                    </button>
                    <button id="attTabUploadBtn" onclick="attSwitchTab('upload')"
                        class="flex-1 py-2 rounded-full text-xs font-bold text-gray-600">
                        <i class="fa-solid fa-upload mr-1"></i>Muat Naik QR
                    </button>
                </div>

                <div id="attScanPane">
                    <div id="att-qr-reader" class="rounded-2xl overflow-hidden shadow"></div>
                </div>

                <div id="attUploadPane" class="hidden text-center">
                    <label class="block border-2 border-dashed rounded-2xl p-8 cursor-pointer hover:bg-black/[.02] transition" style="border-color: rgba(138,0,40,.3)">
                        <i class="fa-solid fa-image text-3xl mb-2" style="color:var(--maroon)"></i>
                        <p class="text-xs text-gray-600">Ketik untuk pilih imej QR dari galeri</p>
                        <input type="file" id="attQrFileInput" accept="image/*" class="hidden">
                    </label>
                    <div id="attUploadPreview" class="mt-4"></div>
                </div>
            </div>
        </div>

    </div><!-- end main -->

    <!-- Guru Detail Modal -->
    <div id="modalGuru" class="modal-overlay">
        <div class="glass-card w-full max-w-lg mx-4 rounded-3xl p-8 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-[#520018]"><i class="fa-solid fa-chalkboard-user mr-2"></i> Guru Pengiring</h3>
                <button onclick="document.getElementById('modalGuru').classList.remove('open')" class="text-slate-400 hover:text-[#8a0028] text-2xl leading-none">&times;</button>
            </div>
            <div id="kandunganModalGuru"></div>
        </div>
    </div>

    <?= view('partials/footer_watermark') ?>

    <script>
        var guruCount = 0;
        var selectedProgramId = null;
        var selectedProgramData = null;

        // ── Page navigation ──
        var SEKSYEN_MAP = { daftar: ['seksyenDaftar', 'menuDaftar'], saya: ['seksyenSaya', 'menuSaya'], kehadiran: ['seksyenKehadiran', 'menuKehadiran'] };
        function tunjukSeksyen(seksyen) {
            if (seksyen === 'kehadiran') setTimeout(() => attSwitchTab('scan'), 100);
            if (seksyen !== 'kehadiran') attStopScanner();

            document.querySelectorAll('.page-section').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('nav button').forEach(btn => {
                btn.classList.remove('active-tab');
                btn.classList.add('text-yellow-100','hover:bg-white/10');
                btn.style.borderRadius = '';
            });
            var map     = SEKSYEN_MAP[seksyen] || SEKSYEN_MAP.daftar;
            var aktifId = map[0];
            var btnId   = map[1];
            document.getElementById(aktifId).classList.add('active');
            var btn = document.getElementById(btnId);
            if (btn) { btn.classList.add('active-tab'); btn.classList.remove('text-yellow-100','hover:bg-white/10'); }
            if (seksyen === 'saya') muatPendaftaranSaya();
        }

        // ── On load ──
        window.onload = async function () {
            await muatProgramUtama();
            tambahGuru(); // start with 1 guru
            kemasKiniMaxMurid(); // populate murid dropdown based on 1 guru

            // Pre-select program from URL ?program=ID (coming from Acara page)
            var urlParams = new URLSearchParams(window.location.search);
            var preProgram = urlParams.get('program');
            if (preProgram) {
                var drop = document.getElementById('mainProgList');
                drop.value = preProgram;
                if (drop.value) {
                    await onMainProgramChange();
                    document.getElementById('regFormTRG').scrollIntoView({ behavior: 'smooth' });
                }
            }

            // Pre-fill school info from session
            var namaInput = document.getElementById('namaSekolahInput');
            var kodInput  = document.getElementById('kodSekolahInput');
            if (namaInput) namaInput.value = '<?= esc(session('school_name')) ?>';
            if (kodInput)  kodInput.value  = '<?= esc(session('school_code')) ?>';
        };

        // ── Load programs ──
        async function muatProgramUtama() {
            try {
                const res  = await fetch('<?= base_url('school/programs') ?>?t=' + Date.now(), { cache: 'no-store' });
                const list = await res.json();
                var drop = document.getElementById('mainProgList');
                drop.innerHTML = '<option value="">-- Sila Pilih Program --</option>';
                if (Array.isArray(list)) {
                    list.forEach(function(p) {
                        var opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.nama + (p.is_full ? ' [PENUH]' : '');
                        opt.dataset.picNama  = p.pic_nama || '';
                        opt.dataset.picTel   = p.pic_tel || '';
                        opt.dataset.regLimit = p.registration_limit || 0;
                        opt.dataset.used     = p.used_capacity || 0;
                        opt.dataset.remaining = p.remaining_capacity !== null ? p.remaining_capacity : '';
                        opt.dataset.isFull   = p.is_full ? '1' : '0';
                        if (p.is_full) opt.style.color = '#9ca3af';
                        drop.appendChild(opt);
                    });
                }
            } catch (err) { console.error('muatProgramUtama:', err); }
        }

        async function onMainProgramChange() {
            var drop = document.getElementById('mainProgList');
            var selectedOpt = drop.options[drop.selectedIndex];
            selectedProgramId = drop.value || null;

            // Hide sub programs by default
            document.getElementById('subProgramRow').classList.remove('visible');
            document.getElementById('subProgList').innerHTML = '<option value="">-- Tiada sub program --</option>';

            // PIC info
            if (selectedProgramId && selectedOpt.dataset.picNama) {
                document.getElementById('displayPicNama').textContent = selectedOpt.dataset.picNama || '-';
                document.getElementById('displayPicTel').textContent  = selectedOpt.dataset.picTel  || '-';
                document.getElementById('picInfoContainer').style.display = '';
            } else {
                document.getElementById('picInfoContainer').style.display = 'none';
            }

            // Capacity
            if (selectedProgramId) {
                updateCapacityUI(selectedOpt.dataset.regLimit, selectedOpt.dataset.used, selectedOpt.dataset.remaining, selectedOpt.dataset.isFull === '1');
            } else {
                document.getElementById('capacityInfo').style.display = 'none';
            }

            // Load sub programs
            if (selectedProgramId) {
                try {
                    const res  = await fetch('<?= base_url('school/programs/sub') ?>/' + selectedProgramId + '?t=' + Date.now(), { cache: 'no-store' });
                    const subs = await res.json();
                    if (Array.isArray(subs) && subs.length) {
                        var subDrop = document.getElementById('subProgList');
                        subDrop.innerHTML = '<option value="">-- Pilih Sub Program (jika ada) --</option>';
                        subs.forEach(function(s) {
                            var opt = document.createElement('option');
                            opt.value = s.id;
                            opt.textContent = s.nama + (s.is_full ? ' [PENUH]' : '');
                            opt.dataset.regLimit  = s.registration_limit || 0;
                            opt.dataset.used      = s.used_capacity || 0;
                            opt.dataset.remaining = s.remaining_capacity !== null ? s.remaining_capacity : '';
                            opt.dataset.isFull    = s.is_full ? '1' : '0';
                            subDrop.appendChild(opt);
                        });
                        document.getElementById('subProgramRow').classList.add('visible');
                    }
                } catch(e) {}
            }
        }

        function onSubProgramChange() {
            var subDrop = document.getElementById('subProgList');
            var opt = subDrop.options[subDrop.selectedIndex];
            if (subDrop.value) {
                updateCapacityUI(opt.dataset.regLimit, opt.dataset.used, opt.dataset.remaining, opt.dataset.isFull === '1');
                selectedProgramId = subDrop.value;
            } else {
                // revert to main program capacity
                var mainDrop = document.getElementById('mainProgList');
                var mainOpt  = mainDrop.options[mainDrop.selectedIndex];
                selectedProgramId = mainDrop.value;
                updateCapacityUI(mainOpt.dataset.regLimit, mainOpt.dataset.used, mainOpt.dataset.remaining, mainOpt.dataset.isFull === '1');
            }
        }

        function updateCapacityUI(limit, used, remaining, isFull) {
            limit    = parseInt(limit || 0);
            used     = parseInt(used  || 0);
            var info = document.getElementById('capacityInfo');
            var fill = document.getElementById('capacityFill');
            var txt  = document.getElementById('capacityText');
            var msg  = document.getElementById('capacityFullMsg');
            var btn  = document.getElementById('btnHantar');

            if (limit <= 0) { info.style.display = 'none'; btn.disabled = false; btn.classList.remove('opacity-50','cursor-not-allowed'); return; }
            info.style.display = '';
            var pct = Math.min(100, Math.round((used / limit) * 100));
            fill.style.width = pct + '%';
            fill.className = 'capacity-fill ' + (pct >= 100 ? 'bg-red-500' : pct >= 75 ? 'bg-amber-400' : 'bg-green-500');
            txt.textContent = used + ' / ' + limit + ' (' + (remaining !== '' ? remaining + ' baki' : '—') + ')';
            msg.style.display  = isFull ? '' : 'none';
            btn.disabled = isFull;
            if (isFull) btn.classList.add('opacity-50','cursor-not-allowed');
            else btn.classList.remove('opacity-50','cursor-not-allowed');
        }

        // ── Guru Pengiring ──
        function tambahGuru() {
            var box = document.getElementById('boxGuru');
            var idx = guruCount;
            guruCount++;
            var div = document.createElement('div');
            div.className = 'guru-card';
            div.id = 'guruCard_' + idx;
            div.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-[#8a0028]"><i class="fa-solid fa-chalkboard-user mr-1"></i> Guru Pengiring ${box.children.length + 1}</span>
                    ${idx > 0 ? `<button type="button" onclick="buangGuru(${idx})" class="text-red-400 hover:text-red-600 text-sm"><i class="fa-solid fa-xmark"></i></button>` : ''}
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">Nama Guru *</label>
                        <input type="text" name="namaGuru_${idx}" id="namaGuru_${idx}" placeholder="Nama Guru Pengiring"
                            class="eventraz-field w-full p-3 border rounded-2xl text-sm outline-none" oninput="kemasKiniMaxMurid()">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 ml-1">No. IC Guru *</label>
                        <input type="text" name="icGuru_${idx}" id="icGuru_${idx}" placeholder="Tanpa sengkang"
                            class="eventraz-field w-full p-3 border rounded-2xl text-sm outline-none">
                    </div>
                </div>`;
            box.appendChild(div);
            renumberGuru();
            kemasKiniMaxMurid();
        }

        function buangGuru(idx) {
            var card = document.getElementById('guruCard_' + idx);
            if (card) card.remove();
            renumberGuru();
            kemasKiniMaxMurid();
        }

        function renumberGuru() {
            var cards = document.querySelectorAll('#boxGuru .guru-card');
            cards.forEach(function(card, i) {
                var label = card.querySelector('span');
                if (label) label.innerHTML = '<i class="fa-solid fa-chalkboard-user mr-1"></i> Guru Pengiring ' + (i + 1);
            });
        }

        function bilGuruAktif() {
            return document.querySelectorAll('#boxGuru .guru-card').length;
        }

        function kemasKiniMaxMurid() {
            var n = bilGuruAktif();
            var maxMurid = n * 10;
            var info = document.getElementById('bilMuridInfo');
            info.textContent = n + ' Guru Pengiring → Maksimum ' + maxMurid + ' murid';

            // Rebuild murid count dropdown
            var drop = document.getElementById('bilMurid');
            var prev = parseInt(drop.value || 0);
            drop.innerHTML = '<option value="">-- Bil. Murid --</option>';
            for (var i = 1; i <= maxMurid; i++) {
                var opt = document.createElement('option');
                opt.value = i;
                opt.textContent = i + ' murid';
                if (i === prev) opt.selected = true;
                drop.appendChild(opt);
            }

            // If previous selection exceeds new max, trim murid boxes
            if (prev > maxMurid) {
                drop.value = maxMurid;
            }
            janaBorangMurid();
        }

        function janaBorangMurid() {
            var bil = parseInt(document.getElementById('bilMurid').value || 0);
            var box = document.getElementById('boxMurid');
            var existing = box.querySelectorAll('.murid-card');

            // Add cards if needed
            for (var i = existing.length; i < bil; i++) {
                var div = document.createElement('div');
                div.className = 'murid-card p-4 bg-white/60 border border-white/80 rounded-2xl';
                div.innerHTML = `<p class="text-xs font-bold text-[#8a0028] mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Murid <span class="murid-num">${i + 1}</span></p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Nama Murid *</label>
                            <input type="text" name="namaMurid_${i}" placeholder="Nama penuh murid"
                                class="eventraz-field w-full p-3 border rounded-2xl text-sm outline-none" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">No. MyKid / IC *</label>
                            <input type="text" name="icMurid_${i}" placeholder="Tanpa sengkang"
                                class="eventraz-field w-full p-3 border rounded-2xl text-sm outline-none" required>
                        </div>
                    </div>`;
                box.appendChild(div);
            }

            // Remove excess cards
            var cards = box.querySelectorAll('.murid-card');
            for (var j = cards.length - 1; j >= bil; j--) {
                cards[j].remove();
            }

            // Renumber
            box.querySelectorAll('.murid-card').forEach(function(card, idx) {
                var num = card.querySelector('.murid-num');
                if (num) num.textContent = idx + 1;
                // Update name attributes to keep indexes sequential
                card.querySelectorAll('input').forEach(function(inp) {
                    inp.name = inp.name.replace(/_\d+$/, '_' + idx);
                });
            });
        }

        function sahkanBilMurid() {
            // kept for compatibility — logic now handled in kemasKiniMaxMurid
        }

        function kumpulDataGuru() {
            var cards = document.querySelectorAll('#boxGuru .guru-card');
            var guruList = [];
            var valid = true;
            cards.forEach(function(card, i) {
                var namaInput = card.querySelector('input[name^="namaGuru"]');
                var icInput   = card.querySelector('input[name^="icGuru"]');
                var nama = namaInput ? namaInput.value.trim() : '';
                var ic   = icInput   ? icInput.value.trim()   : '';
                if (!nama || !ic) { valid = false; }
                guruList.push({ nama, ic, idx: i });
            });
            return valid ? guruList : null;
        }

        async function hantarFormTRG() {
            var form = document.getElementById('regFormTRG');

            // Validate program
            var progId = document.getElementById('subProgList').value || document.getElementById('mainProgList').value;
            if (!progId) { Swal.fire({ icon: 'warning', title: 'Program belum dipilih', text: 'Sila pilih program dahulu.' }); return; }

            // Validate guru
            var guruList = kumpulDataGuru();
            if (!guruList || !guruList.length) {
                document.getElementById('guruKosongMsg').style.display = '';
                Swal.fire({ icon: 'warning', title: 'Guru Pengiring diperlukan', text: 'Sila tambah sekurang-kurangnya satu Guru Pengiring.' });
                return;
            }
            document.getElementById('guruKosongMsg').style.display = 'none';

            // Collect murid entries
            var muridCards = document.querySelectorAll('#boxMurid .murid-card');
            var muridList  = [];
            var muridValid = true;
            muridCards.forEach(function(card, i) {
                var namaInput = card.querySelector('input[name^="namaMurid"]');
                var icInput   = card.querySelector('input[name^="icMurid"]');
                var nama = namaInput ? namaInput.value.trim() : '';
                var ic   = icInput   ? icInput.value.trim()   : '';
                if (!nama || !ic) { muridValid = false; }
                muridList.push({ nama, ic });
            });

            var bilMurid = muridList.length;
            var maxMurid = guruList.length * 10;

            if (bilMurid < 1) { Swal.fire({ icon: 'warning', title: 'Tiada murid', text: 'Sila pilih bilangan murid dan isi maklumat mereka.' }); return; }
            if (!muridValid)  { Swal.fire({ icon: 'warning', title: 'Maklumat murid tidak lengkap', text: 'Sila lengkapkan nama dan No. MyKid/IC bagi setiap murid.' }); return; }
            if (bilMurid > maxMurid) {
                Swal.fire({ icon: 'warning', title: 'Melebihi had murid', text: guruList.length + ' Guru Pengiring membenarkan maksimum ' + maxMurid + ' murid.' }); return;
            }

            // Build form data
            var body = new FormData();
            body.append('programId', progId);
            body.append('namaSekolah', form.querySelector('[name="namaSekolah"]').value.trim());
            body.append('kodSekolah',  form.querySelector('[name="kodSekolah"]').value.trim());
            body.append('emailSekolah',form.querySelector('[name="emailSekolah"]').value.trim());
            body.append('telSekolah',  form.querySelector('[name="telSekolah"]').value.trim());
            body.append('bilMurid',    bilMurid);
            guruList.forEach(function(g, i) {
                body.append('namaGuru_' + i, g.nama);
                body.append('icGuru_' + i,   g.ic);
            });
            muridList.forEach(function(m, i) {
                body.append('namaMurid_' + i, m.nama);
                body.append('icMurid_' + i,   m.ic);
            });

            Swal.fire({ title: 'Menghantar pendaftaran...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const res    = await fetch('<?= base_url('school/daftar') ?>', { method: 'POST', body });
                const result = await res.json();
                Swal.close();
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Berjaya!', text: 'Pendaftaran berjaya dihantar.', confirmButtonColor: '#8a0028' });
                    form.reset();
                    guruCount = 0;
                    document.getElementById('boxGuru').innerHTML = '';
                    document.getElementById('boxMurid').innerHTML = '';
                    document.getElementById('bilMurid').innerHTML = '<option value="">-- Bil. Murid --</option>';
                    tambahGuru();
                    muatProgramUtama();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Pendaftaran tidak dapat dihantar.' });
                }
            } catch (err) {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Ralat', text: 'Masalah sambungan. Sila cuba lagi.' });
            }
        }

        // ── My Registrations ──
        function togglMuridList(id) {
            var box  = document.getElementById(id);
            var icon = document.getElementById(id + '_icon');
            if (!box) return;
            box.classList.toggle('hidden');
            if (icon) icon.style.transform = box.classList.contains('hidden') ? '' : 'rotate(180deg)';
        }

        var pendaftaranSayaCache = [];
        var currentFilterSaya    = 'akan_datang';

        async function muatPendaftaranSaya() {
            var container = document.getElementById('senaraiPendaftaranSaya');
            container.innerHTML = '<p class="text-center text-slate-400 text-sm py-12"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuatkan...</p>';
            try {
                const res    = await fetch('<?= base_url('school/my-registrations') ?>?t=' + Date.now(), { cache: 'no-store' });
                const result = await res.json();
                if (!result.success) {
                    container.innerHTML = '<p class="text-center text-red-400 text-sm py-12">Gagal memuatkan pendaftaran.</p>'; return;
                }
                pendaftaranSayaCache = result.data || [];
                renderPendaftaranSaya();
            } catch (err) {
                container.innerHTML = '<p class="text-center text-red-400 text-sm py-12">Gagal memuatkan pendaftaran.</p>';
            }
        }

        // Split registrations into upcoming (ongoing/future) and past, each sorted so the
        // most relevant item comes first (soonest upcoming, most recent past).
        function klasifikasiPendaftaranSaya(data) {
            var today = new Date().toISOString().slice(0, 10);
            var upcoming = [], past = [];
            data.forEach(function(reg) {
                if (!reg.end_date || reg.end_date >= today) upcoming.push(reg);
                else past.push(reg);
            });
            upcoming.sort(function(a, b) { return (a.start_date || '').localeCompare(b.start_date || ''); });
            past.sort(function(a, b) { return (b.start_date || '').localeCompare(a.start_date || ''); });
            return { upcoming: upcoming, past: past };
        }

        function tukarFilterSaya(filter) {
            currentFilterSaya = filter;
            document.getElementById('filterAkanDatang').classList.toggle('active-filter', filter === 'akan_datang');
            document.getElementById('filterLepas').classList.toggle('active-filter', filter === 'lepas');
            renderPendaftaranSaya();
        }

        function renderPendaftaranSaya() {
            var wrap      = document.getElementById('acaraTerdekatWrap');
            var terdekat  = document.getElementById('acaraTerdekat');
            var container = document.getElementById('senaraiPendaftaranSaya');

            if (!pendaftaranSayaCache.length) {
                wrap.style.display = 'none';
                container.innerHTML = '<p class="text-center text-slate-400 text-sm py-12">Tiada rekod pendaftaran.</p>';
                return;
            }

            var grup = klasifikasiPendaftaranSaya(pendaftaranSayaCache);
            var closest = grup.upcoming.length ? grup.upcoming[0] : null;
            var upcomingRest = grup.upcoming.slice(1);

            if (closest) {
                wrap.style.display = '';
                terdekat.innerHTML = buildRegCardSaya(closest, 'terdekat', true);
            } else {
                wrap.style.display = 'none';
            }

            var senarai = currentFilterSaya === 'lepas' ? grup.past : upcomingRest;
            if (!senarai.length) {
                container.innerHTML = '<p class="text-center text-slate-400 text-sm py-12">' +
                    (currentFilterSaya === 'lepas' ? 'Tiada acara lepas.' : 'Tiada acara akan datang lain buat masa ini.') + '</p>';
                return;
            }

            container.innerHTML = senarai.map(function(reg, idx) {
                return buildRegCardSaya(reg, currentFilterSaya + '_' + idx, false);
            }).join('');
        }

        function buildRegCardSaya(reg, idx, highlight) {
            var today = new Date().toISOString().slice(0, 10);
            var statusLabel = !reg.end_date ? 'Tidak diketahui' : (reg.end_date < today ? 'Selesai' : (reg.start_date <= today ? 'Sedang Berlangsung' : 'Akan Datang'));
            var statusCls   = reg.end_date < today ? 'bg-slate-100 text-slate-500' : (reg.start_date <= today ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700');
            var guruList  = Array.isArray(reg.guru)  ? reg.guru  : [];
            var muridList = Array.isArray(reg.murid) ? reg.murid : [];
            var guruHtml  = guruList.length  ? guruList.map(g  => `<span class="inline-block bg-yellow-50 border border-yellow-200 text-[#8a0028] text-[10px] font-bold px-2 py-0.5 rounded-lg mr-1 mb-1">${g.nama_guru}</span>`).join('') : '<span class="text-slate-400 text-xs">—</span>';
            var muridHtml = muridList.length ? muridList.map((m, i) => `<div class="flex justify-between gap-2 text-[10px] py-1.5 px-1 border-b border-white/60 last:border-0"><span class="text-slate-500 truncate">${i+1}. ${m.nama_murid}</span><span class="text-slate-400 whitespace-nowrap">${m.ic_murid}</span></div>`).join('') : '<span class="text-slate-400 text-xs">—</span>';
            var muridId = 'muridList_' + idx;
            var cardCls = highlight
                ? 'border-2 border-[#ffc20e] bg-gradient-to-br from-yellow-50 to-white shadow-lg rounded-2xl p-5 mb-4 relative'
                : 'border border-white/80 bg-white/60 rounded-2xl p-5 mb-4';
            return `<div class="${cardCls}">
                ${highlight ? '<span class="absolute -top-3 left-5 bg-[#ffc20e] text-[#520018] text-[9px] font-black uppercase px-3 py-1 rounded-full shadow"><i class="fa-solid fa-star mr-1"></i>Terdekat</span>' : ''}
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-black text-[#520018] text-sm">${reg.program_name}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">${reg.start_date || '—'} → ${reg.end_date || '—'}</p>
                    </div>
                    <span class="${statusCls} text-[10px] font-bold px-3 py-1 rounded-full whitespace-nowrap">${statusLabel}</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 gap-3 text-xs text-slate-600 mb-3">
                    <div><span class="text-slate-400">Sekolah</span><br><b class="break-words">${reg.nama_sekolah}</b></div>
                    <div><span class="text-slate-400">Kod</span><br><b>${reg.kod_sekolah}</b></div>
                    <div><span class="text-slate-400">Bil. Murid</span><br><b class="text-[#8a0028]">${reg.bil_murid}</b></div>
                    <div><span class="text-slate-400">Status</span><br><b>${reg.status}</b></div>
                </div>
                <div class="mb-3">
                    <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Guru Pengiring</p>
                    ${guruHtml}
                </div>
                ${muridList.length ? `<div class="mb-3">
                    <button type="button" onclick="togglMuridList('${muridId}')" class="w-full flex items-center justify-between text-[10px] font-bold text-slate-500 uppercase mb-1 bg-white/60 hover:bg-white/90 border border-white/80 rounded-xl px-3 py-2 transition-all">
                        <span>Senarai Murid (${muridList.length})</span>
                        <i id="${muridId}_icon" class="fa-solid fa-chevron-down text-[#8a0028] transition-transform"></i>
                    </button>
                    <div id="${muridId}" class="hidden bg-white/50 rounded-xl p-2 mt-1 max-h-48 overflow-y-auto">${muridHtml}</div>
                </div>` : ''}
                <div class="text-[10px] text-slate-400">PIC: ${reg.pic_nama || '—'} | ${reg.pic_tel || '—'}</div>
            </div>`;
        }

        // ── Kehadiran (QR attendance) ──
        let attHtml5QrCode;
        let attScanning = false;

        function attSwitchTab(tab) {
            const scanBtn = document.getElementById('attTabScanBtn');
            const uploadBtn = document.getElementById('attTabUploadBtn');
            const scanPane = document.getElementById('attScanPane');
            const uploadPane = document.getElementById('attUploadPane');
            if (!scanBtn) return; // section not on this page load yet

            if (tab === 'scan') {
                scanBtn.classList.add('eventraz-btn', 'text-[#ffc20e]'); scanBtn.classList.remove('text-gray-600');
                uploadBtn.classList.remove('eventraz-btn', 'text-[#ffc20e]'); uploadBtn.classList.add('text-gray-600');
                scanPane.classList.remove('hidden'); uploadPane.classList.add('hidden');
                attStartScanner();
            } else {
                uploadBtn.classList.add('eventraz-btn', 'text-[#ffc20e]'); uploadBtn.classList.remove('text-gray-600');
                scanBtn.classList.remove('eventraz-btn', 'text-[#ffc20e]'); scanBtn.classList.add('text-gray-600');
                uploadPane.classList.remove('hidden'); scanPane.classList.add('hidden');
                attStopScanner();
            }
        }

        function attStartScanner() {
            if (attScanning) return;
            attHtml5QrCode = new Html5Qrcode('att-qr-reader');
            Html5Qrcode.getCameras().then(cameras => {
                if (!cameras || !cameras.length) {
                    document.getElementById('att-qr-reader').innerHTML = '<p class="text-red-600 text-xs p-4">Tiada kamera dijumpai.</p>';
                    return;
                }
                const camId = cameras.find(c => /back|rear|environment/i.test(c.label))?.id || cameras[0].id;
                attHtml5QrCode.start(camId, { fps: 10, qrbox: 230 }, attOnScanSuccess, () => {}).then(() => attScanning = true);
            }).catch(() => {
                document.getElementById('att-qr-reader').innerHTML = '<p class="text-red-600 text-xs p-4">Akses kamera ditolak.</p>';
            });
        }
        function attStopScanner() {
            if (attHtml5QrCode && attScanning) attHtml5QrCode.stop().then(() => attScanning = false).catch(() => {});
        }
        function attOnScanSuccess(decodedText) {
            attStopScanner();
            attSubmitQrText(decodedText);
        }

        // Downloaded/screenshotted QR images often have a transparent background or no
        // white "quiet zone" margin around the code — both trip up the zxing decoder
        // that html5-qrcode uses. Flattening onto a white canvas with padding (and
        // upscaling tiny images) fixes the vast majority of these upload failures.
        function attPreprocessQrImage(file) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                const url = URL.createObjectURL(file);
                img.onload = () => {
                    URL.revokeObjectURL(url);
                    const PAD = 40;
                    const MIN_SIZE = 400;
                    const scale = Math.max(1, MIN_SIZE / Math.max(img.naturalWidth, img.naturalHeight));
                    const w = Math.round(img.naturalWidth * scale);
                    const h = Math.round(img.naturalHeight * scale);
                    const canvas = document.createElement('canvas');
                    canvas.width = w + PAD * 2;
                    canvas.height = h + PAD * 2;
                    const ctx = canvas.getContext('2d');
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(img, PAD, PAD, w, h);
                    canvas.toBlob(blob => {
                        if (!blob) { reject(new Error('canvas_blob_failed')); return; }
                        resolve(new File([blob], 'qr-processed.png', { type: 'image/png' }));
                    }, 'image/png');
                };
                img.onerror = () => { URL.revokeObjectURL(url); reject(new Error('image_load_failed')); };
                img.src = url;
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('attQrFileInput');
            if (!input) return;
            input.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (!file) return;
                document.getElementById('attUploadPreview').innerHTML = '<p class="text-xs text-gray-500 mt-2"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Membaca kod QR...</p>';
                const tempScanner = new Html5Qrcode('attUploadPreview');
                attPreprocessQrImage(file)
                    .then(processedFile => tempScanner.scanFile(processedFile, true))
                    .catch(() => tempScanner.scanFile(file, true)) // fall back to the raw file if preprocessing (or the processed decode) fails
                    .then(decodedText => { document.getElementById('attUploadPreview').innerHTML = ''; attSubmitQrText(decodedText); })
                    .catch(() => { document.getElementById('attUploadPreview').innerHTML = '<p class="text-xs text-red-600 mt-2">Kod QR tidak dikesan dalam imej ini.</p>'; });
            });
        });

function attSubmitQrText(qrText) {
    fetch('<?= base_url('attendance/process-scan') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'qr_text=' + encodeURIComponent(qrText)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success === true) {
            Swal.fire({
                icon: 'success',
                title: 'Kehadiran Berjaya',
                text: res.session?.session_name || 'Kehadiran anda telah direkodkan.',
                confirmButtonColor: '#8a0028'
            });
        } else if (res.status === 'duplicate') {
            Swal.fire({
                icon: 'info',
                title: 'Sudah Direkodkan',
                text: res.message || 'Kehadiran anda telah direkodkan sebelum ini.',
                confirmButtonColor: '#8a0028'
            });
        } else if (res.status === 'expired') {
            Swal.fire({
                icon: 'error',
                title: 'Sesi Tamat Tempoh',
                text: res.message || 'Sesi kehadiran ini telah tamat.',
                confirmButtonColor: '#8a0028'
            });
        } else if (res.status === 'not_registered') {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Berdaftar',
                text: res.message || 'Anda tidak berdaftar untuk program ini. Sila daftar terlebih dahulu.',
                confirmButtonColor: '#8a0028'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Tidak Sah / Tamat Tempoh',
                text: res.message || 'Kod QR tidak sah atau sesi telah tamat.',
                confirmButtonColor: '#8a0028'
            });
        }
    })
    .catch(function() {
        Swal.fire({
            icon: 'error',
            title: 'Ralat',
            text: 'Sila cuba lagi.',
            confirmButtonColor: '#8a0028'
        });
    });
}
    </script>
</body>
</html>