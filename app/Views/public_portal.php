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
        :root { --maroon: #8a0028; --maroon-dark: #520018; --gold: #ffc20e; }
        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at 10% 10%, rgba(255,194,14,.22), transparent 28%),
                radial-gradient(circle at 88% 15%, rgba(138,0,40,.16), transparent 30%),
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
                <p class="text-[9px] text-yellow-200 font-bold mt-1">Orang Awam</p>
                <p class="text-[10px] text-white mt-2 font-semibold"><?= esc(session('name')) ?></p>
                <p class="text-[9px] text-yellow-100"><?= esc(session('email')) ?></p>
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
                <button onclick="window.location.href='<?= base_url('awam/events') ?>'"
                    class="w-full text-left p-4 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-calendar-days"></i> ACARA
                </button>
            </nav>
        </div>
        <a href="<?= base_url('logout') ?>" class="text-xs text-yellow-100 font-bold p-3 flex items-center gap-2 hover:text-white hover:bg-white/10 rounded-xl transition-all">
            <i class="fa-solid fa-power-off"></i> LOG KELUAR
        </a>
    </div>

    <div class="app-main ml-[280px] w-full p-10 flex justify-center items-start min-h-screen py-12">

        <!-- ── DAFTAR SECTION ── -->
        <div id="seksyenDaftar" class="page-section active w-full max-w-4xl">
            <div class="glass-card p-10 rounded-3xl">
                <h2 class="text-2xl font-bold text-[#520018] mb-2 uppercase tracking-tight">Borang Pendaftaran Awam</h2>
                <p class="text-xs text-slate-400 mb-8 border-b pb-4">Sila lengkapkan semua maklumat yang diperlukan.</p>

                <form id="regFormAwam" class="space-y-6" onsubmit="return false;">

                    <!-- PROGRAM -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Program *</label>
                        <select name="programId" id="mainProgList"
                            class="program-select eventraz-field w-full p-4 border rounded-2xl outline-none text-sm transition-all"
                            onchange="onMainProgramChange()" required>
                            <option value="">-- Sila Pilih Program --</option>
                        </select>
                    </div>

                    <!-- Capacity -->
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
                    <div id="picInfoContainer" style="display:none" class="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-2xl p-4">
                        <div class="flex items-center gap-3 text-sm">
                            <i class="fa-solid fa-user-tie text-[#8a0028]"></i>
                            <span class="font-bold text-slate-600">PIC:</span>
                            <span id="displayPicNama" class="font-semibold text-[#520018]">-</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm mt-2">
                            <i class="fa-solid fa-phone text-[#8a0028]"></i>
                            <span class="font-bold text-slate-600">No. Tel:</span>
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

                    <!-- PERSONAL INFO -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Nama Penuh *</label>
                        <input type="text" name="namaPenuh" placeholder="Nama Penuh"
                            class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">No. Kad Pengenalan *</label>
                            <input type="text" name="noIC" placeholder="Tanpa sengkang"
                                class="eventraz-field p-4 w-full border rounded-2xl text-sm outline-none transition-all" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">No. Telefon *</label>
                            <input type="text" name="telAwam" placeholder="01XXXXXXXX"
                                class="eventraz-field p-4 w-full border rounded-2xl text-sm outline-none transition-all" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Emel *</label>
                        <input type="email" name="email" value="<?= esc(session('email')) ?>"
                            placeholder="Emel Anda" class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                    </div>

                    <!-- FAMILY MEMBERS -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Bilangan Ahli Keluarga (0 jika tiada)</label>
                        <input type="number" name="bilAhli" id="bilAhli" min="0" max="20" value="0"
                            oninput="janaBorangAhli()" placeholder="0"
                            class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all">
                        <p class="text-[10px] text-slate-400 mt-1 ml-1">Pendaftar (1) + Ahli Keluarga = Jumlah slot yang digunakan</p>
                    </div>
                    <div id="boxAhli" class="space-y-3"></div>

                    <!-- Total participants display -->
                    <div id="jumlahPeserta" style="display:none" class="p-4 bg-yellow-50 border border-yellow-200 rounded-2xl text-sm">
                        <i class="fa-solid fa-users text-[#8a0028] mr-2"></i>
                        Jumlah Peserta: <span id="txtJumlah" class="font-black text-[#8a0028]">1</span>
                        (Pendaftar + Ahli Keluarga)
                    </div>

                    <button type="button" id="btnHantar" onclick="hantarFormAwam()"
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

    </div>

    <?= view('partials/footer_watermark') ?>

    <script>
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

        window.onload = async function () {
            await muatProgramUtama();

            // Pre-select program from URL ?program=ID (from Acara page)
            var urlParams = new URLSearchParams(window.location.search);
            var preProgram = urlParams.get('program');
            if (preProgram) {
                var drop = document.getElementById('mainProgList');
                drop.value = preProgram;
                if (drop.value) {
                    await onMainProgramChange();
                    document.getElementById('regFormAwam').scrollIntoView({ behavior: 'smooth' });
                }
            }
        };

        async function muatProgramUtama() {
            try {
                const res  = await fetch('<?= base_url('awam/programs') ?>?t=' + Date.now(), { cache: 'no-store' });
                const list = await res.json();
                var drop = document.getElementById('mainProgList');
                drop.innerHTML = '<option value="">-- Sila Pilih Program --</option>';
                if (Array.isArray(list)) {
                    list.forEach(function(p) {
                        var opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.nama + (p.is_full ? ' [PENUH]' : '');
                        opt.dataset.picNama   = p.pic_nama || '';
                        opt.dataset.picTel    = p.pic_tel || '';
                        opt.dataset.regLimit  = p.registration_limit || 0;
                        opt.dataset.used      = p.used_capacity || 0;
                        opt.dataset.remaining = p.remaining_capacity !== null ? p.remaining_capacity : '';
                        opt.dataset.isFull    = p.is_full ? '1' : '0';
                        if (p.is_full) opt.style.color = '#9ca3af';
                        drop.appendChild(opt);
                    });
                }
            } catch (err) { console.error('muatProgramUtama:', err); }
        }

        async function onMainProgramChange() {
            var drop = document.getElementById('mainProgList');
            var opt  = drop.options[drop.selectedIndex];

            document.getElementById('subProgramRow').classList.remove('visible');
            document.getElementById('subProgList').innerHTML = '<option value="">-- Tiada sub program --</option>';

            if (drop.value && opt.dataset.picNama) {
                document.getElementById('displayPicNama').textContent = opt.dataset.picNama || '-';
                document.getElementById('displayPicTel').textContent  = opt.dataset.picTel  || '-';
                document.getElementById('picInfoContainer').style.display = '';
            } else {
                document.getElementById('picInfoContainer').style.display = 'none';
            }

            if (drop.value) {
                updateCapacityUI(opt.dataset.regLimit, opt.dataset.used, opt.dataset.remaining, opt.dataset.isFull === '1');
                try {
                    const res  = await fetch('<?= base_url('awam/programs/sub') ?>/' + drop.value + '?t=' + Date.now(), { cache: 'no-store' });
                    const subs = await res.json();
                    if (Array.isArray(subs) && subs.length) {
                        var subDrop = document.getElementById('subProgList');
                        subDrop.innerHTML = '<option value="">-- Pilih Sub Program (jika ada) --</option>';
                        subs.forEach(function(s) {
                            var o = document.createElement('option');
                            o.value = s.id; o.textContent = s.nama + (s.is_full ? ' [PENUH]' : '');
                            o.dataset.regLimit  = s.registration_limit || 0;
                            o.dataset.used      = s.used_capacity || 0;
                            o.dataset.remaining = s.remaining_capacity !== null ? s.remaining_capacity : '';
                            o.dataset.isFull    = s.is_full ? '1' : '0';
                            subDrop.appendChild(o);
                        });
                        document.getElementById('subProgramRow').classList.add('visible');
                    }
                } catch(e) {}
            } else {
                document.getElementById('capacityInfo').style.display = 'none';
            }
        }

        function onSubProgramChange() {
            var sub = document.getElementById('subProgList');
            var opt = sub.options[sub.selectedIndex];
            if (sub.value) {
                updateCapacityUI(opt.dataset.regLimit, opt.dataset.used, opt.dataset.remaining, opt.dataset.isFull === '1');
            } else {
                var main = document.getElementById('mainProgList');
                var mainOpt = main.options[main.selectedIndex];
                updateCapacityUI(mainOpt.dataset.regLimit, mainOpt.dataset.used, mainOpt.dataset.remaining, mainOpt.dataset.isFull === '1');
            }
        }

        function updateCapacityUI(limit, used, remaining, isFull) {
            limit = parseInt(limit || 0); used = parseInt(used || 0);
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
            msg.style.display = isFull ? '' : 'none';
            btn.disabled = isFull;
            if (isFull) btn.classList.add('opacity-50','cursor-not-allowed');
            else btn.classList.remove('opacity-50','cursor-not-allowed');
        }

        function janaBorangAhli() {
            var bil = parseInt(document.getElementById('bilAhli').value || 0);
            bil = Math.max(0, Math.min(20, bil));
            var box = document.getElementById('boxAhli');
            box.innerHTML = '';
            for (var i = 0; i < bil; i++) {
                var div = document.createElement('div');
                div.className = 'p-4 bg-white/60 border border-white/80 rounded-2xl';
                div.innerHTML = `<p class="text-xs font-bold text-slate-500 uppercase mb-3">Ahli Keluarga ${i+1}</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Nama</label>
                            <input type="text" name="namaAhli_${i}" placeholder="Nama penuh"
                                class="eventraz-field w-full p-3 border rounded-2xl text-sm outline-none" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">No. IC</label>
                            <input type="text" name="icAhli_${i}" placeholder="Tanpa sengkang"
                                class="eventraz-field w-full p-3 border rounded-2xl text-sm outline-none" required>
                        </div>
                    </div>`;
                box.appendChild(div);
            }
            var jumlah = 1 + bil;
            document.getElementById('jumlahPeserta').style.display = bil > 0 ? '' : 'none';
            document.getElementById('txtJumlah').textContent = jumlah;
        }

        async function hantarFormAwam() {
            var form = document.getElementById('regFormAwam');
            var progId = document.getElementById('subProgList').value || document.getElementById('mainProgList').value;
            if (!progId) { Swal.fire({ icon: 'warning', title: 'Program belum dipilih' }); return; }

            var body = new FormData(form);
            body.set('programId', progId);

            Swal.fire({ title: 'Menghantar pendaftaran...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            try {
                const res    = await fetch('<?= base_url('awam/daftar') ?>', { method: 'POST', body });
                const result = await res.json();
                Swal.close();
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Berjaya!', text: 'Pendaftaran berjaya dihantar.', confirmButtonColor: '#8a0028' });
                    form.reset();
                    document.getElementById('bilAhli').value = 0;
                    janaBorangAhli();
                    muatProgramUtama();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Pendaftaran tidak dapat dihantar.' });
                }
            } catch (err) {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Ralat', text: 'Masalah sambungan. Sila cuba lagi.' });
            }
        }

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
                const res    = await fetch('<?= base_url('awam/my-registrations') ?>?t=' + Date.now(), { cache: 'no-store' });
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
            var statusLabel = !reg.end_date ? 'Tidak diketahui' : (reg.end_date < today ? 'Selesai' : 'Akan Datang / Aktif');
            var statusCls   = reg.end_date < today ? 'bg-slate-100 text-slate-500' : 'bg-blue-100 text-blue-700';
            var ahli = Array.isArray(reg.ahli) ? reg.ahli : [];
            var jumlah = 1 + parseInt(reg.bil_ahli || 0);
            var ahliId = 'ahliList_' + idx;
            var ahliHtml = ahli.map((a, i) => `<div class="flex justify-between gap-2 text-[10px] py-1.5 px-1 border-b border-white/60 last:border-0"><span class="text-slate-500 truncate">${i+1}. ${a.nama_ahli}</span>${a.ic_ahli ? `<span class="text-slate-400 whitespace-nowrap">${a.ic_ahli}</span>` : ''}</div>`).join('');
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
                <div class="grid grid-cols-2 gap-3 text-xs text-slate-600 mb-3">
                    <div><span class="text-slate-400">Nama</span><br><b class="break-words">${reg.nama}</b></div>
                    <div><span class="text-slate-400">No. IC</span><br><b>${reg.ic}</b></div>
                    <div><span class="text-slate-400">Emel</span><br><b class="break-words">${reg.email}</b></div>
                    <div><span class="text-slate-400">Jumlah Peserta</span><br><b class="text-[#8a0028]">${jumlah} orang</b></div>
                </div>
                ${ahli.length ? `<div class="mb-3">
                    <button type="button" onclick="togglMuridList('${ahliId}')" class="w-full flex items-center justify-between text-[10px] font-bold text-slate-500 uppercase mb-1 bg-white/60 hover:bg-white/90 border border-white/80 rounded-xl px-3 py-2 transition-all">
                        <span>Ahli Keluarga (${ahli.length})</span>
                        <i id="${ahliId}_icon" class="fa-solid fa-chevron-down text-[#8a0028] transition-transform"></i>
                    </button>
                    <div id="${ahliId}" class="hidden bg-white/50 rounded-xl p-2 mt-1 max-h-48 overflow-y-auto">${ahliHtml}</div>
                </div>` : ''}
                <div class="text-[10px] text-slate-400 mt-2">PIC: ${reg.pic_nama || '—'} | ${reg.pic_tel || '—'}</div>
            </div>`;
        }
    </script>
</body>
</html>