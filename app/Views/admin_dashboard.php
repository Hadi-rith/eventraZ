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
        .event-card {
            background: rgba(255, 255, 255, .7);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, .82);
            transition: all 0.3s ease;
            overflow: hidden;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .event-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(82, 0, 24, 0.12);
        }
        .event-poster {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f0f0f0;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-upcoming { background: #dbeafe; color: #1e40af; }
        .status-ongoing { background: #d1fae5; color: #065f46; }
        .status-past { background: #f3f4f6; color: #4b5563; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        @media (max-width: 900px) {
            body.flex { display: block; }
            .sidebar { position: relative; width: 100%; height: auto; }
            .ml-\[280px\] { margin-left: 0 !important; }
            .grid-cols-3 { grid-template-columns: repeat(1, minmax(0, 1fr)) !important; }
            .event-grid { grid-template-columns: repeat(1, minmax(0, 1fr)) !important; }
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

        <!-- Data Header -->
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

        <!-- Data Tables -->
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
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 id="programFormTitle" class="text-lg font-black text-[#520018] uppercase tracking-wider flex items-center gap-3">
                            <i class="fa-solid fa-calendar-plus text-[#8a0028]"></i> Daftar Program Baharu
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">Status program dikira automatik berdasarkan tarikh mula dan tarikh tamat.</p>
                    </div>
                    <span class="bg-yellow-100/80 text-[#8a0028] text-[10px] font-bold px-3 py-1 rounded-full uppercase">Database: programs</span>
                </div>

                <!-- Program Type Toggle -->
                <div id="programTypeToggle" class="flex gap-3 mb-6">
                    <button type="button" id="btnTypeUtama" onclick="setProgramType('utama')"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-[#8a0028] bg-[#8a0028] text-white shadow transition-all">
                        <i class="fa-solid fa-star"></i> Program Utama
                    </button>
                    <button type="button" id="btnTypeSub" onclick="setProgramType('sub')"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-[#8a0028] text-[#8a0028] bg-white hover:bg-yellow-50 transition-all">
                        <i class="fa-solid fa-sitemap"></i> Sub Program
                    </button>
                </div>

                <!-- Parent Program selector (sub only) -->
                <div id="parentProgramRow" class="hidden mb-5 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <label class="block text-[10px] font-bold text-[#8a0028] uppercase mb-2 ml-1 tracking-wider">
                        <i class="fa-solid fa-sitemap mr-1"></i> Program Induk *
                    </label>
                    <select id="parentProgramSelect" name="parent_code"
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                        <option value="">-- Pilih Program Induk --</option>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1.5 ml-1">Hanya program utama (tanpa induk) boleh dipilih.</p>
                </div>

                <form id="programForm" onsubmit="daftarProgram(event)" class="grid grid-cols-12 gap-5 items-end" data-mode="create" data-original-code="" data-type="utama">
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
                    <div class="col-span-12 md:col-span-5">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider"><i class="fa-solid fa-user-tie mr-1 text-[#8a0028]"></i> Nama Penganjur / PIC</label>
                        <input type="text" id="picNama" name="pic_nama" placeholder="Nama penuh penganjur / PIC"
                            class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                    </div>
                    <div class="col-span-12 md:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider"><i class="fa-solid fa-phone mr-1 text-[#8a0028]"></i> No. Tel Penganjur / PIC</label>
                        <input type="text" id="picTel" name="pic_tel" placeholder="Contoh: 013XXXXXXX"
                            class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                    </div>
                    <div class="col-span-12 md:col-span-4">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider"><i class="fa-solid fa-location-dot mr-1 text-[#8a0028]"></i> Lokasi Program</label>
                        <input type="text" id="programLocation" name="location" placeholder="Contoh: Dewan Utama, KL"
                            class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                    </div>
                    <div class="col-span-12">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider"><i class="fa-solid fa-image mr-1 text-[#8a0028]"></i> Poster Program</label>
                        <div class="flex items-start gap-4">
                            <div class="flex-1">
                                <label for="programPoster"
                                    class="flex items-center gap-3 w-full p-3 border-2 border-dashed border-[#8a0028]/30 rounded-xl cursor-pointer hover:border-[#8a0028]/60 hover:bg-yellow-50/40 transition-all eventraz-field">
                                    <i class="fa-solid fa-cloud-arrow-up text-[#8a0028] text-xl"></i>
                                    <div>
                                        <p class="text-xs font-bold text-slate-600">Klik untuk pilih gambar poster</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">JPG, PNG, WEBP — maks 2MB</p>
                                    </div>
                                </label>
                                <input type="file" id="programPoster" name="poster_image" accept="image/*" class="hidden" onchange="pratonton_poster(this)">
                            </div>
                            <div id="posterPreviewBox" class="hidden w-24 h-24 rounded-xl overflow-hidden border border-slate-200 flex-shrink-0">
                                <img id="posterPreviewImg" src="" alt="Poster" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <p id="posterFileName" class="text-[10px] text-slate-400 mt-1.5 ml-1 hidden"></p>
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
                                <th class="p-4">Jenis</th>
                                <th class="p-4">Kod Program</th>
                                <th class="p-4">Nama Program</th>
                                <th class="p-4">Penganjur / PIC</th>
                                <th class="p-4">No. Tel PIC</th>
                                <th class="p-4">Tarikh Mula</th>
                                <th class="p-4">Tarikh Tamat</th>
                                <th class="p-4">Tempoh (Hari)</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-center">Hari Berbaki / Lepas</th>
                                <th class="p-4 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody id="tableProgramSenarai" class="divide-y text-slate-600">
                            <tr><td colspan="12" class="p-8 text-center text-slate-400 italic">Memuatkan senarai program...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Events Management Section - Poster & Info Editor -->
            <div class="mt-12 pt-8 border-t-2 border-dashed border-yellow-300">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-black text-[#520018] uppercase tracking-wider flex items-center gap-3">
                            <i class="fa-solid fa-calendar-days text-[#8a0028]"></i> Acara & Poster Program
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">Tambah acara baharu atau kemaskini poster, lokasi dan status featured</p>
                    </div>
                    <button onclick="bukaBorangAcara()"
                        class="eventraz-btn text-white text-xs font-bold px-5 py-3 rounded-xl flex items-center gap-2 shadow-md transition-all active:scale-95">
                        <i class="fa-solid fa-plus"></i> TAMBAH ACARA
                    </button>
                </div>

                <!-- Program Cards Grid -->
                <div id="eventGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <p class="col-span-3 text-center text-slate-400 text-sm py-12">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuatkan program...
                    </p>
                </div>
            </div>

        </div>

        <!-- Akaun Tab -->
        <div id="tab-akaun" class="tab-content">
            <div class="glass-card flex justify-between items-center mb-8 p-6 rounded-2xl">
                <div>
                    <h2 class="text-2xl font-black text-[#520018] uppercase tracking-tight">Akaun Pengguna</h2>
                    <p class="text-xs text-slate-400 mt-1">Tambah, kemaskini, padam dan reset kata laluan akaun.</p>
                </div>
                <div class="flex flex-wrap justify-end gap-2">
                    <button onclick="bukaBorangAkaun('school')"
                        class="bg-white border-2 border-[#8a0028] text-[#8a0028] text-xs font-bold px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm transition-all active:scale-95 hover:bg-yellow-50">
                        <i class="fa-solid fa-school"></i> TAMBAH SEKOLAH
                    </button>
                    <button onclick="bukaBorangAkaun('public')"
                        class="bg-white border-2 border-[#8a0028] text-[#8a0028] text-xs font-bold px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm transition-all active:scale-95 hover:bg-yellow-50">
                        <i class="fa-solid fa-user-plus"></i> TAMBAH AWAM
                    </button>
                    <button onclick="muatAkaun(true)"
                        class="eventraz-btn text-white text-xs font-bold px-5 py-3 rounded-xl flex items-center gap-2 shadow-md transition-all active:scale-95">
                        <i class="fa-solid fa-rotate"></i> REFRESH
                    </button>
                </div>
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
        var eventsCache = [];

        // ============================================================
        // UTILITY FUNCTIONS
        // ============================================================

        function getTodayDate() {
            return new Date().toISOString().slice(0, 10);
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

        function formatTarikh(dateStr) {
            if (!dateStr) return '—';
            var d = new Date(dateStr + 'T00:00:00');
            return d.toLocaleDateString('ms-MY', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        function baseUrl(path) {
            return '<?= base_url() ?>' + path;
        }

        // ============================================================
        // TAB NAVIGATION
        // ============================================================

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

            var isDaftar = tabId === 'daftar';
            var isDataTab = ['trg', 'luar', 'awam'].includes(tabId);
            
            document.getElementById('data-header').style.display    = isDataTab ? '' : 'none';
            document.getElementById('stat-cards').style.display     = isDataTab ? '' : 'none';
            document.getElementById('data-tables').style.display    = isDataTab ? '' : 'none';
            document.getElementById('daftar-header').style.display  = isDaftar ? '' : 'none';

            if (tabId === 'akaun') {
                muatAkaun(false);
            }

            if (tabId === 'daftar') {
                setTimeout(function() {
                    muatAcaraProgram();
                }, 500);
            }

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
            var validTabs = ['daftar', 'trg', 'luar', 'awam', 'akaun'];

            if (!validTabs.includes(tab)) {
                tab = 'daftar';
            }

            tukarTab(tab);
        }

        window.onload = function () {
            tetapkanTarikhMinimum();
            bukaTabPermulaan();
            muatSenaraiProgram().catch(function (err) {
                console.error('Gagal memuatkan senarai program semasa muat halaman:', err);
                Swal.fire({ icon: 'error', title: 'Ralat', text: (err && err.message) ? err.message : 'Gagal memuatkan senarai program.' });
            });

            var initialTab = getTabAktif();
            if (initialTab === 'daftar') {
                muatDataLive(false);
                setTimeout(function() { muatAcaraProgram(); }, 600);
            } else if (['trg', 'luar', 'awam'].includes(initialTab)) {
                muatDataLive(true);
            }

            muatAkaun(false);
        };

        // ============================================================
        // PROGRAM FUNCTIONS
        // ============================================================

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

        async function muatSenaraiProgram() {
            const res = await fetch('<?= base_url('admin/programs') ?>?t=' + Date.now(), { cache: 'no-store' });
            const list = await res.json();

            if (!res.ok || !Array.isArray(list)) {
                var errMsg = (list && list.message) ? list.message : ('Ralat pelayan (' + res.status + ') semasa memuatkan senarai program.');
                console.error('Gagal memuatkan senarai program:', list);
                throw new Error(errMsg);
            }

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

            var parentDrop = document.getElementById('parentProgramSelect');
            var parentSelected = parentDrop.value || '';
            parentDrop.innerHTML = '<option value="">-- Pilih Program Induk --</option>';
            list.forEach(function(p) {
                var parentId = p.parent_id;
                var isMain = parentId === null || parentId === undefined || parentId === '' || parentId === 0 || parentId === '0' || parentId === 'null';
                if (isMain) {
                    var option = document.createElement('option');
                    option.value = p.kod || p.id;
                    option.textContent = (p.kod || p.id) + ' — ' + p.nama;
                    parentDrop.appendChild(option);
                }
            });
            if (parentSelected && [...parentDrop.options].some(o => o.value === parentSelected)) {
                parentDrop.value = parentSelected;
            }

            binaSenaraProgram(list);
        }

        function setProgramType(type) {
            var form = document.getElementById('programForm');
            form.dataset.type = type;

            var btnUtama  = document.getElementById('btnTypeUtama');
            var btnSub    = document.getElementById('btnTypeSub');
            var parentRow = document.getElementById('parentProgramRow');
            var title     = document.getElementById('programFormTitle');

            if (type === 'utama') {
                btnUtama.className  = 'flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-[#8a0028] bg-[#8a0028] text-white shadow transition-all';
                btnSub.className    = 'flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-[#8a0028] text-[#8a0028] bg-white hover:bg-yellow-50 transition-all';
                parentRow.classList.add('hidden');
                title.innerHTML     = '<i class="fa-solid fa-star text-[#8a0028]"></i> Daftar Program Utama Baharu';
            } else {
                btnSub.className    = 'flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-blue-600 bg-blue-600 text-white shadow transition-all';
                btnUtama.className  = 'flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-[#8a0028] text-[#8a0028] bg-white hover:bg-yellow-50 transition-all';
                parentRow.classList.remove('hidden');
                title.innerHTML     = '<i class="fa-solid fa-sitemap text-blue-600"></i> Daftar Sub Program Baharu';
            }
        }

        function tambahSubProgram(parentKod, parentNama) {
            setProgramType('sub');
            var parentDrop = document.getElementById('parentProgramSelect');
            if ([...parentDrop.options].some(o => o.value === parentKod)) {
                parentDrop.value = parentKod;
            }
            document.getElementById('programForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
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

            var mains = [];
            var subs = [];

            list.forEach(function(p) {
                var parentId = p.parent_id;
                var isSub = parentId !== null && 
                           parentId !== undefined && 
                           parentId !== '' && 
                           parentId !== 0 && 
                           parentId !== '0' && 
                           parentId !== 'null' &&
                           parentId !== 'NULL';
                if (isSub) {
                    subs.push(p);
                } else {
                    mains.push(p);
                }
            });

            mains.sort((a, b) => (a.mula || a.start_date || '').localeCompare(b.mula || b.start_date || ''));

            countEl.textContent = list.length + ' program (' + mains.length + ' utama, ' + subs.length + ' sub)';

            if (list.length === 0) {
                tbody.innerHTML = '<tr><td colspan="12" class="p-8 text-center text-slate-400 italic">Tiada program didaftarkan lagi.</td></tr>';
                return;
            }

            var codeToProgram = {};
            list.forEach(function(p) {
                codeToProgram[p.kod || p.id] = p;
            });

            var idToKod = {};
            list.forEach(function(p) {
                if (p.db_id) idToKod[p.db_id] = p.kod || p.id;
                if (p.id && !isNaN(p.id)) idToKod[parseInt(p.id)] = p.kod || p.id;
            });
            subs.forEach(function(s) {
                if (!s.parent_kod && s.parent_id) {
                    var parentId = parseInt(s.parent_id);
                    if (parentId && idToKod[parentId]) {
                        s.parent_kod = idToKod[parentId];
                    } else {
                        var parentByKod = list.find(function(p) {
                            return p.kod === String(s.parent_id) || p.id === String(s.parent_id);
                        });
                        if (parentByKod) {
                            s.parent_kod = parentByKod.kod || parentByKod.id;
                        } else {
                            s.parent_kod = null;
                        }
                    }
                }
            });

            tbody.innerHTML = '';
            var rowNum = 0;

            mains.forEach(function(p) {
                rowNum++;
                var kod    = p.kod   || p.id || '—';
                var nama   = p.nama  || '—';
                var mula   = p.mula  || p.start_date || '';
                var tamat  = p.tamat || p.end_date   || '';
                var status = String(p.status || '').toUpperCase();
                var isAktif = status === 'AKTIF';
                var statusHtml = isAktif
                    ? '<span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full font-bold text-[10px] uppercase">Aktif</span>'
                    : '<span class="bg-slate-100 text-slate-500 px-2.5 py-1 rounded-full font-bold text-[10px] uppercase">Tamat</span>';
                var berbaki = kiraHariBerbaki(tamat);

                var picNama = p.pic_nama || '—';
                var picTel  = p.pic_tel  || '—';
                tbody.innerHTML += `<tr class="hover:bg-yellow-50/40 transition-all bg-white">
                    <td class="p-4 text-slate-400 font-medium border-l-4 border-[#8a0028]">${rowNum}</td>
                    <td class="p-4 border-l-0">
                        <span class="bg-[#8a0028] text-white px-2 py-1 rounded-md font-bold text-[10px] uppercase tracking-wider whitespace-nowrap">
                            <i class="fa-solid fa-star mr-1"></i>Utama
                        </span>
                    </td>
                    <td class="p-4"><span class="bg-yellow-50 text-[#8a0028] px-2 py-1 rounded-md font-bold text-[10px] uppercase tracking-wider">${escapeHtml(kod)}</span></td>
                    <td class="p-4 font-semibold text-slate-800">${escapeHtml(nama)}</td>
                    <td class="p-4 text-slate-700 whitespace-nowrap"><i class="fa-solid fa-user-tie text-[#8a0028] mr-1 text-[10px]"></i>${escapeHtml(picNama)}</td>
                    <td class="p-4 text-slate-500 whitespace-nowrap">${escapeHtml(picTel)}</td>
                    <td class="p-4 text-slate-600 whitespace-nowrap">${formatTarikh(mula)}</td>
                    <td class="p-4 text-slate-600 whitespace-nowrap">${formatTarikh(tamat)}</td>
                    <td class="p-4 text-slate-500">${kiraTempoh(mula, tamat)}</td>
                    <td class="p-4 text-center">${statusHtml}</td>
                    <td class="p-4 text-center text-xs ${berbaki.cls}">${berbaki.label}</td>
                    <td class="p-4">
                        <div class="flex justify-center gap-2 flex-wrap">
                            <button type="button" onclick="mulaEditProgram('${escapeJs(kod)}')"
                                class="bg-yellow-100 text-[#8a0028] w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-yellow-200 transition-all"
                                title="Edit program"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button type="button" onclick="tambahSubProgram('${escapeJs(kod)}', '${escapeJs(nama)}')"
                                class="bg-blue-100 text-blue-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-blue-200 transition-all"
                                title="Tambah sub program"><i class="fa-solid fa-sitemap"></i></button>
                            <button type="button" onclick="padamProgram('${escapeJs(kod)}', '${escapeJs(nama)}')"
                                class="bg-red-100 text-red-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-red-200 transition-all"
                                title="Padam program"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>`;

                var children = subs.filter(function(s) {
                    var parentMatch = s.parent_kod && s.parent_kod === kod;
                    var idMatch = false;
                    if (p.db_id && s.parent_id) {
                        idMatch = parseInt(s.parent_id) === parseInt(p.db_id);
                    }
                    var kodMatch = s.parent_id && String(s.parent_id) === kod;
                    var idStringMatch = s.parent_id && String(s.parent_id) === p.id;
                    return parentMatch || idMatch || kodMatch || idStringMatch;
                });

                children.forEach(function(s) {
                    var skod    = s.kod   || s.id || '—';
                    var snama   = s.nama  || '—';
                    var smula   = s.mula  || s.start_date || '';
                    var stamat  = s.tamat || s.end_date   || '';
                    var spicNama = s.pic_nama || '—';
                    var spicTel  = s.pic_tel  || '—';
                    var sstatus = String(s.status || '').toUpperCase();
                    var sisAktif = sstatus === 'AKTIF';
                    var sstatusHtml = sisAktif
                        ? '<span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full font-bold text-[10px] uppercase">Aktif</span>'
                        : '<span class="bg-slate-100 text-slate-500 px-2.5 py-1 rounded-full font-bold text-[10px] uppercase">Tamat</span>';
                    var sberbaki = kiraHariBerbaki(stamat);

                    tbody.innerHTML += `<tr class="hover:bg-blue-50/40 transition-all bg-blue-50/20">
                        <td class="p-4 text-slate-300 font-medium pl-8 border-l-4 border-blue-300">↳</td>
                        <td class="p-4">
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-md font-bold text-[10px] uppercase tracking-wider whitespace-nowrap">
                                <i class="fa-solid fa-sitemap mr-1"></i>Sub
                            </span>
                        </td>
                        <td class="p-4"><span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-md font-bold text-[10px] uppercase tracking-wider">${escapeHtml(skod)}</span></td>
                        <td class="p-4 font-medium text-slate-700 pl-2">${escapeHtml(snama)}</td>
                        <td class="p-4 text-slate-700 whitespace-nowrap"><i class="fa-solid fa-user-tie text-blue-500 mr-1 text-[10px]"></i>${escapeHtml(spicNama)}</td>
                        <td class="p-4 text-slate-500 whitespace-nowrap">${escapeHtml(spicTel)}</td>
                        <td class="p-4 text-slate-600 whitespace-nowrap">${formatTarikh(smula)}</td>
                        <td class="p-4 text-slate-600 whitespace-nowrap">${formatTarikh(stamat)}</td>
                        <td class="p-4 text-slate-500">${kiraTempoh(smula, stamat)}</td>
                        <td class="p-4 text-center">${sstatusHtml}</td>
                        <td class="p-4 text-center text-xs ${sberbaki.cls}">${sberbaki.label}</td>
                        <td class="p-4">
                            <div class="flex justify-center gap-2">
                                <button type="button" onclick="mulaEditProgram('${escapeJs(skod)}')"
                                    class="bg-yellow-100 text-[#8a0028] w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-yellow-200 transition-all"
                                    title="Edit sub program"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button type="button" onclick="padamProgram('${escapeJs(skod)}', '${escapeJs(snama)}')"
                                    class="bg-red-100 text-red-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-red-200 transition-all"
                                    title="Padam sub program"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
                });
            });

            var orphans = subs.filter(function(s) {
                var hasParent = mains.some(function(m) {
                    var mKod = m.kod || m.id;
                    var mDbId = m.db_id;
                    if (s.parent_kod && s.parent_kod === mKod) return true;
                    if (s.parent_id && mDbId && parseInt(s.parent_id) === parseInt(mDbId)) return true;
                    if (s.parent_id && String(s.parent_id) === mKod) return true;
                    if (s.parent_id && String(s.parent_id) === m.id) return true;
                    return false;
                });
                return !hasParent;
            });

            orphans.forEach(function(s) {
                var skod    = s.kod   || s.id || '—';
                var snama   = s.nama  || '—';
                var smula   = s.mula  || s.start_date || '';
                var stamat  = s.tamat || s.end_date   || '';
                var spicNama = s.pic_nama || '—';
                var spicTel  = s.pic_tel  || '—';
                var sstatus = String(s.status || '').toUpperCase();
                var sstatusHtml = sstatus === 'AKTIF'
                    ? '<span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full font-bold text-[10px] uppercase">Aktif</span>'
                    : '<span class="bg-slate-100 text-slate-500 px-2.5 py-1 rounded-full font-bold text-[10px] uppercase">Tamat</span>';
                var sberbaki = kiraHariBerbaki(stamat);

                tbody.innerHTML += `<tr class="hover:bg-blue-50/40 transition-all bg-blue-50/20">
                    <td class="p-4 text-slate-300 border-l-4 border-blue-300">—</td>
                    <td class="p-4"><span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-md font-bold text-[10px] uppercase tracking-wider whitespace-nowrap"><i class="fa-solid fa-sitemap mr-1"></i>Sub</span></td>
                    <td class="p-4"><span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-md font-bold text-[10px] uppercase tracking-wider">${escapeHtml(skod)}</span></td>
                    <td class="p-4 font-medium text-slate-700">${escapeHtml(snama)}</td>
                    <td class="p-4 text-slate-700 whitespace-nowrap"><i class="fa-solid fa-user-tie text-blue-500 mr-1 text-[10px]"></i>${escapeHtml(spicNama)}</td>
                    <td class="p-4 text-slate-500 whitespace-nowrap">${escapeHtml(spicTel)}</td>
                    <td class="p-4 text-slate-600 whitespace-nowrap">${formatTarikh(smula)}</td>
                    <td class="p-4 text-slate-600 whitespace-nowrap">${formatTarikh(stamat)}</td>
                    <td class="p-4 text-slate-500">${kiraTempoh(smula, stamat)}</td>
                    <td class="p-4 text-center">${sstatusHtml}</td>
                    <td class="p-4 text-center text-xs ${sberbaki.cls}">${sberbaki.label}</td>
                    <td class="p-4">
                        <div class="flex justify-center gap-2">
                            <button type="button" onclick="mulaEditProgram('${escapeJs(skod)}')"
                                class="bg-yellow-100 text-[#8a0028] w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-yellow-200 transition-all"
                                title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button type="button" onclick="padamProgram('${escapeJs(skod)}', '${escapeJs(snama)}')"
                                class="bg-red-100 text-red-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-red-200 transition-all"
                                title="Padam"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
            });
        }

        function pratonton_poster(input) {
            var box  = document.getElementById('posterPreviewBox');
            var img  = document.getElementById('posterPreviewImg');
            var name = document.getElementById('posterFileName');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    box.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
                name.textContent = input.files[0].name;
                name.classList.remove('hidden');
            } else {
                box.classList.add('hidden');
                name.classList.add('hidden');
            }
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

            var programType = form.dataset.type || 'utama';
            var parentCode  = document.getElementById('parentProgramSelect').value.trim();

            if (programType === 'sub' && !parentCode && form.dataset.mode !== 'edit') {
                Swal.fire({ icon: 'warning', title: 'Program induk diperlukan', text: 'Sila pilih program induk untuk sub program ini.' });
                return;
            }

            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-not-allowed');

            try {
                var isEdit       = form.dataset.mode === 'edit';
                var originalCode = form.dataset.originalCode || programCode;

                var picNama  = document.getElementById('picNama').value.trim();
                var picTel   = document.getElementById('picTel').value.trim();
                var location = document.getElementById('programLocation').value.trim();
                var posterFile = document.getElementById('programPoster').files[0];
                var url;

                var body = new FormData();
                body.append('program_code', programCode);
                body.append('program_name', programName);
                body.append('start_date',   startDate);
                body.append('end_date',     endDate);
                body.append('pic_nama',     picNama);
                body.append('pic_tel',      picTel);
                body.append('location',     location);
                if (posterFile) body.append('poster_image', posterFile);

                if (isEdit) {
                    url = '<?= base_url('admin/programs/update') ?>/' + encodeURIComponent(originalCode);
                } else if (programType === 'sub') {
                    url = '<?= base_url('admin/programs/sub') ?>';
                    body.append('parent_code', parentCode);
                } else {
                    url = '<?= base_url('admin/programs') ?>';
                }

                const res    = await fetch(url, { method: 'POST', body });
                const result = await res.json();

                if (result.success) {
                    resetProgramForm();
                    await muatSenaraiProgram();
                    Swal.fire({ icon: 'success', title: 'Berjaya', text: result.message, timer: 1600, showConfirmButton: false });
                    document.getElementById('tableProgramSenarai').closest('.glass-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Program tidak dapat disimpan.' });
                }
            } catch (err) {
                console.error('daftarProgram error:', err);
                Swal.fire({ icon: 'error', title: 'Ralat', text: (err && err.message) ? err.message : 'Gagal menghantar data program ke pelayan.' });
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
            
            var parentId = program.parent_id;
            var isSub = parentId !== null && 
                       parentId !== undefined && 
                       parentId !== '' && 
                       parentId !== 0 && 
                       parentId !== '0' && 
                       parentId !== 'null' &&
                       parentId !== 'NULL';
            form.dataset.type = isSub ? 'sub' : 'utama';

            document.getElementById('programCode').value = program.kod || program.id || '';
            document.getElementById('programName').value = program.nama || program.name || '';
            document.getElementById('startDate').value = program.mula || program.start_date || '';
            document.getElementById('endDate').value = program.tamat || program.end_date || '';
            document.getElementById('picNama').value = program.pic_nama || '';
            document.getElementById('picTel').value  = program.pic_tel  || '';
            document.getElementById('programLocation').value = program.location || '';

            // Show existing poster preview if available
            var box = document.getElementById('posterPreviewBox');
            var img = document.getElementById('posterPreviewImg');
            var nameLbl = document.getElementById('posterFileName');
            if (program.poster_image) {
                img.src = baseUrl(program.poster_image);
                box.classList.remove('hidden');
                nameLbl.textContent = 'Poster sedia ada (pilih fail baru untuk ganti)';
                nameLbl.classList.remove('hidden');
            } else {
                box.classList.add('hidden');
                nameLbl.classList.add('hidden');
            }

            document.getElementById('programTypeToggle').style.display = 'none';
            document.getElementById('parentProgramRow').classList.add('hidden');

            document.getElementById('programFormTitle').innerHTML = isSub
                ? '<i class="fa-solid fa-pen-to-square text-blue-600"></i> Edit Sub Program'
                : '<i class="fa-solid fa-pen-to-square text-[#8a0028]"></i> Edit Program Utama';
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
            document.getElementById('btnDaftarProgram').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> SIMPAN PROGRAM';
            document.getElementById('btnBatalEditProgram').style.display = 'none';
            document.getElementById('programTypeToggle').style.display = '';
            document.getElementById('parentProgramSelect').value = '';
            document.getElementById('posterPreviewBox').classList.add('hidden');
            document.getElementById('posterFileName').classList.add('hidden');
            setProgramType('utama');
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
                console.error('padamProgram error:', err);
                Swal.fire({ icon: 'error', title: 'Ralat', text: (err && err.message) ? err.message : 'Gagal memadam program.' });
            }
        }

        // ============================================================
        // ACCOUNT FUNCTIONS
        // ============================================================

        async function muatAkaun(showLoading = true) {
            var schoolBody = document.getElementById('tableSchoolAccounts');
            var publicBody = document.getElementById('tablePublicAccounts');

            if (showLoading) {
                Swal.fire({ title: 'Memuatkan akaun...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            }

            try {
                const res = await fetch('<?= base_url('admin/accounts') ?>?t=' + Date.now(), { cache: 'no-store' });
                const result = await res.json();

                if (showLoading) {
                    Swal.close();
                }

                if (!res.ok || !result.success) {
                    throw new Error((result && result.message) ? result.message : 'Senarai akaun tidak dapat dimuatkan.');
                }

                accountCache = {
                    school: Array.isArray(result.school) ? result.school : [],
                    public: Array.isArray(result.public) ? result.public : []
                };
                binaJadualAkaun();
            } catch (err) {
                if (showLoading) {
                    Swal.close();
                    Swal.fire({ icon: 'error', title: 'Ralat', text: (err && err.message) ? err.message : 'Gagal memuatkan akaun.' });
                }
                schoolBody.innerHTML = '<tr><td colspan="4" class="p-8 text-center text-red-500 italic">Gagal memuatkan akaun sekolah.</td></tr>';
                publicBody.innerHTML = '<tr><td colspan="3" class="p-8 text-center text-red-500 italic">Gagal memuatkan akaun awam.</td></tr>';
            }
        }

        function binaJadualAkaun() {
            var schoolBody = document.getElementById('tableSchoolAccounts');
            var publicBody = document.getElementById('tablePublicAccounts');
            var schoolCount = document.getElementById('schoolAccountCount');
            var publicCount = document.getElementById('publicAccountCount');

            schoolCount.textContent = accountCache.school.length + ' akaun';
            publicCount.textContent = accountCache.public.length + ' akaun';

            if (accountCache.school.length === 0) {
                schoolBody.innerHTML = '<tr><td colspan="4" class="p-8 text-center text-slate-400 italic">Tiada akaun sekolah ditemui.</td></tr>';
            } else {
                schoolBody.innerHTML = accountCache.school.map(function(account) {
                    return `<tr class="hover:bg-slate-50 transition-all">
                        <td class="p-4 font-black text-[#8a0028] uppercase whitespace-nowrap">${escapeHtml(account.school_code)}</td>
                        <td class="p-4 font-semibold text-slate-800">${escapeHtml(account.school_name)}</td>
                        <td class="p-4 text-slate-500">${escapeHtml(account.email || '—')}</td>
                        <td class="p-4">
                            <div class="flex justify-center gap-2">
                                <button type="button" onclick="bukaBorangAkaun('school', ${Number(account.id)})"
                                    class="bg-yellow-100 text-[#8a0028] w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-yellow-200 transition-all"
                                    title="Edit akaun"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button type="button" onclick="bukaResetPassword('school', ${Number(account.id)})"
                                    class="bg-blue-100 text-blue-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-blue-200 transition-all"
                                    title="Reset kata laluan"><i class="fa-solid fa-key"></i></button>
                                <button type="button" onclick="padamAkaun('school', ${Number(account.id)})"
                                    class="bg-red-100 text-red-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-red-200 transition-all"
                                    title="Padam akaun"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
                }).join('');
            }

            if (accountCache.public.length === 0) {
                publicBody.innerHTML = '<tr><td colspan="3" class="p-8 text-center text-slate-400 italic">Tiada akaun awam ditemui.</td></tr>';
            } else {
                publicBody.innerHTML = accountCache.public.map(function(account) {
                    return `<tr class="hover:bg-slate-50 transition-all">
                        <td class="p-4 font-semibold text-slate-800">${escapeHtml(account.name)}</td>
                        <td class="p-4 text-slate-500">${escapeHtml(account.email)}</td>
                        <td class="p-4">
                            <div class="flex justify-center gap-2">
                                <button type="button" onclick="bukaBorangAkaun('public', ${Number(account.id)})"
                                    class="bg-yellow-100 text-[#8a0028] w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-yellow-200 transition-all"
                                    title="Edit akaun"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button type="button" onclick="bukaResetPassword('public', ${Number(account.id)})"
                                    class="bg-blue-100 text-blue-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-blue-200 transition-all"
                                    title="Reset kata laluan"><i class="fa-solid fa-key"></i></button>
                                <button type="button" onclick="padamAkaun('public', ${Number(account.id)})"
                                    class="bg-red-100 text-red-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-red-200 transition-all"
                                    title="Padam akaun"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
                }).join('');
            }
        }

        function cariAkaun(type, id) {
            var list = type === 'school' ? accountCache.school : accountCache.public;
            return list.find(function(account) {
                return Number(account.id) === Number(id);
            }) || null;
        }

        async function bukaBorangAkaun(type, id = null) {
            var isEdit = id !== null;
            var account = isEdit ? cariAkaun(type, id) : null;

            if (isEdit && !account) {
                Swal.fire({ icon: 'error', title: 'Akaun tidak ditemui', text: 'Sila refresh senarai akaun.' });
                return;
            }

            var isSchool = type === 'school';
            var title = (isEdit ? 'Kemaskini ' : 'Tambah ') + (isSchool ? 'Akaun Sekolah' : 'Akaun Awam');
            var html = isSchool
                ? `<div class="text-left space-y-3">
                    <input id="swalSchoolCode" class="swal2-input" style="width:100%;margin:0;" placeholder="Kod sekolah" value="${escapeHtml(account ? account.school_code : '')}">
                    <input id="swalSchoolName" class="swal2-input" style="width:100%;margin:0;" placeholder="Nama sekolah" value="${escapeHtml(account ? account.school_name : '')}">
                    <input id="swalAccountEmail" class="swal2-input" style="width:100%;margin:0;" placeholder="Emel" value="${escapeHtml(account ? (account.email || '') : '')}">
                    <input id="swalAccountPassword" class="swal2-input" style="width:100%;margin:0;" type="password" placeholder="${isEdit ? 'Kata laluan baharu (biarkan kosong jika tidak ubah)' : 'Kata laluan'}">
                </div>`
                : `<div class="text-left space-y-3">
                    <input id="swalPublicName" class="swal2-input" style="width:100%;margin:0;" placeholder="Nama penuh" value="${escapeHtml(account ? account.name : '')}">
                    <input id="swalAccountEmail" class="swal2-input" style="width:100%;margin:0;" placeholder="Emel" value="${escapeHtml(account ? account.email : '')}">
                    <input id="swalAccountPassword" class="swal2-input" style="width:100%;margin:0;" type="password" placeholder="${isEdit ? 'Kata laluan baharu (biarkan kosong jika tidak ubah)' : 'Kata laluan'}">
                </div>`;

            var modal = await Swal.fire({
                title: title,
                html: html,
                showCancelButton: true,
                confirmButtonText: isEdit ? 'Simpan' : 'Cipta',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#8a0028',
                focusConfirm: false,
                preConfirm: function() {
                    var payload = isSchool
                        ? {
                            school_code: document.getElementById('swalSchoolCode').value.trim().toUpperCase(),
                            school_name: document.getElementById('swalSchoolName').value.trim(),
                            email: document.getElementById('swalAccountEmail').value.trim().toLowerCase(),
                            password: document.getElementById('swalAccountPassword').value.trim()
                        }
                        : {
                            name: document.getElementById('swalPublicName').value.trim(),
                            email: document.getElementById('swalAccountEmail').value.trim().toLowerCase(),
                            password: document.getElementById('swalAccountPassword').value.trim()
                        };

                    if (isSchool && (!payload.school_code || !payload.school_name || !payload.email)) {
                        Swal.showValidationMessage('Kod sekolah, nama sekolah dan emel diperlukan.');
                        return false;
                    }

                    if (!isSchool && (!payload.name || !payload.email)) {
                        Swal.showValidationMessage('Nama dan emel diperlukan.');
                        return false;
                    }

                    if (!isEdit && !payload.password) {
                        Swal.showValidationMessage('Kata laluan diperlukan untuk akaun baharu.');
                        return false;
                    }

                    return payload;
                }
            });

            if (!modal.isConfirmed) return;

            await hantarAkaun(type, id, modal.value);
        }

        async function bukaResetPassword(type, id) {
            var account = cariAkaun(type, id);
            if (!account) {
                Swal.fire({ icon: 'error', title: 'Akaun tidak ditemui', text: 'Sila refresh senarai akaun.' });
                return;
            }

            var label = type === 'school' ? account.school_name : account.name;
            var modal = await Swal.fire({
                title: 'Reset Kata Laluan',
                text: label,
                input: 'password',
                inputPlaceholder: 'Kata laluan baharu',
                inputAttributes: { autocapitalize: 'off', autocomplete: 'new-password' },
                showCancelButton: true,
                confirmButtonText: 'Reset',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#8a0028',
                preConfirm: function(value) {
                    if (!value || !value.trim()) {
                        Swal.showValidationMessage('Masukkan kata laluan baharu.');
                        return false;
                    }
                    return value.trim();
                }
            });

            if (!modal.isConfirmed) return;

            var payload = type === 'school'
                ? {
                    school_code: account.school_code,
                    school_name: account.school_name,
                    email: account.email || '',
                    password: modal.value
                }
                : {
                    name: account.name,
                    email: account.email,
                    password: modal.value
                };

            await hantarAkaun(type, id, payload);
        }

        async function hantarAkaun(type, id, payload) {
            var isEdit = id !== null && id !== undefined;
            var url = isEdit
                ? '<?= base_url('admin/accounts/update') ?>/' + encodeURIComponent(type) + '/' + encodeURIComponent(id)
                : '<?= base_url('admin/accounts/create') ?>/' + encodeURIComponent(type);

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams(payload)
                });
                const result = await res.json();

                if (!res.ok || !result.success) {
                    throw new Error((result && result.message) ? result.message : 'Akaun tidak dapat disimpan.');
                }

                await muatAkaun(false);
                Swal.fire({ icon: 'success', title: 'Berjaya', text: result.message, timer: 1600, showConfirmButton: false });
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: (err && err.message) ? err.message : 'Akaun tidak dapat disimpan.' });
            }
        }

        async function padamAkaun(type, id) {
            var account = cariAkaun(type, id);
            var label = account ? (type === 'school' ? account.school_name : account.name) : 'akaun ini';
            var confirm = await Swal.fire({
                icon: 'warning',
                title: 'Padam akaun?',
                text: label + ' akan dipadam.',
                showCancelButton: true,
                confirmButtonText: 'Ya, padam',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626'
            });

            if (!confirm.isConfirmed) return;

            try {
                const res = await fetch('<?= base_url('admin/accounts/delete') ?>/' + encodeURIComponent(type) + '/' + encodeURIComponent(id), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                const result = await res.json();

                if (!res.ok || !result.success) {
                    throw new Error((result && result.message) ? result.message : 'Akaun tidak dapat dipadam.');
                }

                await muatAkaun(false);
                Swal.fire({ icon: 'success', title: 'Berjaya', text: result.message, timer: 1600, showConfirmButton: false });
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: (err && err.message) ? err.message : 'Akaun tidak dapat dipadam.' });
            }
        }

        // ============================================================
        // DATA FUNCTIONS (TRG, LUAR, AWAM)
        // ============================================================

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
                    console.error('muatDataLive gagal:', result);
                    if (showLoading) {
                        Swal.fire({ icon: 'error', title: 'Ralat', text: result.message });
                    }
                }
            } catch (err) {
                console.error('muatDataLive error:', err);
                if (showLoading) {
                    Swal.close();
                    Swal.fire({ icon: 'error', title: 'Ralat Sambungan', text: (err && err.message) ? err.message : 'Gagal mendapatkan data dari pengkalan data.' });
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

        // ============================================================
        // PROGRAM POSTER / INFO EDITOR (inside Daftar tab)
        // ============================================================

        async function muatAcaraProgram() {
            var grid = document.getElementById('eventGrid');
            if (!grid) return;

            grid.innerHTML = '<p class="col-span-3 text-center text-slate-400 text-sm py-12"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuatkan program...</p>';

            try {
                const res = await fetch('<?= base_url('admin/events') ?>?t=' + Date.now());
                const result = await res.json();

                // Support both result.programs and result.events (depending on backend)
                var programs = result.programs || result.events || [];

                if (!result.success || programs.length === 0) {
                    grid.innerHTML = `
                        <div class="col-span-3 text-center py-16">
                            <i class="fa-solid fa-calendar-plus text-6xl text-slate-300 mb-4"></i>
                            <p class="text-slate-400 text-sm">Tiada program ditemui.</p>
                            <p class="text-slate-300 text-xs mt-1">Program akan muncul di sini untuk kemaskini poster.</p>
                        </div>`;
                    return;
                }

                grid.innerHTML = '';
                programs.forEach(prog => {
                    if (!prog.start_date || !prog.end_date) return;

                    var today = new Date().toISOString().slice(0, 10);
                    var eventStatus = '', eventStatusColor = '';
                    if (prog.end_date < today) {
                        eventStatus = 'Telah Tamat';
                        eventStatusColor = 'bg-gray-100 text-gray-600';
                    } else if (prog.start_date <= today && prog.end_date >= today) {
                        eventStatus = 'Sedang Berlangsung';
                        eventStatusColor = 'bg-green-100 text-green-700';
                    } else {
                        eventStatus = 'Akan Datang';
                        eventStatusColor = 'bg-blue-100 text-blue-700';
                    }

                    var posterImg = prog.poster_image
                        ? `<img src="${baseUrl(prog.poster_image)}" alt="${escapeHtml(prog.program_name || prog.event_title)}" class="w-full h-48 object-cover">`
                        : `<div class="w-full h-48 bg-gradient-to-br from-[#8a0028]/20 to-[#ffc20e]/20 flex items-center justify-center">
                               <i class="fa-solid fa-image text-5xl text-slate-300"></i>
                           </div>`;

                    var progName = prog.program_name || prog.event_title || '—';
                    var statusBadge = prog.status === 'AKTIF'
                        ? '<span class="bg-green-100 text-green-700 text-[9px] font-bold px-2 py-0.5 rounded-full">Aktif</span>'
                        : '<span class="bg-gray-100 text-gray-600 text-[9px] font-bold px-2 py-0.5 rounded-full">Tidak Aktif</span>';

                    grid.innerHTML += `
                        <div class="glass-card rounded-2xl overflow-hidden transition-all hover:scale-[1.02]">
                            ${posterImg}
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-bold text-[#520018] text-sm flex-1">${escapeHtml(progName)}</h3>
                                    <div class="flex flex-col gap-1 items-end">
                                        <span class="${eventStatusColor} text-[9px] font-bold px-2 py-0.5 rounded-full uppercase whitespace-nowrap">${eventStatus}</span>
                                        ${statusBadge}
                                    </div>
                                </div>
                                ${prog.location ? `<p class="text-xs text-slate-500"><i class="fa-solid fa-location-dot text-[#8a0028]"></i> ${escapeHtml(prog.location)}</p>` : ''}
                                <p class="text-xs text-slate-500 mt-1"><i class="fa-solid fa-calendar-days text-[#8a0028]"></i> ${formatTarikh(prog.start_date)} - ${formatTarikh(prog.end_date)}</p>
                                ${prog.pic_nama ? `<p class="text-xs text-slate-500"><i class="fa-solid fa-user-tie text-[#8a0028]"></i> PIC: ${escapeHtml(prog.pic_nama)}</p>` : ''}
                                ${prog.is_featured ? `<p class="text-[10px] text-yellow-600 mt-1"><i class="fa-solid fa-star"></i> Featured</p>` : ''}
                                <button onclick="bukaEditEventProgram(${prog.id})"
                                    class="mt-4 w-full bg-yellow-100 text-[#8a0028] py-2 rounded-xl text-xs font-bold hover:bg-yellow-200 transition-all">
                                    <i class="fa-solid fa-pen"></i> Kemaskini Poster & Maklumat
                                </button>
                            </div>
                        </div>
                    `;
                });
            } catch (err) {
                console.error('muatAcaraProgram error:', err);
                grid.innerHTML = '<p class="col-span-3 text-center text-red-400 text-sm py-12">Gagal memuatkan program.</p>';
            }
        }

        function bukaEditEventProgram(programId) {
            fetch('<?= base_url('admin/events') ?>?t=' + Date.now())
                .then(res => res.json())
                .then(result => {
                    var programs = result.programs || result.events || [];
                    console.log('[bukaEditEventProgram] API result keys:', Object.keys(result), '| programs count:', programs.length, '| looking for id:', programId);
                    var program = programs.find(p => p.id == programId);
                    if (!program) {
                        console.warn('[bukaEditEventProgram] IDs available:', programs.map(p => p.id));
                        Swal.fire({ icon: 'error', title: 'Program tidak ditemui', text: 'ID: ' + programId + '. Semak Console untuk butiran.' });
                        return;
                    }

                    var progName = program.program_name || program.event_title || '';
                    var posterPreview = program.poster_image
                        ? `<img src="${baseUrl(program.poster_image)}" class="w-full max-h-40 object-cover rounded-lg mb-2">`
                        : '';

                    var html = `
                        <div class="text-left space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Nama Program *</label>
                                <input id="swalEPProgramName" class="swal2-input" style="width:100%;margin:0;"
                                       value="${escapeHtml(progName)}" required>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Tarikh Mula *</label>
                                    <input id="swalEPStartDate" class="swal2-input" style="width:100%;margin:0;"
                                           type="date" value="${program.start_date || ''}" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Tarikh Tamat *</label>
                                    <input id="swalEPEndDate" class="swal2-input" style="width:100%;margin:0;"
                                           type="date" value="${program.end_date || ''}" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Lokasi</label>
                                <input id="swalEPLocation" class="swal2-input" style="width:100%;margin:0;"
                                       value="${escapeHtml(program.location || '')}" placeholder="Lokasi program">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">PIC / Penganjur</label>
                                <input id="swalEPPicNama" class="swal2-input" style="width:100%;margin:0;"
                                       value="${escapeHtml(program.pic_nama || '')}" placeholder="Nama PIC">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">No. Telefon PIC</label>
                                <input id="swalEPPicTel" class="swal2-input" style="width:100%;margin:0;"
                                       value="${escapeHtml(program.pic_tel || '')}" placeholder="No. Telefon PIC">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Poster Program</label>
                                ${posterPreview}
                                <input id="swalEPPoster" class="swal2-input" style="width:100%;margin:0;"
                                       type="file" accept="image/*">
                                <p class="text-[10px] text-slate-400 mt-1">* Biarkan kosong jika tidak mahu tukar poster</p>
                            </div>
                            <div>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" id="swalEPFeatured" ${program.is_featured ? 'checked' : ''}>
                                    <span class="text-sm text-slate-600">Jadikan Program Pilihan (Featured)</span>
                                </label>
                            </div>
                        </div>
                    `;

                    Swal.fire({
                        title: 'Kemaskini Program',
                        html: html,
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#8a0028',
                        width: '650px',
                        preConfirm: function() {
                            var formData = new FormData();
                            formData.append('program_name', document.getElementById('swalEPProgramName').value.trim());
                            formData.append('start_date', document.getElementById('swalEPStartDate').value);
                            formData.append('end_date', document.getElementById('swalEPEndDate').value);
                            formData.append('location', document.getElementById('swalEPLocation').value.trim());
                            formData.append('pic_nama', document.getElementById('swalEPPicNama').value.trim());
                            formData.append('pic_tel', document.getElementById('swalEPPicTel').value.trim());
                            formData.append('is_featured', document.getElementById('swalEPFeatured').checked ? 1 : 0);

                            var posterFile = document.getElementById('swalEPPoster').files[0];
                            if (posterFile) formData.append('poster_image', posterFile);

                            if (!formData.get('program_name')) {
                                Swal.showValidationMessage('Nama program diperlukan.');
                                return false;
                            }
                            if (!formData.get('start_date') || !formData.get('end_date')) {
                                Swal.showValidationMessage('Tarikh mula dan tarikh tamat diperlukan.');
                                return false;
                            }
                            return formData;
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            hantarUpdateEventProgram(programId, result.value);
                        }
                    });
                });
        }

        async function hantarUpdateEventProgram(programId, formData) {
            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const res = await fetch('<?= base_url('admin/events/update') ?>/' + programId, {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                Swal.close();

                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Berjaya', text: result.message, timer: 1600, showConfirmButton: false });
                    muatAcaraProgram();
                    muatSenaraiProgram();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message });
                }
            } catch (err) {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Ralat', text: 'Gagal menyimpan maklumat program.' });
            }
        }

        // ============================================================
        // EVENTS FUNCTIONS
        // ============================================================

        function bukaBorangAcara(eventId = null) {
            var isEdit = eventId !== null;
            var title = isEdit ? 'Kemaskini Acara' : 'Tambah Acara Baharu';
            
            // Build program options
            var progOptions = '<option value="">-- Tiada Program --</option>';
            programCache.forEach(function(p) {
                var selected = '';
                if (isEdit) {
                    // We'll set selected after fetching event data
                }
                progOptions += `<option value="${p.id}">${escapeHtml(p.nama)} (${escapeHtml(p.kod || p.id)})</option>`;
            });
            
            var html = `
                <div class="text-left space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Tajuk Acara *</label>
                        <input id="swalEventTitle" class="swal2-input" style="width:100%;margin:0;" 
                               placeholder="Tajuk acara" value="${isEdit ? '' : ''}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Program Berkaitan</label>
                        <select id="swalProgramId" class="swal2-input" style="width:100%;margin:0;">
                            ${progOptions}
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Tarikh Mula *</label>
                            <input id="swalStartDate" class="swal2-input" style="width:100%;margin:0;" 
                                   type="date" value="" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Tarikh Tamat *</label>
                            <input id="swalEndDate" class="swal2-input" style="width:100%;margin:0;" 
                                   type="date" value="" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Lokasi</label>
                        <input id="swalLocation" class="swal2-input" style="width:100%;margin:0;" 
                               placeholder="Lokasi acara" value="">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Penerangan</label>
                        <textarea id="swalDescription" class="swal2-input" style="width:100%;margin:0;min-height:80px;resize:vertical;" 
                                  placeholder="Penerangan acara"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Poster Acara</label>
                        <div id="swalPosterPreview" style="display:none;margin-bottom:8px;"></div>
                        <input id="swalPoster" class="swal2-input" style="width:100%;margin:0;" 
                               type="file" accept="image/*">
                    </div>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="swalFeatured">
                            <span class="text-sm text-slate-600">Jadikan Acara Pilihan (Featured)</span>
                        </label>
                    </div>
                </div>
            `;
            
            // If editing, fetch event data first
            if (isEdit) {
                fetch('<?= base_url('admin/events') ?>?t=' + Date.now())
                    .then(res => res.json())
                    .then(result => {
                        var event = result.events.find(e => e.id === eventId);
                        if (event) {
                            showEventForm(event, title, isEdit);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Acara tidak ditemui' });
                        }
                    });
            } else {
                showEventForm(null, title, isEdit);
            }
        }

        function showEventForm(event, title, isEdit) {
            // Rebuild program options with selection
            var progOptions = '<option value="">-- Tiada Program --</option>';
            programCache.forEach(function(p) {
                var selected = (event && event.program_id == p.id) ? 'selected' : '';
                progOptions += `<option value="${p.id}" ${selected}>${escapeHtml(p.nama)} (${escapeHtml(p.kod || p.id)})</option>`;
            });
            
            var html = `
                <div class="text-left space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Tajuk Acara *</label>
                        <input id="swalEventTitle" class="swal2-input" style="width:100%;margin:0;" 
                               placeholder="Tajuk acara" value="${event ? escapeHtml(event.event_title) : ''}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Program Berkaitan</label>
                        <select id="swalProgramId" class="swal2-input" style="width:100%;margin:0;">
                            ${progOptions}
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Tarikh Mula *</label>
                            <input id="swalStartDate" class="swal2-input" style="width:100%;margin:0;" 
                                   type="date" value="${event ? event.start_date : ''}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Tarikh Tamat *</label>
                            <input id="swalEndDate" class="swal2-input" style="width:100%;margin:0;" 
                                   type="date" value="${event ? event.end_date : ''}" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Lokasi</label>
                        <input id="swalLocation" class="swal2-input" style="width:100%;margin:0;" 
                               placeholder="Lokasi acara" value="${event ? escapeHtml(event.location) : ''}">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Penerangan</label>
                        <textarea id="swalDescription" class="swal2-input" style="width:100%;margin:0;min-height:80px;resize:vertical;" 
                                  placeholder="Penerangan acara">${event ? escapeHtml(event.event_description) : ''}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Poster Acara</label>
                        ${event && event.poster_image ? `
                            <div style="margin-bottom:8px;">
                                <img src="${baseUrl(event.poster_image)}" style="max-height:100px;border-radius:8px;border:1px solid #e5e7eb;">
                                <p class="text-[10px] text-slate-400 mt-1">Poster sedia ada</p>
                            </div>
                        ` : ''}
                        <input id="swalPoster" class="swal2-input" style="width:100%;margin:0;" 
                               type="file" accept="image/*">
                    </div>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="swalFeatured" ${event && event.is_featured ? 'checked' : ''}>
                            <span class="text-sm text-slate-600">Jadikan Acara Pilihan (Featured)</span>
                        </label>
                    </div>
                </div>
            `;
            
            Swal.fire({
                title: title,
                html: html,
                showCancelButton: true,
                confirmButtonText: isEdit ? 'Kemaskini' : 'Cipta',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#8a0028',
                width: '650px',
                preConfirm: function() {
                    var formData = new FormData();
                    formData.append('event_title', document.getElementById('swalEventTitle').value.trim());
                    formData.append('program_id', document.getElementById('swalProgramId').value);
                    formData.append('start_date', document.getElementById('swalStartDate').value);
                    formData.append('end_date', document.getElementById('swalEndDate').value);
                    formData.append('location', document.getElementById('swalLocation').value.trim());
                    formData.append('event_description', document.getElementById('swalDescription').value.trim());
                    formData.append('is_featured', document.getElementById('swalFeatured').checked ? 1 : 0);
                    
                    var posterFile = document.getElementById('swalPoster').files[0];
                    if (posterFile) {
                        formData.append('poster_image', posterFile);
                    }
                    
                    if (!formData.get('event_title')) {
                        Swal.showValidationMessage('Tajuk acara diperlukan.');
                        return false;
                    }
                    if (!formData.get('start_date') || !formData.get('end_date')) {
                        Swal.showValidationMessage('Tarikh mula dan tarikh tamat diperlukan.');
                        return false;
                    }
                    
                    return formData;
                }
            }).then(result => {
                if (result.isConfirmed) {
                    var url = isEdit 
                        ? '<?= base_url('admin/events/update') ?>/' + event.id
                        : '<?= base_url('admin/events/create') ?>';
                    hantarAcara(url, result.value, isEdit);
                }
            });
        }

        async function hantarAcara(url, formData, isEdit) {
            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            try {
                const res = await fetch(url, { method: 'POST', body: formData });
                const result = await res.json();
                Swal.close();
                
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Berjaya', text: result.message, timer: 1600, showConfirmButton: false });
                    muatAcaraProgram();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Acara tidak dapat disimpan.' });
                }
            } catch (err) {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Ralat', text: 'Gagal menyimpan acara.' });
            }
        }

        async function padamAcara(eventId, eventTitle) {
            var confirm = await Swal.fire({
                icon: 'warning',
                title: 'Padam acara?',
                text: 'Acara "' + eventTitle + '" akan dipadam.',
                showCancelButton: true,
                confirmButtonText: 'Ya, padam',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626'
            });
            
            if (!confirm.isConfirmed) return;
            
            Swal.fire({ title: 'Memadam...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            try {
                const res = await fetch('<?= base_url('admin/events/delete') ?>/' + eventId, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                const result = await res.json();
                Swal.close();
                
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Berjaya', text: result.message, timer: 1600, showConfirmButton: false });
                    muatAcaraProgram();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Acara tidak dapat dipadam.' });
                }
            } catch (err) {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Ralat', text: 'Gagal memadam acara.' });
            }
        }
    </script>
</body>
</html>