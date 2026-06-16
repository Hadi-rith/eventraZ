<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventraZ - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root { --maroon: #8a0028; --maroon-dark: #520018; --gold: #ffc20e; --ink: #231f20; }
        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at 18% 8%, rgba(255, 194, 14, .22), transparent 28%),
                radial-gradient(circle at 88% 16%, rgba(138, 0, 40, .18), transparent 26%),
                linear-gradient(135deg, #fffaf0 0%, #f7eef2 46%, #fff8df 100%);
            color: var(--ink);
        }
        .sidebar {
            background: linear-gradient(160deg, rgba(82, 0, 24, .92), rgba(138, 0, 40, .82));
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,.25);
            box-shadow: 24px 0 60px rgba(82, 0, 24, .22);
            backdrop-filter: blur(24px) saturate(160%);
            -webkit-backdrop-filter: blur(24px) saturate(160%);
        }
        .brand-logo {
            width: 178px;
            background: #fff;
            border-radius: 28px;
            padding: 6px;
            filter: drop-shadow(0 14px 20px rgba(82, 0, 24, .16));
        }
        .glass-card {
            background: rgba(255, 255, 255, .58) !important;
            border: 1px solid rgba(255, 255, 255, .82) !important;
            box-shadow: 0 24px 58px rgba(82, 0, 24, .12), inset 0 1px 0 rgba(255,255,255,.9) !important;
            backdrop-filter: blur(26px) saturate(160%);
            -webkit-backdrop-filter: blur(26px) saturate(160%);
        }
        .active-nav { background: rgba(255, 194, 14, .98) !important; color: #520018 !important; box-shadow: 0 16px 34px rgba(255,194,14,.24); }
        .eventraz-btn { background: linear-gradient(135deg, var(--maroon), var(--maroon-dark)) !important; }
        .eventraz-btn:hover { filter: brightness(1.08); }
        .eventraz-field { background: rgba(255,255,255,.58) !important; border-color: rgba(138,0,40,.15) !important; }
        .eventraz-field:focus { box-shadow: 0 0 0 3px rgba(255,194,14,.28); }
        select, select option { background: #fff !important; color: #111827 !important; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        @media (max-width: 900px) {
            body.flex { display: block; }
            .sidebar { position: relative; width: 100%; height: auto; }
            .ml-\[280px\] { margin-left: 0 !important; }
            .grid-cols-3 { grid-template-columns: repeat(1, minmax(0, 1fr)) !important; }
        }
    </style>
</head>
<body class="flex">

    <!-- Sidebar -->
    <div class="sidebar p-6 flex flex-col justify-between text-white shadow-2xl z-10">
        <div>
            <div class="mb-8 border-b border-white/15 pb-5 text-center">
                <img src="<?= base_url('assets/eventraz-logo.jpeg') ?>" alt="EventraZ" class="brand-logo mx-auto mb-3">
                <h1 class="text-xl font-black text-white tracking-wider">EventraZ Admin</h1>
                <p class="text-[9px] text-yellow-200 uppercase font-bold mt-1">Event Tracking, Registration & Engagement Zone</p>
            </div>

            <!-- Program Filter -->
            <div class="mb-6">
                <label class="block text-[10px] font-bold text-yellow-100 uppercase mb-2 ml-1 tracking-wider">Tapis Program</label>
                <select id="filterProgram" onchange="tapisSemuaData()"
                    class="w-full p-3 bg-white border border-white/70 rounded-xl text-xs text-slate-900 outline-none focus:ring-2 focus:ring-yellow-300">
                    <option value="SEMUA">-- SEMUA PROGRAM --</option>
                </select>
            </div>

            <!-- Nav Tabs -->
            <nav class="space-y-2">
                <button onclick="tukarTab('daftar', this)"
                    class="nav-btn w-full text-left p-3.5 text-xs font-bold active-nav flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-calendar-plus"></i> DAFTAR PROGRAM
                </button>
                <div class="border-t border-white/15 my-2"></div>
                <button onclick="tukarTab('trg', this)"
                    class="nav-btn w-full text-left p-3.5 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-layer-group"></i> SEKOLAH TRG
                </button>
                <button onclick="tukarTab('luar', this)"
                    class="nav-btn w-full text-left p-3.5 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-school-flag"></i> SEKOLAH LUAR
                </button>
                <button onclick="tukarTab('awam', this)"
                    class="nav-btn w-full text-left p-3.5 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-user-group"></i> ORANG AWAM
                </button>
                <div class="border-t border-white/15 my-2"></div>
                <button onclick="tukarTab('akaun', this)"
                    class="nav-btn w-full text-left p-3.5 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-users-gear"></i> AKAUN PENGGUNA
                </button>
            </nav>
        </div>

        <a href="<?= base_url('logout') ?>"
            class="text-xs text-yellow-100 font-bold p-3 flex items-center gap-2 hover:bg-white/10 rounded-xl transition-all mt-6">
            <i class="fa-solid fa-power-off"></i> LOG KELUAR ADMIN
        </a>
    </div>

    <!-- Main -->
    <div class="ml-[280px] w-full p-8 min-h-screen">

        <!-- Daftar Program Header (only shown on daftar tab) -->
        <div id="daftar-header" class="glass-card flex justify-between items-center mb-8 p-6 rounded-2xl">
            <div>
                <h2 class="text-2xl font-black text-[#520018] uppercase tracking-tight">Daftar Program</h2>
                <p class="text-xs text-slate-400 mt-1">Tambah program baharu ke dalam sistem</p>
            </div>
            <span class="bg-yellow-100/80 text-[#8a0028] text-xs font-bold px-4 py-2 rounded-xl uppercase flex items-center gap-2">
                <i class="fa-solid fa-calendar-plus"></i> Program Management
            </span>
        </div>

        <!-- Header -->
        <div id="data-header" style="display:none" class="glass-card flex justify-between items-center mb-8 p-6 rounded-2xl">
            <div>
                <h2 class="text-2xl font-black text-[#520018] uppercase tracking-tight">Paparan Data Utama</h2>
                <p class="text-xs text-slate-400 mt-1">Memaparkan rekod pendaftaran secara langsung (Live)</p>
            </div>
            <button onclick="muatDataLive()"
                class="eventraz-btn text-white text-xs font-bold px-5 py-3 rounded-xl flex items-center gap-2 shadow-md transition-all active:scale-95">
                <i class="fa-solid fa-rotate"></i> REFRESH DATA
            </button>
        </div>

        <!-- Stat Cards -->
        <div id="stat-cards" style="display:none" class="grid grid-cols-3 gap-6 mb-8">
            <div class="glass-card p-6 rounded-2xl border-l-4 border-[#8a0028]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sekolah Terengganu</p>
                <h3 id="statTRG" class="text-3xl font-black text-slate-800 mt-1">—</h3>
            </div>
            <div class="glass-card p-6 rounded-2xl border-l-4 border-[#ffc20e]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sekolah Luar (Non-TRG)</p>
                <h3 id="statLuar" class="text-3xl font-black text-slate-800 mt-1">—</h3>
            </div>
            <div class="glass-card p-6 rounded-2xl border-l-4 border-[#520018]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Orang Awam</p>
                <h3 id="statAwam" class="text-3xl font-black text-slate-800 mt-1">—</h3>
            </div>
        </div>

        <!-- Tables -->
        <div id="data-tables" style="display:none" class="glass-card rounded-2xl overflow-hidden">

            <!-- TRG Tab -->
            <div id="tab-trg" class="tab-content">
                <div class="p-5 border-b font-bold text-sm text-slate-700 bg-slate-50 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-[#8a0028]"></i> Senarai Sekolah Terengganu
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b">
                            <tr>
                                <th class="p-4">Tarikh</th>
                                <th class="p-4">Program</th>
                                <th class="p-4">Nama Sekolah</th>
                                <th class="p-4">Kod</th>
                                <th class="p-4">Guru Pengiring</th>
                                <th class="p-4">No. Tel</th>
                                <th class="p-4 text-center">Bil. Murid</th>
                            </tr>
                        </thead>
                        <tbody id="tableTRG" class="divide-y text-slate-600">
                            <tr><td colspan="7" class="p-8 text-center text-slate-400 italic">Memuatkan data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Luar Tab -->
            <div id="tab-luar" class="tab-content">
                <div class="p-5 border-b font-bold text-sm text-slate-700 bg-slate-50 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-school-flag text-amber-500"></i> Senarai Sekolah Luar (Kategori Awam)
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b">
                            <tr>
                                <th class="p-4">Tarikh</th>
                                <th class="p-4">Program</th>
                                <th class="p-4">Nama Sekolah Luar</th>
                                <th class="p-4">Kod Sekolah</th>
                                <th class="p-4">No. Tel</th>
                                <th class="p-4">Emel</th>
                            </tr>
                        </thead>
                        <tbody id="tableLuar" class="divide-y text-slate-600">
                            <tr><td colspan="6" class="p-8 text-center text-slate-400 italic">Klik tab untuk memuatkan data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Awam Tab -->
            <div id="tab-awam" class="tab-content">
                <div class="p-5 border-b font-bold text-sm text-slate-700 bg-slate-50 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-user-group text-[#8a0028]"></i> Senarai Pendaftaran Orang Awam
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b">
                            <tr>
                                <th class="p-4">Tarikh</th>
                                <th class="p-4">Program</th>
                                <th class="p-4">Nama Pemohon</th>
                                <th class="p-4">No. IC</th>
                                <th class="p-4">No. Tel</th>
                                <th class="p-4">Emel</th>
                            </tr>
                        </thead>
                        <tbody id="tableAwam" class="divide-y text-slate-600">
                            <tr><td colspan="6" class="p-8 text-center text-slate-400 italic">Klik tab untuk memuatkan data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Daftar Program Tab -->
        <div id="tab-daftar" class="tab-content active">

            <!-- Form Card -->
            <div class="glass-card p-8 rounded-2xl mb-8">
                <div class="flex items-center justify-between gap-4 mb-8">
                    <div>
                        <h3 id="programFormTitle" class="text-lg font-black text-[#520018] uppercase tracking-wider flex items-center gap-3">
                            <i class="fa-solid fa-calendar-plus text-[#8a0028]"></i> Daftar Program Baharu
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">Status program dikira automatik berdasarkan tarikh mula dan tarikh tamat.</p>
                    </div>
                    <span class="bg-yellow-100/80 text-[#8a0028] text-[10px] font-bold px-3 py-1 rounded-full uppercase">Database: programs</span>
                </div>

                <form id="programForm" onsubmit="daftarProgram(event)" class="grid grid-cols-12 gap-5 items-end" data-mode="create" data-original-code="">
                    <div class="col-span-12 md:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Kod Program *</label>
                        <input type="text" id="programCode" name="program_code" maxlength="30" placeholder="CONTOH: EVZ2026"
                            class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none uppercase">
                    </div>
                    <div class="col-span-12 md:col-span-4">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Nama Program *</label>
                        <input type="text" id="programName" name="program_name" placeholder="Masukkan nama program"
                            class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                    </div>
                    <div class="col-span-12 md:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Tarikh Mula *</label>
                        <input type="date" id="startDate" name="start_date"
                            class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                    </div>
                    <div class="col-span-12 md:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Tarikh Tamat *</label>
                        <input type="date" id="endDate" name="end_date"
                            class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                    </div>
                    <div class="col-span-12 md:col-span-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Status</label>
                        <div id="programStatusPreview"
                            class="w-full p-3 border border-slate-200 rounded-xl text-[10px] text-center font-black bg-slate-50 text-slate-500 uppercase">
                            AUTO
                        </div>
                    </div>
                    <div class="col-span-12 flex flex-wrap gap-3">
                        <button type="submit" id="btnDaftarProgram"
                            class="eventraz-btn text-white text-sm font-bold px-8 py-3 rounded-xl flex items-center justify-center gap-2 shadow-md transition-all active:scale-95">
                            <i class="fa-solid fa-floppy-disk"></i> SIMPAN PROGRAM
                        </button>
                        <button type="button" id="btnBatalEditProgram" onclick="resetProgramForm()" style="display:none"
                            class="bg-slate-200 text-slate-700 text-sm font-bold px-6 py-3 rounded-xl flex items-center justify-center gap-2 shadow-md transition-all active:scale-95">
                            <i class="fa-solid fa-xmark"></i> BATAL EDIT
                        </button>
                    </div>
                </form>
            </div>

            <!-- Program List Table -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="p-5 border-b bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2 font-bold text-sm text-slate-700 uppercase tracking-wider">
                        <i class="fa-solid fa-list-check text-[#8a0028]"></i> Senarai Semua Program
                    </div>
                    <span id="programCount" class="bg-[#8a0028]/10 text-[#8a0028] text-[10px] font-bold px-3 py-1 rounded-full">Memuatkan...</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b">
                            <tr>
                                <th class="p-4">#</th>
                                <th class="p-4">Kod Program</th>
                                <th class="p-4">Nama Program</th>
                                <th class="p-4">Tarikh Mula</th>
                                <th class="p-4">Tarikh Tamat</th>
                                <th class="p-4">Tempoh (Hari)</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-center">Hari Berbaki / Lepas</th>
                                <th class="p-4 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody id="tableProgramSenarai" class="divide-y text-slate-600">
                            <tr><td colspan="9" class="p-8 text-center text-slate-400 italic">Memuatkan senarai program...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div id="tab-akaun" class="tab-content">
            <div class="glass-card flex justify-between items-center mb-8 p-6 rounded-2xl">
                <div>
                    <h2 class="text-2xl font-black text-[#520018] uppercase tracking-tight">Akaun Pengguna</h2>
                    <p class="text-xs text-slate-400 mt-1">Kemaskini atau padam akaun sekolah dan orang awam.</p>
                </div>
                <button onclick="muatAkaun()"
                    class="eventraz-btn text-white text-xs font-bold px-5 py-3 rounded-xl flex items-center gap-2 shadow-md transition-all active:scale-95">
                    <i class="fa-solid fa-rotate"></i> REFRESH AKAUN
                </button>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="glass-card rounded-2xl overflow-hidden">
                    <div class="p-5 border-b bg-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-2 font-bold text-sm text-slate-700 uppercase tracking-wider">
                            <i class="fa-solid fa-school text-[#8a0028]"></i> Akaun Sekolah
                        </div>
                        <span id="schoolAccountCount" class="bg-[#8a0028]/10 text-[#8a0028] text-[10px] font-bold px-3 py-1 rounded-full">0 akaun</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b">
                                <tr>
                                    <th class="p-4">Kod</th>
                                    <th class="p-4">Nama Sekolah</th>
                                    <th class="p-4">Emel</th>
                                    <th class="p-4 text-center">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody id="tableSchoolAccounts" class="divide-y text-slate-600">
                                <tr><td colspan="4" class="p-8 text-center text-slate-400 italic">Memuatkan akaun...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="glass-card rounded-2xl overflow-hidden">
                    <div class="p-5 border-b bg-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-2 font-bold text-sm text-slate-700 uppercase tracking-wider">
                            <i class="fa-solid fa-user-group text-[#8a0028]"></i> Akaun Awam
                        </div>
                        <span id="publicAccountCount" class="bg-[#8a0028]/10 text-[#8a0028] text-[10px] font-bold px-3 py-1 rounded-full">0 akaun</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b">
                                <tr>
                                    <th class="p-4">Nama</th>
                                    <th class="p-4">Emel</th>
                                    <th class="p-4 text-center">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody id="tablePublicAccounts" class="divide-y text-slate-600">
                                <tr><td colspan="3" class="p-8 text-center text-slate-400 italic">Memuatkan akaun...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        var masterData = { sekolahTRG: [], sekolahLuar: [], orangAwam: [] };
        var programCache = [];
        var accountCache = { school: [], public: [] };

        window.onload = function () {
            tetapkanTarikhMinimum();
            bukaTabPermulaan();
            muatSenaraiProgram();

            if (getTabAktif() === 'daftar') {
                muatDataLive(false);
            } else {
                muatDataLive(true);
            }
        };

        function getTodayDate() {
            return new Date().toISOString().slice(0, 10);
        }

        function tetapkanTarikhMinimum() {
            var startInput = document.getElementById('startDate');
            var endInput = document.getElementById('endDate');
            var today = getTodayDate();

            startInput.min = today;
            endInput.min = today;
            startInput.addEventListener('change', kemasKiniStatusPreview);
            endInput.addEventListener('change', kemasKiniStatusPreview);
        }

        function kiraStatusProgram(startDate, endDate) {
            var today = getTodayDate();

            if (endDate && endDate < today) {
                return 'TIDAK AKTIF';
            }

            return 'AKTIF';
        }

        function kemasKiniStatusPreview() {
            var startDate = document.getElementById('startDate').value;
            var endDate = document.getElementById('endDate').value;
            var preview = document.getElementById('programStatusPreview');

            if (!startDate || !endDate) {
                preview.textContent = 'AUTO';
                preview.className = 'w-full p-3 border border-slate-200 rounded-xl text-[10px] text-center font-black bg-slate-50 text-slate-500 uppercase';
                return;
            }

            if (endDate < startDate) {
                preview.textContent = 'RALAT';
                preview.className = 'w-full p-3 border border-red-200 rounded-xl text-[10px] text-center font-black bg-red-50 text-red-600 uppercase';
                return;
            }

            var status = kiraStatusProgram(startDate, endDate);
            preview.textContent = status;
            preview.className = status === 'AKTIF'
                ? 'w-full p-3 border border-yellow-200 rounded-xl text-[10px] text-center font-black bg-yellow-50 text-[#8a0028] uppercase'
                : 'w-full p-3 border border-slate-200 rounded-xl text-[10px] text-center font-black bg-slate-100 text-slate-600 uppercase';
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (char) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[char];
            });
        }

        function escapeJs(value) {
            return String(value ?? '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        }

        async function muatSenaraiProgram() {
            const res = await fetch('<?= base_url('admin/programs') ?>?t=' + Date.now(), { cache: 'no-store' });
            const list = await res.json();
            programCache = list;
            var drop = document.getElementById('filterProgram');
            var selected = drop.value || 'SEMUA';

            drop.innerHTML = '<option value="SEMUA">-- SEMUA PROGRAM --</option>';
            list.forEach(p => {
                var status = String(p.status || '').toUpperCase();
                if (status && status !== 'AKTIF') return;

                var option = document.createElement('option');
                option.value = p.nama;
                option.textContent = p.nama;
                drop.appendChild(option);
            });

            if ([...drop.options].some(option => option.value === selected)) {
                drop.value = selected;
            }

            binaSenaraProgram(list);
        }

        function formatTarikh(dateStr) {
            if (!dateStr) return '—';
            var d = new Date(dateStr + 'T00:00:00');
            return d.toLocaleDateString('ms-MY', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        function kiraTempoh(start, end) {
            if (!start || !end) return '—';
            var ms = new Date(end + 'T00:00:00') - new Date(start + 'T00:00:00');
            var days = Math.round(ms / 86400000) + 1;
            return days > 0 ? days + ' hari' : '—';
        }

        function kiraHariBerbaki(end) {
            if (!end) return { label: '—', cls: 'text-slate-400' };
            var today = new Date(); today.setHours(0,0,0,0);
            var endDate = new Date(end + 'T00:00:00');
            var diff = Math.round((endDate - today) / 86400000);
            if (diff < 0)  return { label: Math.abs(diff) + ' hari lepas', cls: 'text-slate-400' };
            if (diff === 0) return { label: 'Hari ini tamat', cls: 'text-amber-600 font-bold' };
            return { label: diff + ' hari lagi', cls: 'text-green-600 font-bold' };
        }

        function binaSenaraProgram(list) {
            var tbody = document.getElementById('tableProgramSenarai');
            var countEl = document.getElementById('programCount');
            if (!tbody) return;

            // Sort oldest start_date first (ascending)
            var sorted = [...list].sort((a, b) => {
                var da = a.start_date || a.mula || '';
                var db = b.start_date || b.mula || '';
                return da.localeCompare(db);
            });

            countEl.textContent = sorted.length + ' program';

            if (sorted.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="p-8 text-center text-slate-400 italic">Tiada program didaftarkan lagi.</td></tr>';
                return;
            }

            var today = new Date(); today.setHours(0,0,0,0);

            tbody.innerHTML = '';
            sorted.forEach(function(p, i) {
                // Support both possible key names from API
                var kod   = p.kod   || p.code || p.program_code || '—';
                var nama  = p.nama  || p.name || p.program_name || '—';
                var mula  = p.mula  || p.start_date || '';
                var tamat = p.tamat || p.end_date   || '';
                var status = String(p.status || '').toUpperCase();

                var endDate = tamat ? new Date(tamat + 'T00:00:00') : null;
                var isAktif = status ? status === 'AKTIF' : endDate && endDate >= today;

                var statusHtml = isAktif
                    ? '<span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full font-bold text-[10px] uppercase">Aktif</span>'
                    : '<span class="bg-slate-100 text-slate-500 px-2.5 py-1 rounded-full font-bold text-[10px] uppercase">Tamat</span>';

                var berbaki = kiraHariBerbaki(tamat);
                var rowNum  = i + 1;

                tbody.innerHTML += `<tr class="hover:bg-slate-50 transition-all">
                    <td class="p-4 text-slate-400 font-medium">${rowNum}</td>
                    <td class="p-4"><span class="bg-yellow-50 text-[#8a0028] px-2 py-1 rounded-md font-bold text-[10px] uppercase tracking-wider">${escapeHtml(kod)}</span></td>
                    <td class="p-4 font-semibold text-slate-800">${escapeHtml(nama)}</td>
                    <td class="p-4 text-slate-600 whitespace-nowrap">${formatTarikh(mula)}</td>
                    <td class="p-4 text-slate-600 whitespace-nowrap">${formatTarikh(tamat)}</td>
                    <td class="p-4 text-slate-500">${kiraTempoh(mula, tamat)}</td>
                    <td class="p-4 text-center">${statusHtml}</td>
                    <td class="p-4 text-center text-xs ${berbaki.cls}">${berbaki.label}</td>
                    <td class="p-4">
                        <div class="flex justify-center gap-2">
                            <button type="button" onclick="mulaEditProgram('${escapeJs(kod)}')"
                                class="bg-yellow-100 text-[#8a0028] w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-yellow-200 transition-all"
                                title="Edit program">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button type="button" onclick="padamProgram('${escapeJs(kod)}', '${escapeJs(nama)}')"
                                class="bg-red-100 text-red-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-red-200 transition-all"
                                title="Padam program">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
        }

        async function daftarProgram(event) {
            event.preventDefault();

            var form = document.getElementById('programForm');
            var codeInput = document.getElementById('programCode');
            var nameInput = document.getElementById('programName');
            var startInput = document.getElementById('startDate');
            var endInput = document.getElementById('endDate');
            var btn = document.getElementById('btnDaftarProgram');

            var programCode = codeInput.value.trim().toUpperCase();
            var programName = nameInput.value.trim();
            var startDate = startInput.value;
            var endDate = endInput.value;

            if (!programCode || !programName || !startDate || !endDate) {
                Swal.fire({ icon: 'warning', title: 'Maklumat belum lengkap', text: 'Kod program, nama program, tarikh mula dan tarikh tamat diperlukan.' });
                return;
            }

            if (endDate < startDate) {
                Swal.fire({ icon: 'warning', title: 'Tarikh tidak sah', text: 'Tarikh tamat mesti sama atau selepas tarikh mula.' });
                return;
            }

            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-not-allowed');

            try {
                var isEdit = form.dataset.mode === 'edit';
                var originalCode = form.dataset.originalCode || programCode;
                var url = isEdit
                    ? '<?= base_url('admin/programs/update') ?>/' + encodeURIComponent(originalCode)
                    : '<?= base_url('admin/programs') ?>';

                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        program_code: programCode,
                        program_name: programName,
                        start_date: startDate,
                        end_date: endDate
                    })
                });
                const result = await res.json();

                if (result.success) {
                    resetProgramForm();
                    await muatSenaraiProgram();
                    Swal.fire({ icon: 'success', title: 'Berjaya', text: result.message, timer: 1600, showConfirmButton: false });
                    // Scroll to program list so admin can see the newly added entry
                    document.getElementById('tableProgramSenarai').closest('.glass-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Program tidak dapat disimpan.' });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Ralat Sambungan', text: 'Gagal menghantar data program ke pelayan.' });
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        }

        function mulaEditProgram(programCode) {
            var program = programCache.find(function(item) {
                return (item.kod || item.id || item.program_code) === programCode;
            });

            if (!program) {
                Swal.fire({ icon: 'error', title: 'Program tidak ditemui', text: 'Sila muat semula senarai program.' });
                return;
            }

            var form = document.getElementById('programForm');
            form.dataset.mode = 'edit';
            form.dataset.originalCode = programCode;

            document.getElementById('programCode').value = program.kod || program.id || program.program_code || '';
            document.getElementById('programName').value = program.nama || program.name || program.program_name || '';
            document.getElementById('startDate').value = program.mula || program.start_date || '';
            document.getElementById('endDate').value = program.tamat || program.end_date || '';
            document.getElementById('programFormTitle').innerHTML = '<i class="fa-solid fa-pen-to-square text-[#8a0028]"></i> Edit Program';
            document.getElementById('btnDaftarProgram').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> KEMASKINI PROGRAM';
            document.getElementById('btnBatalEditProgram').style.display = '';
            kemasKiniStatusPreview();
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function resetProgramForm() {
            var form = document.getElementById('programForm');
            form.reset();
            form.dataset.mode = 'create';
            form.dataset.originalCode = '';
            document.getElementById('programFormTitle').innerHTML = '<i class="fa-solid fa-calendar-plus text-[#8a0028]"></i> Daftar Program Baharu';
            document.getElementById('btnDaftarProgram').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> SIMPAN PROGRAM';
            document.getElementById('btnBatalEditProgram').style.display = 'none';
            kemasKiniStatusPreview();
        }

        async function padamProgram(programCode, programName) {
            var confirm = await Swal.fire({
                icon: 'warning',
                title: 'Padam program?',
                text: 'Program "' + programName + '" akan dipadam jika tiada rekod pendaftaran.',
                showCancelButton: true,
                confirmButtonText: 'Ya, padam',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626'
            });

            if (!confirm.isConfirmed) return;

            try {
                const res = await fetch('<?= base_url('admin/programs/delete') ?>/' + encodeURIComponent(programCode), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                const result = await res.json();

                if (result.success) {
                    resetProgramForm();
                    await muatSenaraiProgram();
                    Swal.fire({ icon: 'success', title: 'Berjaya', text: result.message, timer: 1600, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Program tidak dapat dipadam.' });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Ralat Sambungan', text: 'Gagal memadam program.' });
            }
        }

        async function muatDataLive(showLoading = true) {
            if (showLoading) {
                Swal.fire({ title: 'Mengambil Data Live...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            }

            try {
                const res    = await fetch('<?= base_url('admin/data') ?>');
                const result = await res.json();
                if (showLoading) {
                    Swal.close();
                }

                if (result.success) {
                    masterData = result;
                    document.getElementById('statTRG').innerText  = result.sekolahTRG.length;
                    document.getElementById('statLuar').innerText = result.sekolahLuar.length;
                    document.getElementById('statAwam').innerText = result.orangAwam.length;
                    tapisSemuaData();
                } else {
                    Swal.fire({ icon: 'error', title: 'Ralat', text: result.message });
                }
            } catch (err) {
                if (showLoading) {
                    Swal.close();
                    Swal.fire({ icon: 'error', title: 'Ralat Sambungan', text: 'Gagal mendapatkan data dari pengkalan data.' });
                }
            }
        }

        function tapisSemuaData() {
            var prog = document.getElementById('filterProgram').value;

            var trgF  = masterData.sekolahTRG.filter(d => prog === 'SEMUA' || d.program === prog);
            var luarF = masterData.sekolahLuar.filter(d => prog === 'SEMUA' || d.program === prog);
            var awamF = masterData.orangAwam.filter(d => prog === 'SEMUA' || d.program === prog);

            binaJadual('tableTRG',  trgF,  'TRG');
            binaJadual('tableLuar', luarF, 'LUAR');
            binaJadual('tableAwam', awamF, 'AWAM');
        }

        function binaJadual(tableId, data, jenis) {
            var tbody = document.getElementById(tableId);
            var cols  = jenis === 'TRG' ? 7 : 6;

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${cols}" class="p-8 text-center text-slate-400 italic">Tiada rekod pendaftaran ditemui.</td></tr>`;
                return;
            }

            tbody.innerHTML = '';
            data.forEach(d => {
                var html = `<tr class="hover:bg-slate-50 transition-all">
                    <td class="p-4 font-medium text-slate-500 whitespace-nowrap">${escapeHtml(d.timestamp)}</td>
                    <td class="p-4"><span class="bg-yellow-50 text-[#8a0028] px-2 py-1 rounded-md font-semibold text-[10px] whitespace-nowrap">${escapeHtml(d.program)}</span></td>`;

                if (jenis === 'TRG') {
                    html += `
                        <td class="p-4 font-bold text-slate-700">${escapeHtml(d.namaSekolah)}</td>
                        <td class="p-4 uppercase text-slate-500">${escapeHtml(d.kodSekolah)}</td>
                        <td class="p-4">${escapeHtml(d.namaGuru)}</td>
                        <td class="p-4">${escapeHtml(d.telGuru)}</td>
                        <td class="p-4 text-center">
                            <button type="button" onclick="paparSenaraiMurid('${escapeJs(d.id)}', '${escapeJs(d.namaSekolah)}')"
                                class="font-bold text-[#8a0028] underline decoration-yellow-400 decoration-2 underline-offset-4 hover:text-[#520018] transition-all">
                                ${escapeHtml(d.bilMurid)} orang
                            </button>
                        </td>`;
                } else if (jenis === 'LUAR') {
                    html += `
                        <td class="p-4 font-bold text-amber-700">${escapeHtml(d.namaSekolah)}</td>
                        <td class="p-4 uppercase text-slate-500">${escapeHtml(d.kodSekolah)}</td>
                        <td class="p-4">${escapeHtml(d.tel)}</td>
                        <td class="p-4">${escapeHtml(d.email)}</td>`;
                } else if (jenis === 'AWAM') {
                    html += `
                        <td class="p-4 font-bold text-[#8a0028]">${escapeHtml(d.nama)}</td>
                        <td class="p-4 text-slate-500">${escapeHtml(d.ic)}</td>
                        <td class="p-4">${escapeHtml(d.tel)}</td>
                        <td class="p-4">${escapeHtml(d.email)}</td>`;
                }

                html += `</tr>`;
                tbody.innerHTML += html;
            });
        }

        async function paparSenaraiMurid(registrationId, schoolName) {
            if (!registrationId) {
                Swal.fire({ icon: 'error', title: 'Rekod tidak lengkap', text: 'ID pendaftaran tidak ditemui.' });
                return;
            }

            Swal.fire({ title: 'Memuatkan senarai murid...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const res = await fetch('<?= base_url('admin/registration-students') ?>/' + encodeURIComponent(registrationId), { cache: 'no-store' });
                const result = await res.json();

                if (!result.success) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Senarai murid tidak dapat dimuatkan.' });
                    return;
                }

                var rows = result.students.length
                    ? result.students.map(function(student, index) {
                        return `<tr>
                            <td style="padding:10px;border-bottom:1px solid #e5e7eb;color:#64748b;font-weight:700;">${index + 1}</td>
                            <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:left;font-weight:700;color:#334155;">${escapeHtml(student.nama)}</td>
                            <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:left;color:#475569;">${escapeHtml(student.ic)}</td>
                        </tr>`;
                    }).join('')
                    : '<tr><td colspan="3" style="padding:18px;color:#94a3b8;text-align:center;">Tiada rekod murid ditemui.</td></tr>';

                Swal.fire({
                    title: 'Senarai Murid',
                    html: `<div style="text-align:left;margin-bottom:12px;color:#64748b;font-size:12px;">
                            <strong style="color:#520018;">${escapeHtml(result.school || schoolName)}</strong><br>
                            ${escapeHtml(result.program || '')}
                        </div>
                        <div style="max-height:360px;overflow:auto;border:1px solid #e5e7eb;border-radius:12px;">
                            <table style="width:100%;border-collapse:collapse;font-size:12px;">
                                <thead style="background:#f8fafc;color:#475569;text-transform:uppercase;">
                                    <tr>
                                        <th style="padding:10px;text-align:left;width:48px;">#</th>
                                        <th style="padding:10px;text-align:left;">Nama Murid</th>
                                        <th style="padding:10px;text-align:left;">No. IC</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>`,
                    width: 720,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#8a0028'
                });
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Ralat Sambungan', text: 'Gagal memuatkan senarai murid.' });
            }
        }

        function tukarTab(tabId, btn) {
            if (!document.getElementById('tab-' + tabId)) {
                tabId = 'daftar';
            }

            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById('tab-' + tabId).classList.add('active');

            if (!btn) {
                btn = document.querySelector('.nav-btn[onclick*="' + tabId + '"]');
            }

            document.querySelectorAll('.nav-btn').forEach(b => {
                b.classList.remove('active-nav');
                b.classList.add('text-yellow-100', 'hover:bg-white/10');
            });
            if (btn) {
                btn.classList.add('active-nav');
                btn.classList.remove('text-yellow-100', 'hover:bg-white/10');
            }

            // Show/hide data-only elements when on Daftar Program tab
            var isDaftar = tabId === 'daftar';
            document.getElementById('data-header').style.display    = isDaftar ? 'none' : '';
            document.getElementById('stat-cards').style.display     = isDaftar ? 'none' : '';
            document.getElementById('data-tables').style.display    = isDaftar ? 'none' : '';
            document.getElementById('daftar-header').style.display  = isDaftar ? '' : 'none';

            localStorage.setItem('adminDashboardTab', tabId);
            var url = new URL(window.location.href);
            url.searchParams.set('tab', tabId);
            history.replaceState(null, '', url.toString());
        }

        function getTabAktif() {
            var active = document.querySelector('.tab-content.active');
            return active ? active.id.replace('tab-', '') : 'daftar';
        }

        function bukaTabPermulaan() {
            var params = new URLSearchParams(window.location.search);
            var tab = params.get('tab') || localStorage.getItem('adminDashboardTab') || 'daftar';
            var validTabs = ['daftar', 'trg', 'luar', 'awam'];

            if (!validTabs.includes(tab)) {
                tab = 'daftar';
            }

            tukarTab(tab);
        }
    </script>
</body>
</html>
