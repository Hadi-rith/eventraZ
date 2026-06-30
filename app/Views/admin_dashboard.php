<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventraZ - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root { --maroon: #8a0028; --maroon-dark: #520018; --gold: #ffc20e; --ink: #231f20; }

        /* Flatpickr theming to match EventraZ branding */
        .flatpickr-day.selected, .flatpickr-day.selected:hover,
        .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: var(--maroon); border-color: var(--maroon);
        }
        .flatpickr-day.today { border-color: var(--gold); }
        .flatpickr-day:hover { background: #fdf0e8; }
        .flatpickr-months .flatpickr-month,
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        span.flatpickr-weekday { background: var(--maroon); color: #fff; fill: #fff; }
        .flatpickr-current-month input.cur-year { color: #fff; }
        .flatpickr-months .flatpickr-prev-month, .flatpickr-months .flatpickr-next-month { fill: #fff; }
        .numInputWrapper span.arrowUp:after { border-bottom-color: var(--maroon); }
        .numInputWrapper span.arrowDown:after { border-top-color: var(--maroon); }
        .flatpickr-time input:focus, .flatpickr-time .flatpickr-am-pm:focus { background: #fdf0e8; }
        .flatpickr-time .flatpickr-am-pm:hover, .flatpickr-time input:hover { background: #fdf0e8; }
        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at 18% 8%, rgba(255,194,14,.22), transparent 28%),
                radial-gradient(circle at 88% 16%, rgba(138,0,40,.18), transparent 26%),
                linear-gradient(135deg, #fffaf0 0%, #f7eef2 46%, #fff8df 100%);
            color: var(--ink);
        }
        .sidebar {
            background: linear-gradient(160deg, rgba(82,0,24,.92), rgba(138,0,40,.82));
            width: 280px; height: 100vh; position: fixed; top: 0; left: 0;
            overflow-y: auto; border-right: 1px solid rgba(255,255,255,.25);
            box-shadow: 24px 0 60px rgba(82,0,24,.22);
            backdrop-filter: blur(24px) saturate(160%);
        }
        .brand-logo { width: 178px; background: #fff; border-radius: 28px; padding: 6px; filter: drop-shadow(0 14px 20px rgba(82,0,24,.16)); }
        .glass-card { background: rgba(255,255,255,.58) !important; border: 1px solid rgba(255,255,255,.82) !important; box-shadow: 0 24px 58px rgba(82,0,24,.12), inset 0 1px 0 rgba(255,255,255,.9) !important; backdrop-filter: blur(26px) saturate(160%); }
        .active-nav { background: rgba(255,194,14,.98) !important; color: #520018 !important; box-shadow: 0 16px 34px rgba(255,194,14,.24); }
        .eventraz-btn { background: linear-gradient(135deg, var(--maroon), var(--maroon-dark)) !important; }
        .eventraz-btn:hover { filter: brightness(1.08); }
        .eventraz-field { background: rgba(255,255,255,.58) !important; border-color: rgba(138,0,40,.15) !important; }
        .eventraz-field:focus { box-shadow: 0 0 0 3px rgba(255,194,14,.28); }
        select { background: #fff !important; color: #111827 !important; }
        select option { background: #fff !important; color: #111827 !important; font-weight: 500; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .super-admin-badge { background: linear-gradient(135deg, #ffc20e, #e6a800); color: #520018; font-size: 9px; font-weight: 900; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: .08em; }
        .admin-badge { background: rgba(255,255,255,.2); color: #fef3c7; font-size: 9px; font-weight: 700; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; }
        .capacity-bar { height: 6px; border-radius: 99px; background: #e5e7eb; overflow: hidden; }
        .capacity-fill { height: 100%; border-radius: 99px; transition: width .4s ease; }
        .count-link { cursor: pointer; text-decoration: underline; text-underline-offset: 3px; }
        .count-link:hover { color: #520018; }
    </style>
    <?= view('partials/mobile_responsive', ['mobileLayout' => 'sidebar']) ?>
</head>
<body class="flex app-shell">

<?php
$role      = session('role');
$adminName = session('admin_name') ?? 'Admin';
$isSuperAdmin = ($role === 'super_admin');
?>

<!-- Sidebar -->
<div class="sidebar app-sidebar p-6 flex flex-col justify-between text-white shadow-2xl z-10">
    <div>
        <div class="mb-8 border-b border-white/15 pb-5 text-center">
            <img src="<?= base_url('assets/eventraz-logo.jpeg') ?>" alt="EventraZ" class="brand-logo mx-auto mb-3">
            <h1 class="text-xl font-black text-white tracking-wider">EventraZ Admin</h1>
            <p class="text-[9px] text-yellow-200 uppercase font-bold mt-1">Event Tracking, Registration &amp; Engagement Zone</p>
            <div class="mt-3 flex flex-col items-center gap-1">
                <?php if ($isSuperAdmin): ?>
                    <span class="super-admin-badge"><i class="fa-solid fa-crown mr-1"></i> Super Admin</span>
                <?php else: ?>
                    <span class="admin-badge"><i class="fa-solid fa-user-shield mr-1"></i> Admin</span>
                <?php endif; ?>
                <p class="text-[10px] text-yellow-100 mt-1 font-semibold"><?= esc($adminName) ?></p>
            </div>
        </div>

        <!-- Program Filter (data tabs only) -->
        <div class="mb-6" id="filterWrap">
            <label class="block text-[10px] font-bold text-yellow-100 uppercase mb-2 ml-1 tracking-wider">Tapis Program Utama</label>
            <select id="filterProgramMain" onchange="bilaTukarFilterUtama()"
                style="color:#111827;background:#fff;"
                class="w-full p-3 bg-white border border-white/70 rounded-xl text-xs text-slate-900 outline-none focus:ring-2 focus:ring-yellow-300">
                <option value="SEMUA">-- SEMUA PROGRAM --</option>
            </select>

            <div id="filterSubWrap" class="mt-3 hidden">
                <label class="block text-[10px] font-bold text-yellow-100 uppercase mb-2 ml-1 tracking-wider">Tapis Sub Program</label>
                <select id="filterProgramSub" onchange="tapisSemuaData()"
                    style="color:#111827;background:#fff;"
                    class="w-full p-3 bg-white border border-white/70 rounded-xl text-xs text-slate-900 outline-none focus:ring-2 focus:ring-yellow-300">
                    <option value="SEMUA">-- SEMUA SUB PROGRAM --</option>
                </select>
            </div>
        </div>

        <nav class="space-y-2">
            <button onclick="tukarTab('daftar', this)"
                class="nav-btn w-full text-left p-3.5 text-xs font-bold active-nav flex items-center gap-3 rounded-xl transition-all">
                <i class="fa-solid fa-calendar-plus"></i> DAFTAR PROGRAM
            </button>
            <div class="border-t border-white/15 my-2"></div>
            <button onclick="tukarTab('sekolah', this)"
                class="nav-btn w-full text-left p-3.5 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                <i class="fa-solid fa-school"></i> SEKOLAH
            </button>
            <button onclick="tukarTab('awam', this)"
                class="nav-btn w-full text-left p-3.5 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                <i class="fa-solid fa-user-group"></i> ORANG AWAM
            </button>
            <button onclick="tukarTab('program-stats', this)"
                class="nav-btn w-full text-left p-3.5 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                <i class="fa-solid fa-chart-pie"></i> STATISTIK PROGRAM
            </button>
            <div class="border-t border-white/15 my-2"></div>
            <button onclick="tukarTab('akaun', this)"
                class="nav-btn w-full text-left p-3.5 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                <i class="fa-solid fa-users-gear"></i> AKAUN PENGGUNA
            </button>
            <?php if ($isSuperAdmin): ?>
            <button onclick="tukarTab('stats', this)"
                class="nav-btn w-full text-left p-3.5 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                <i class="fa-solid fa-chart-bar"></i> STATISTIK ADMIN
            </button>
            <?php endif; ?>
        </nav>
    </div>
    <a href="<?= base_url('logout') ?>"
        class="text-xs text-yellow-100 font-bold p-3 flex items-center gap-2 hover:bg-white/10 rounded-xl transition-all mt-6">
        <i class="fa-solid fa-power-off"></i> LOG KELUAR
    </a>
</div>

<!-- Main -->
<div class="app-main ml-[280px] w-full p-8 min-h-screen">

    <!-- Daftar Program Tab -->
    <div id="tab-daftar" class="tab-content active">
        <div class="glass-card flex justify-between items-center mb-8 p-6 rounded-2xl">
            <div>
                <h2 class="text-2xl font-black text-[#520018] uppercase tracking-tight">Daftar Program</h2>
                <p class="text-xs text-slate-400 mt-1">Tambah dan urus program <?= $isSuperAdmin ? 'seluruh sistem' : 'anda' ?></p>
            </div>
            <span class="bg-yellow-100/80 text-[#8a0028] text-xs font-bold px-4 py-2 rounded-xl uppercase flex items-center gap-2">
                <i class="fa-solid fa-calendar-plus"></i> Program Management
            </span>
        </div>

        <!-- Form Card -->
        <div class="glass-card p-8 rounded-2xl mb-8">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h3 id="programFormTitle" class="text-lg font-black text-[#520018] uppercase tracking-wider flex items-center gap-3">
                        <i class="fa-solid fa-calendar-plus text-[#8a0028]"></i> Daftar Program Baharu
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Status program dikira automatik berdasarkan tarikh.</p>
                </div>
                <span class="bg-yellow-100/80 text-[#8a0028] text-[10px] font-bold px-3 py-1 rounded-full uppercase">DB: programs</span>
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

            <!-- Parent Program selector -->
            <div id="parentProgramRow" class="hidden mb-5 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <label class="block text-[10px] font-bold text-[#8a0028] uppercase mb-2 ml-1 tracking-wider">
                    <i class="fa-solid fa-sitemap mr-1"></i> Program Induk *
                </label>
                <select id="parentProgramSelect" name="parent_code"
                    class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                    <option value="">-- Pilih Program Induk --</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1.5 ml-1">Hanya program utama boleh dipilih.</p>
            </div>

            <form id="programForm" onsubmit="daftarProgram(event)" class="grid grid-cols-12 gap-5 items-end" data-mode="create" data-original-code="" data-type="utama">
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Kod Program *</label>
                    <input type="text" id="programCode" name="program_code" maxlength="30" placeholder="CONTOH: EVZ2026"
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none uppercase">
                </div>
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Nama Program *</label>
                    <input type="text" id="programName" name="program_name" placeholder="Masukkan nama program"
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                </div>
                <div class="col-span-12 md:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Had Pendaftaran</label>
                    <input type="number" id="regLimit" name="registration_limit" min="0" placeholder="0 = tiada had"
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                </div>
                <div class="col-span-12 md:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Status</label>
                    <div id="programStatusPreview" class="w-full p-3 border border-slate-200 rounded-xl text-[10px] text-center font-black bg-slate-50 text-slate-500 uppercase">AUTO</div>
                </div>
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Tarikh Mula *</label>
                    <input type="text" id="startDate" name="start_date" placeholder="DD/MM/YYYY" autocomplete="off"
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                </div>
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Tarikh Tamat *</label>
                    <input type="text" id="endDate" name="end_date" placeholder="DD/MM/YYYY" autocomplete="off"
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                </div>
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Masa Acara</label>
                    <input type="text" id="eventTime" name="event_time" placeholder="--:-- --" autocomplete="off"
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                </div>
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Penganjur</label>
                    <input type="text" id="organizer" name="organizer" placeholder="Nama Penganjur"
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                </div>
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider"><i class="fa-solid fa-user-tie mr-1 text-[#8a0028]"></i> Nama PIC</label>
                    <input type="text" id="picNama" name="pic_nama" placeholder="Nama PIC"
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                </div>
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider"><i class="fa-solid fa-phone mr-1 text-[#8a0028]"></i> No. Tel PIC</label>
                    <input type="text" id="picTel" name="pic_tel" placeholder="013XXXXXXX"
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider"><i class="fa-solid fa-location-dot mr-1 text-[#8a0028]"></i> Lokasi Program</label>
                    <input type="text" id="programLocation" name="location" placeholder="Dewan Utama, KL"
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none">
                </div>
                <div class="col-span-12">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Penerangan Program</label>
                    <textarea id="programDescription" name="description" rows="2" placeholder="Penerangan ringkas program..."
                        class="eventraz-field w-full p-3 border rounded-xl text-sm outline-none resize-none"></textarea>
                </div>
                <div class="col-span-12 md:col-span-6">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider"><i class="fa-solid fa-star mr-1 text-[#ffc20e]"></i> Tetapan Pilihan</label>
                    <label class="flex items-center gap-3 p-3 border border-[#ffc20e]/40 bg-yellow-50/60 rounded-xl cursor-pointer hover:bg-yellow-50 transition-all select-none">
                        <input type="checkbox" id="isFeatured" name="is_featured" value="1"
                            class="w-4 h-4 accent-[#8a0028] cursor-pointer">
                        <span class="text-xs font-bold text-[#520018]">Program Pilihan
                            <span class="text-[10px] text-slate-400 font-normal">(dipaparkan dalam bahagian Pilihan pada halaman Acara)</span>
                        </span>
                    </label>
                </div>
                <div class="col-span-12">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider"><i class="fa-solid fa-image mr-1 text-[#8a0028]"></i> Poster Program</label>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <label for="programPoster" class="flex items-center gap-3 w-full p-3 border-2 border-dashed border-[#8a0028]/30 rounded-xl cursor-pointer hover:border-[#8a0028]/60 hover:bg-yellow-50/40 transition-all eventraz-field">
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
                    <i class="fa-solid fa-list-check text-[#8a0028]"></i> Senarai Program
                </div>
                <span id="programCount" class="bg-[#8a0028]/10 text-[#8a0028] text-[10px] font-bold px-3 py-1 rounded-full">Memuatkan...</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b">
                        <tr>
                            <th class="p-4">#</th>
                            <th class="p-4 text-center">Poster</th>
                            <th class="p-4">Jenis</th>
                            <th class="p-4">Kod</th>
                            <th class="p-4">Nama Program</th>
                            <th class="p-4">Tarikh</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Had / Kapasiti</th>
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

    <!-- Sekolah Tab -->
    <div id="tab-sekolah" class="tab-content">
        <div class="glass-card flex justify-between items-center mb-8 p-6 rounded-2xl">
            <div>
                <h2 class="text-2xl font-black text-[#520018] uppercase tracking-tight">Sekolah</h2>
                <p class="text-xs text-slate-400 mt-1">Rekod pendaftaran sekolah</p>
            </div>
            <button onclick="muatDataLive()" class="eventraz-btn text-white text-xs font-bold px-5 py-3 rounded-xl flex items-center gap-2 shadow-md transition-all active:scale-95">
                <i class="fa-solid fa-rotate"></i> REFRESH
            </button>
        </div>
        <div class="grid grid-cols-1 gap-6 mb-8">
            <div class="glass-card p-6 rounded-2xl border-l-4 border-[#8a0028]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jumlah Sekolah</p>
                <h3 id="statSekolah" class="text-3xl font-black text-slate-800 mt-1">—</h3>
            </div>
        </div>
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="p-5 border-b font-bold text-sm text-slate-700 bg-slate-50 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-school text-[#8a0028]"></i> Senarai Sekolah
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
                            <th class="p-4">Emel / Tel</th>
                            <th class="p-4 text-center">Bil. Murid</th>
                        </tr>
                    </thead>
                    <tbody id="tableSekolah" class="divide-y text-slate-600">
                        <tr><td colspan="7" class="p-8 text-center text-slate-400 italic">Memuatkan data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="tab-awam" class="tab-content">
        <div class="glass-card flex justify-between items-center mb-8 p-6 rounded-2xl">
            <div>
                <h2 class="text-2xl font-black text-[#520018] uppercase tracking-tight">Orang Awam</h2>
                <p class="text-xs text-slate-400 mt-1">Rekod pendaftaran orang awam</p>
            </div>
            <button onclick="muatDataLive()" class="eventraz-btn text-white text-xs font-bold px-5 py-3 rounded-xl flex items-center gap-2 shadow-md transition-all active:scale-95">
                <i class="fa-solid fa-rotate"></i> REFRESH
            </button>
        </div>
        <div class="grid grid-cols-1 gap-6 mb-8">
            <div class="glass-card p-6 rounded-2xl border-l-4 border-[#520018]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jumlah Orang Awam</p>
                <h3 id="statAwam" class="text-3xl font-black text-slate-800 mt-1">—</h3>
            </div>
        </div>
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="p-5 border-b font-bold text-sm text-slate-700 bg-slate-50 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-user-group text-[#8a0028]"></i> Senarai Pendaftaran Awam
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b">
                        <tr>
                            <th class="p-4">Tarikh</th>
                            <th class="p-4">Program</th>
                            <th class="p-4">Nama</th>
                            <th class="p-4">No. IC</th>
                            <th class="p-4">No. Tel</th>
                            <th class="p-4">Emel</th>
                            <th class="p-4 text-center">Bil. Ahli</th>
                        </tr>
                    </thead>
                    <tbody id="tableAwam" class="divide-y text-slate-600">
                        <tr><td colspan="7" class="p-8 text-center text-slate-400 italic">Memuatkan data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Per-Program Statistics Tab -->
    <div id="tab-program-stats" class="tab-content">
        <div class="glass-card flex justify-between items-center mb-8 p-6 rounded-2xl">
            <div>
                <h2 class="text-2xl font-black text-[#520018] uppercase tracking-tight">Statistik Program</h2>
                <p class="text-xs text-slate-400 mt-1"><?= $isSuperAdmin ? 'Prestasi pendaftaran semua program dalam sistem' : 'Prestasi pendaftaran program yang anda cipta sahaja' ?> — boleh dieksport ke Excel / Google Sheets</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="eksportStatistikProgram()" class="bg-white border-2 border-[#8a0028] text-[#8a0028] text-xs font-bold px-5 py-3 rounded-xl flex items-center gap-2 shadow-sm transition-all active:scale-95 hover:bg-yellow-50">
                    <i class="fa-solid fa-file-csv"></i> EKSPORT SEMUA (CSV)
                </button>
                <button onclick="muatStatistikProgram()" class="eventraz-btn text-white text-xs font-bold px-5 py-3 rounded-xl flex items-center gap-2 shadow-md transition-all active:scale-95">
                    <i class="fa-solid fa-rotate"></i> REFRESH
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8" id="programStatsSummary">
            <div class="glass-card p-5 rounded-2xl border-l-4 border-[#8a0028]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jumlah Program</p>
                <h3 id="psTotalPrograms" class="text-2xl font-black text-slate-800 mt-1">—</h3>
            </div>
            <div class="glass-card p-5 rounded-2xl border-l-4 border-blue-500">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jumlah Peserta</p>
                <h3 id="psTotalParticipants" class="text-2xl font-black text-slate-800 mt-1">—</h3>
            </div>
            <div class="glass-card p-5 rounded-2xl border-l-4 border-green-500">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Murid (Sekolah)</p>
                <h3 id="psTotalMurid" class="text-2xl font-black text-slate-800 mt-1">—</h3>
            </div>
            <div class="glass-card p-5 rounded-2xl border-l-4 border-[#ffc20e]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Peserta Awam</p>
                <h3 id="psTotalAwam" class="text-2xl font-black text-slate-800 mt-1">—</h3>
            </div>
        </div>

        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="p-5 border-b bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2 font-bold text-sm text-slate-700 uppercase tracking-wider">
                    <i class="fa-solid fa-chart-pie text-[#8a0028]"></i> Statistik Setiap Program
                </div>
                <span id="programStatsCount" class="bg-[#8a0028]/10 text-[#8a0028] text-[10px] font-bold px-3 py-1 rounded-full">Memuatkan...</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b">
                        <tr>
                            <th class="p-3">Program</th>
                            <th class="p-3">Tarikh</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Kapasiti</th>
                            <th class="p-3 text-center">Sekolah</th>
                            <th class="p-3 text-center">Murid</th>
                            <th class="p-3 text-center">Guru</th>
                            <th class="p-3 text-center">Awam</th>
                            <th class="p-3 text-center">Peserta Awam</th>
                            <th class="p-3 text-center">Jumlah</th>
                            <th class="p-3 text-center">Kehadiran Awam</th>
                            <th class="p-3 text-center">Eksport</th>
                        </tr>
                    </thead>
                    <tbody id="tableProgramStats" class="divide-y text-slate-600">
                        <tr><td colspan="12" class="p-8 text-center text-slate-400 italic">Memuatkan statistik...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Akaun Tab -->
    <div id="tab-akaun" class="tab-content">
        <div class="glass-card flex justify-between items-center mb-8 p-6 rounded-2xl">
            <div>
                <h2 class="text-2xl font-black text-[#520018] uppercase tracking-tight">Akaun Pengguna</h2>
                <p class="text-xs text-slate-400 mt-1">Urus akaun sekolah, awam<?= $isSuperAdmin ? ' dan admin' : '' ?></p>
            </div>
            <div class="flex flex-wrap justify-end gap-2">
                <?php if ($isSuperAdmin): ?>
                <button onclick="bukaBorangAkaun('admin')"
                    class="bg-yellow-400 border-2 border-yellow-500 text-[#520018] text-xs font-bold px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm transition-all active:scale-95 hover:bg-yellow-300">
                    <i class="fa-solid fa-crown"></i> TAMBAH ADMIN
                </button>
                <?php endif; ?>
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

        <?php if ($isSuperAdmin): ?>
        <!-- Admin Accounts Table -->
        <div class="glass-card rounded-2xl overflow-hidden mb-6">
            <div class="p-5 border-b bg-yellow-50 flex items-center justify-between">
                <div class="flex items-center gap-2 font-bold text-sm text-slate-700 uppercase tracking-wider">
                    <i class="fa-solid fa-crown text-yellow-500"></i> Akaun Admin
                </div>
                <span id="adminAccountCount" class="bg-yellow-400/30 text-[#520018] text-[10px] font-bold px-3 py-1 rounded-full">0 akaun</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b">
                        <tr>
                            <th class="p-4">Username</th>
                            <th class="p-4">Nama</th>
                            <th class="p-4">Emel</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody id="tableAdminAccounts" class="divide-y text-slate-600">
                        <tr><td colspan="5" class="p-8 text-center text-slate-400 italic">Memuatkan akaun...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

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

    <?php if ($isSuperAdmin): ?>
    <!-- Super Admin Stats Tab -->
    <div id="tab-stats" class="tab-content">
        <div class="glass-card flex justify-between items-center mb-8 p-6 rounded-2xl">
            <div>
                <h2 class="text-2xl font-black text-[#520018] uppercase tracking-tight">Statistik Admin</h2>
                <p class="text-xs text-slate-400 mt-1">Prestasi setiap Admin dalam sistem</p>
            </div>
            <button onclick="muatStats()" class="eventraz-btn text-white text-xs font-bold px-5 py-3 rounded-xl flex items-center gap-2 shadow-md transition-all active:scale-95">
                <i class="fa-solid fa-rotate"></i> REFRESH
            </button>
        </div>

        <!-- System-wide stats -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-8" id="sysStatsGrid">
            <div class="glass-card p-6 rounded-2xl border-l-4 border-[#8a0028]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jumlah Admin</p>
                <h3 id="statTotalAdmins" class="text-3xl font-black text-slate-800 mt-1">—</h3>
            </div>
            <div class="glass-card p-6 rounded-2xl border-l-4 border-blue-500">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jumlah Program</p>
                <h3 id="statTotalPrograms" class="text-3xl font-black text-slate-800 mt-1">—</h3>
            </div>
            <div class="glass-card p-6 rounded-2xl border-l-4 border-green-500">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Program Aktif</p>
                <h3 id="statActivePrograms" class="text-3xl font-black text-slate-800 mt-1">—</h3>
            </div>
            <div class="glass-card p-6 rounded-2xl border-l-4 border-[#ffc20e]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jumlah Pendaftaran</p>
                <h3 id="statTotalRegs" class="text-3xl font-black text-slate-800 mt-1">—</h3>
            </div>
            <div class="glass-card p-6 rounded-2xl border-l-4 border-[#8a0028]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pendaftaran Sekolah</p>
                <h3 id="statSekolahRegs" class="text-3xl font-black text-slate-800 mt-1">—</h3>
            </div>
            <div class="glass-card p-6 rounded-2xl border-l-4 border-[#520018]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pendaftaran Awam</p>
                <h3 id="statAwamRegs" class="text-3xl font-black text-slate-800 mt-1">—</h3>
            </div>
        </div>

        <!-- Per-admin table -->
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="p-5 border-b bg-slate-50 font-bold text-sm text-slate-700 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-chart-bar text-[#8a0028]"></i> Prestasi Setiap Admin
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b">
                        <tr>
                            <th class="p-4">Nama Admin</th>
                            <th class="p-4">Username</th>
                            <th class="p-4 text-center">Program</th>
                            <th class="p-4 text-center">Aktif</th>
                            <th class="p-4 text-center">Selesai</th>
                            <th class="p-4 text-center">Sekolah</th>
                            <th class="p-4 text-center">Awam</th>
                            <th class="p-4 text-center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody id="tableAdminStats" class="divide-y text-slate-600">
                        <tr><td colspan="6" class="p-8 text-center text-slate-400 italic">Memuatkan statistik...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div><!-- end main -->

<!-- Poster Lightbox -->
<div id="posterLightbox" onclick="tutupLightbox()" style="display:none"
    class="fixed inset-0 z-[200] bg-black/80 backdrop-blur-sm flex items-center justify-center p-6 cursor-zoom-out">
    <div class="relative max-w-2xl w-full" onclick="event.stopPropagation()">
        <button onclick="tutupLightbox()" class="absolute -top-4 -right-4 bg-white text-slate-700 w-9 h-9 rounded-full flex items-center justify-center shadow-lg text-lg hover:bg-yellow-50 z-10">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <img id="lightboxImg" src="" alt="Poster" class="w-full rounded-2xl shadow-2xl object-contain max-h-[80vh]">
        <p id="lightboxCaption" class="text-center text-white text-xs mt-3 font-semibold opacity-80"></p>
    </div>
</div>

<script>
var IS_SUPER_ADMIN = <?= $isSuperAdmin ? 'true' : 'false' ?>;
var masterData = { sekolah: [], orangAwam: [] };
var programCache = [];
var accountCache = { school: [], public: [], admins: [] };

// ============================================================
// UTILITY
// ============================================================
function getTodayDate() { return new Date().toISOString().slice(0, 10); }
function escapeHtml(v) {
    return String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
}
function escapeJs(v) { return String(v ?? '').replace(/\\/g,'\\\\').replace(/'/g,"\\'"); }
function formatTarikh(d) {
    if (!d) return '—';
    return new Date(d + 'T00:00:00').toLocaleDateString('ms-MY', { day:'2-digit', month:'short', year:'numeric' });
}
function baseUrl(p) { return '<?= base_url() ?>' + p; }

// ============================================================
// TAB NAVIGATION
// ============================================================
function tukarTab(tabId, btn) {
    if (!document.getElementById('tab-' + tabId)) tabId = 'daftar';
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById('tab-' + tabId).classList.add('active');
    if (!btn) btn = document.querySelector('.nav-btn[onclick*="' + tabId + '"]');
    document.querySelectorAll('.nav-btn').forEach(b => {
        b.classList.remove('active-nav');
        b.classList.add('text-yellow-100','hover:bg-white/10');
    });
    if (btn) { btn.classList.add('active-nav'); btn.classList.remove('text-yellow-100','hover:bg-white/10'); }
    if (['sekolah','awam'].includes(tabId)) muatDataLive();
    if (tabId === 'akaun') muatAkaun(false);
    if (tabId === 'stats') muatStats();
    if (tabId === 'program-stats') muatStatistikProgram();
    if (tabId === 'daftar') setTimeout(muatSenaraiProgram, 300);
    localStorage.setItem('adminDashboardTab', tabId);
    var url = new URL(window.location.href);
    url.searchParams.set('tab', tabId);
    history.replaceState(null, '', url.toString());
}

window.onload = function () {
    tetapkanTarikhMinimum();
    var params = new URLSearchParams(window.location.search);
    var tab = params.get('tab') || localStorage.getItem('adminDashboardTab') || 'daftar';
    var valid = ['daftar','sekolah','awam','akaun','stats','program-stats'];
    if (!valid.includes(tab)) tab = 'daftar';
    tukarTab(tab);
    muatSenaraiProgram().catch(err => console.error(err));
    muatAkaun(false);
};

// ============================================================
// STATUS / DATE HELPERS
// ============================================================
var fpStartDate, fpEndDate, fpEventTime;

function tetapkanTarikhMinimum() {
    fpStartDate = flatpickr('#startDate', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true,
        onChange: kemasKiniStatusPreview
    });
    fpEndDate = flatpickr('#endDate', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true,
        onChange: kemasKiniStatusPreview
    });
    fpEventTime = flatpickr('#eventTime', {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        altInput: true,
        altFormat: 'h:i K',
        time_24hr: false,
        minuteIncrement: 5,
        allowInput: true
    });
}

function kemasKiniStatusPreview() {
    var start   = document.getElementById('startDate').value;
    var end     = document.getElementById('endDate').value;
    var preview = document.getElementById('programStatusPreview');
    if (!start || !end) { preview.textContent = 'AUTO'; preview.className = 'w-full p-3 border border-slate-200 rounded-xl text-[10px] text-center font-black bg-slate-50 text-slate-500 uppercase'; return; }
    if (end < start)    { preview.textContent = 'RALAT'; preview.className = 'w-full p-3 border border-red-200 rounded-xl text-[10px] text-center font-black bg-red-50 text-red-600 uppercase'; return; }
    var today = getTodayDate();
    var status = (end < today) ? 'TIDAK AKTIF' : 'AKTIF';
    preview.textContent = status;
    preview.className = status === 'AKTIF'
        ? 'w-full p-3 border border-yellow-200 rounded-xl text-[10px] text-center font-black bg-yellow-50 text-[#8a0028] uppercase'
        : 'w-full p-3 border border-slate-200 rounded-xl text-[10px] text-center font-black bg-slate-100 text-slate-600 uppercase';
}

// ============================================================
// PROGRAM FUNCTIONS
// ============================================================
async function muatSenaraiProgram() {
    const res  = await fetch('<?= base_url('admin/programs') ?>?t=' + Date.now(), { cache: 'no-store' });
    const rawText = await res.text();
    console.log('[DEBUG programs] status=' + res.status + ' body=' + rawText.substring(0, 500));
    let list;
    try { list = JSON.parse(rawText); } catch(e) { console.error('[DEBUG] JSON parse error:', e, rawText.substring(0,300)); return; }
    if (!res.ok || !Array.isArray(list)) { console.error('[DEBUG] Not array or not ok:', list); return; }
    programCache = list;
    console.log('[DEBUG] programs count:', list.length);

    var dropMain = document.getElementById('filterProgramMain');
    var selectedMain = dropMain.value || 'SEMUA';
    dropMain.innerHTML = '<option value="SEMUA">-- SEMUA PROGRAM --</option>';
    list.filter(p => !p.parent_id || p.parent_id === 0 || p.parent_id === null).forEach(p => {
        var o = document.createElement('option');
        var statusLabel = String(p.status||'').toUpperCase() === 'AKTIF' ? '' : ' (Tidak Aktif)';
        o.value = p.nama; o.dataset.dbId = p.db_id; o.textContent = p.nama + statusLabel; dropMain.appendChild(o);
    });
    if ([...dropMain.options].some(o => o.value === selectedMain)) dropMain.value = selectedMain;
    binaFilterSubProgram(list);

    var parentDrop = document.getElementById('parentProgramSelect');
    parentDrop.innerHTML = '<option value="">-- Pilih Program Induk --</option>';
    list.filter(p => !p.parent_id || p.parent_id === null || p.parent_id === 0).forEach(p => {
        var o = document.createElement('option');
        o.value = p.kod || p.id; o.textContent = (p.kod||p.id) + ' — ' + p.nama; parentDrop.appendChild(o);
    });

    binaSenaraProgram(list);
}

// Builds/refreshes the Sub Program dropdown based on the currently selected Main Program.
// Shows the dropdown only when the selected main program actually has sub programs.
function binaFilterSubProgram(list) {
    var dropMain = document.getElementById('filterProgramMain');
    var subWrap  = document.getElementById('filterSubWrap');
    var dropSub  = document.getElementById('filterProgramSub');
    var selectedSub = dropSub.value || 'SEMUA';

    var mainOpt = dropMain.options[dropMain.selectedIndex];
    var mainDbId = mainOpt ? mainOpt.dataset.dbId : null;

    var subs = (mainOpt && mainOpt.value !== 'SEMUA' && mainDbId)
        ? list.filter(p => p.parent_id && String(p.parent_id) === String(mainDbId))
        : [];

    dropSub.innerHTML = '<option value="SEMUA">-- SEMUA SUB PROGRAM --</option>';
    subs.forEach(p => {
        var o = document.createElement('option');
        var statusLabel = String(p.status||'').toUpperCase() === 'AKTIF' ? '' : ' (Tidak Aktif)';
        o.value = p.nama; o.textContent = p.nama + statusLabel; dropSub.appendChild(o);
    });

    if (subs.length) {
        subWrap.classList.remove('hidden');
        if ([...dropSub.options].some(o => o.value === selectedSub)) dropSub.value = selectedSub;
    } else {
        subWrap.classList.add('hidden');
        dropSub.value = 'SEMUA';
    }
}

// Called when the Main Program dropdown changes: rebuild the Sub dropdown then re-filter the data.
function bilaTukarFilterUtama() {
    binaFilterSubProgram(programCache);
    tapisSemuaData();
}

function setProgramType(type) {
    var form = document.getElementById('programForm');
    form.dataset.type = type;
    var btnU = document.getElementById('btnTypeUtama');
    var btnS = document.getElementById('btnTypeSub');
    var row  = document.getElementById('parentProgramRow');
    var title = document.getElementById('programFormTitle');
    if (type === 'utama') {
        btnU.className = 'flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-[#8a0028] bg-[#8a0028] text-white shadow transition-all';
        btnS.className = 'flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-[#8a0028] text-[#8a0028] bg-white hover:bg-yellow-50 transition-all';
        row.classList.add('hidden');
        title.innerHTML = '<i class="fa-solid fa-star text-[#8a0028]"></i> Daftar Program Utama Baharu';
    } else {
        btnS.className = 'flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-blue-600 bg-blue-600 text-white shadow transition-all';
        btnU.className = 'flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold border-2 border-[#8a0028] text-[#8a0028] bg-white hover:bg-yellow-50 transition-all';
        row.classList.remove('hidden');
        title.innerHTML = '<i class="fa-solid fa-sitemap text-blue-600"></i> Daftar Sub Program Baharu';
    }
}

function binaSenaraProgram(list) {
    var tbody = document.getElementById('tableProgramSenarai');
    var countEl = document.getElementById('programCount');
    var mains = list.filter(p => !p.parent_id || p.parent_id === 0 || p.parent_id === null);
    var subs  = list.filter(p => p.parent_id && p.parent_id !== 0 && p.parent_id !== null);
    countEl.textContent = list.length + ' program (' + mains.length + ' utama, ' + subs.length + ' sub)';

    if (!list.length) { tbody.innerHTML = '<tr><td colspan="9" class="p-8 text-center text-slate-400 italic">Tiada program.</td></tr>'; return; }

    tbody.innerHTML = '';
    var rowNum = 0;
    mains.sort((a,b) => (a.mula||'').localeCompare(b.mula||''));

    mains.forEach(function(p) {
        rowNum++;
        renderProgramRow(tbody, p, rowNum, false);
        subs.filter(s => s.parent_id == p.db_id).forEach(function(s) {
            rowNum++;
            renderProgramRow(tbody, s, rowNum, true);
        });
    });
}

function renderProgramRow(tbody, p, num, isSub) {
    var status  = String(p.status||'').toUpperCase();
    var isAktif = status === 'AKTIF';
    var statusHtml = isAktif
        ? '<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase">AKTIF</span>'
        : '<span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[10px] font-bold uppercase">TIDAK AKTIF</span>';

    var limit = parseInt(p.registration_limit || 0);
    var used  = parseInt(p.used_capacity || 0);
    var remaining = p.remaining_capacity;
    var capHtml;
    if (limit <= 0) {
        capHtml = '<span class="text-slate-400 text-[10px]">Tiada had</span>';
    } else {
        var pct = Math.min(100, Math.round((used / limit) * 100));
        var barColor = pct >= 100 ? 'bg-red-500' : pct >= 75 ? 'bg-amber-400' : 'bg-green-500';
        capHtml = `<div class="min-w-[100px]">
            <div class="flex justify-between text-[10px] mb-1">
                <span class="font-bold">${used}/${limit}</span>
                <span class="${pct >= 100 ? 'text-red-600 font-bold' : 'text-slate-500'}">${remaining !== null && remaining !== undefined ? remaining + ' baki' : ''}</span>
            </div>
            <div class="capacity-bar"><div class="capacity-fill ${barColor}" style="width:${pct}%"></div></div>
        </div>`;
    }

    var kod  = escapeHtml(p.kod || p.id || '—');
    var nama = escapeHtml(p.nama || '—');
    var mula = formatTarikh(p.mula || p.start_date);
    var tamat = formatTarikh(p.tamat || p.end_date);
    var isFeatured = p.is_featured == 1 || p.is_featured === true;
    var jenisHtml = isSub
        ? '<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold">SUB</span>'
        : '<span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-[10px] font-bold">UTAMA</span>'
        + (isFeatured ? ' <span class="bg-yellow-400 text-[#520018] px-2 py-0.5 rounded text-[10px] font-black ml-1"><i class=\"fa-solid fa-star text-[9px]\"></i> PILIHAN</span>' : '');

    var posterHtml;
    if (p.poster_image) {
        var posterUrl = baseUrl(p.poster_image);
        posterHtml = `<button type="button" onclick="bukaLightbox('${escapeJs(posterUrl)}','${escapeJs(p.nama||p.id)}')"
            class="block mx-auto w-12 h-12 rounded-lg overflow-hidden border border-slate-200 hover:border-[#8a0028] hover:shadow-md transition-all" title="Lihat Poster">
            <img src="${posterUrl}" alt="Poster" class="w-full h-full object-cover">
        </button>`;
    } else {
        posterHtml = '<div class="w-12 h-12 mx-auto rounded-lg bg-slate-100 flex items-center justify-center text-slate-300"><i class="fa-solid fa-image text-lg"></i></div>';
    }

    var tr = document.createElement('tr');
    tr.className = 'hover:bg-slate-50 transition-all' + (isSub ? ' bg-blue-50/30' : '');
    tr.innerHTML = `
        <td class="p-4 text-slate-400 font-mono">${num}</td>
        <td class="p-4 text-center">${posterHtml}</td>
        <td class="p-4">${jenisHtml}</td>
        <td class="p-4 font-black text-[#8a0028] whitespace-nowrap">${isSub ? '<span class="text-slate-300 mr-1">└</span>' : ''}${kod}</td>
        <td class="p-4 font-semibold text-slate-800">${nama}</td>
        <td class="p-4 text-slate-500 whitespace-nowrap">${mula} – ${tamat}</td>
        <td class="p-4 text-center">${statusHtml}</td>
        <td class="p-4 text-center">${capHtml}</td>
        <td class="p-4">
            <div class="flex justify-center gap-2">
                <button type="button" onclick="mulaEditProgram('${escapeJs(p.kod||p.id)}')"
                    class="bg-yellow-100 text-[#8a0028] w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-yellow-200 transition-all" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                <button type="button" onclick="tambahSubProgram('${escapeJs(p.kod||p.id)}','${escapeJs(p.nama)}')"
                    class="bg-blue-100 text-blue-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-blue-200 transition-all" title="Tambah sub program"><i class="fa-solid fa-sitemap"></i></button>
                <button type="button" onclick="padamProgram('${escapeJs(p.kod||p.id)}','${escapeJs(p.nama)}')"
                    class="bg-red-100 text-red-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-red-200 transition-all" title="Padam"><i class="fa-solid fa-trash"></i></button>
            </div>
        </td>`;
    tbody.appendChild(tr);
}

function tambahSubProgram(parentKod, parentNama) {
    setProgramType('sub');
    var parentDrop = document.getElementById('parentProgramSelect');
    if ([...parentDrop.options].some(o => o.value === parentKod)) parentDrop.value = parentKod;
    document.getElementById('programForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

async function daftarProgram(event) {
    event.preventDefault();
    var form = document.getElementById('programForm');
    var btn  = document.getElementById('btnDaftarProgram');
    var programCode = document.getElementById('programCode').value.trim().toUpperCase();
    var programName = document.getElementById('programName').value.trim();
    var startDate   = document.getElementById('startDate').value;
    var endDate     = document.getElementById('endDate').value;

    if (!programCode || !programName || !startDate || !endDate) {
        Swal.fire({ icon: 'warning', title: 'Maklumat tidak lengkap', text: 'Sila isi semua medan wajib.' }); return;
    }
    if (endDate < startDate) {
        Swal.fire({ icon: 'warning', title: 'Tarikh tidak sah', text: 'Tarikh tamat mesti sama atau selepas tarikh mula.' }); return;
    }

    var programType = form.dataset.type || 'utama';
    var parentCode  = document.getElementById('parentProgramSelect').value.trim();
    if (programType === 'sub' && !parentCode && form.dataset.mode !== 'edit') {
        Swal.fire({ icon: 'warning', title: 'Program induk diperlukan', text: 'Sila pilih program induk.' }); return;
    }

    btn.disabled = true; btn.classList.add('opacity-60','cursor-not-allowed');

    try {
        var isEdit = form.dataset.mode === 'edit';
        var originalCode = form.dataset.originalCode || programCode;
        var body = new FormData();
        body.append('program_code',       programCode);
        body.append('program_name',       programName);
        body.append('start_date',         startDate);
        body.append('end_date',           endDate);
        body.append('event_time',         document.getElementById('eventTime').value);
        body.append('organizer',          document.getElementById('organizer').value.trim());
        body.append('pic_nama',           document.getElementById('picNama').value.trim());
        body.append('pic_tel',            document.getElementById('picTel').value.trim());
        body.append('location',           document.getElementById('programLocation').value.trim());
        body.append('description',        document.getElementById('programDescription').value.trim());
        body.append('registration_limit', document.getElementById('regLimit').value || 0);
        body.append('is_featured', document.getElementById('isFeatured').checked ? 1 : 0);
        var posterFile = document.getElementById('programPoster').files[0];
        if (posterFile) body.append('poster_image', posterFile);

        var url;
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
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Gagal menyimpan program.' });
        }
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Ralat', text: err.message || 'Gagal menghantar data.' });
    } finally {
        btn.disabled = false; btn.classList.remove('opacity-60','cursor-not-allowed');
    }
}

function mulaEditProgram(programCode) {
    var program = programCache.find(p => (p.kod || p.id || p.program_code) === programCode);
    if (!program) { Swal.fire({ icon: 'error', title: 'Program tidak ditemui' }); return; }
    var form = document.getElementById('programForm');
    form.dataset.mode = 'edit';
    form.dataset.originalCode = programCode;
    var isSub = !!program.parent_id && program.parent_id !== 0 && program.parent_id !== null;
    form.dataset.type = isSub ? 'sub' : 'utama';
    document.getElementById('programCode').value         = program.kod || program.id || '';
    document.getElementById('programName').value         = program.nama || '';
    if (fpStartDate) fpStartDate.setDate(program.mula || program.start_date || '', true);
    if (fpEndDate)   fpEndDate.setDate(program.tamat || program.end_date || '', true);
    if (fpEventTime) fpEventTime.setDate(program.event_time || '', true);
    document.getElementById('organizer').value           = program.organizer || '';
    document.getElementById('picNama').value             = program.pic_nama || '';
    document.getElementById('picTel').value              = program.pic_tel || '';
    document.getElementById('programLocation').value     = program.location || '';
    document.getElementById('programDescription').value  = program.description || '';
    document.getElementById('regLimit').value            = program.registration_limit || 0;
    document.getElementById('isFeatured').checked          = (program.is_featured == 1 || program.is_featured === true);
    if (program.poster_image) {
        document.getElementById('posterPreviewImg').src = baseUrl(program.poster_image);
        document.getElementById('posterPreviewBox').classList.remove('hidden');
        var lbl = document.getElementById('posterFileName');
        lbl.textContent = 'Poster sedia ada (pilih fail baru untuk ganti)';
        lbl.classList.remove('hidden');
    }
    document.getElementById('programTypeToggle').style.display = 'none';
    document.getElementById('parentProgramRow').classList.add('hidden');
    document.getElementById('programFormTitle').innerHTML = '<i class="fa-solid fa-pen-to-square text-[#8a0028]"></i> Edit Program';
    document.getElementById('btnDaftarProgram').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> KEMASKINI PROGRAM';
    document.getElementById('btnBatalEditProgram').style.display = '';
    kemasKiniStatusPreview();
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetProgramForm() {
    var form = document.getElementById('programForm');
    form.reset();
    if (fpStartDate) fpStartDate.clear();
    if (fpEndDate)   fpEndDate.clear();
    if (fpEventTime) fpEventTime.clear();
    form.dataset.mode = 'create'; form.dataset.originalCode = '';
    document.getElementById('btnDaftarProgram').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> SIMPAN PROGRAM';
    document.getElementById('btnBatalEditProgram').style.display = 'none';
    document.getElementById('programTypeToggle').style.display = '';
    document.getElementById('parentProgramSelect').value = '';
    document.getElementById('posterPreviewBox').classList.add('hidden');
    document.getElementById('posterFileName').classList.add('hidden');
    document.getElementById('isFeatured').checked = false;
    setProgramType('utama');
    kemasKiniStatusPreview();
}

async function padamProgram(programCode, programName) {
    var confirm = await Swal.fire({
        icon: 'warning', title: 'Padam program?',
        text: 'Program "' + programName + '" akan dipadam jika tiada rekod pendaftaran.',
        showCancelButton: true, confirmButtonText: 'Ya, padam', cancelButtonText: 'Batal', confirmButtonColor: '#dc2626'
    });
    if (!confirm.isConfirmed) return;
    try {
        const res    = await fetch('<?= base_url('admin/programs/delete') ?>/' + encodeURIComponent(programCode), { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' } });
        const result = await res.json();
        if (result.success) { await muatSenaraiProgram(); Swal.fire({ icon: 'success', title: 'Berjaya', text: result.message, timer: 1600, showConfirmButton: false }); }
        else Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Gagal memadam.' });
    } catch (err) { Swal.fire({ icon: 'error', title: 'Ralat', text: err.message || 'Gagal memadam program.' }); }
}

function pratonton_poster(input) {
    var box = document.getElementById('posterPreviewBox');
    var img = document.getElementById('posterPreviewImg');
    var lbl = document.getElementById('posterFileName');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; box.classList.remove('hidden'); };
        reader.readAsDataURL(input.files[0]);
        lbl.textContent = input.files[0].name; lbl.classList.remove('hidden');
    }
}

// ============================================================
// DATA TABLES
// ============================================================
async function muatDataLive() {
    try {
        const res    = await fetch('<?= base_url('admin/data') ?>?t=' + Date.now(), { cache: 'no-store' });
        const result = await res.json();
        if (!result.success) throw new Error(result.message || 'Gagal memuatkan data');
        masterData = {
            sekolah:   Array.isArray(result.sekolah)   ? result.sekolah   : [],
            orangAwam: Array.isArray(result.orangAwam) ? result.orangAwam : [],
        };
        tapisSemuaData();
    } catch (err) { console.error('muatDataLive:', err); }
}

function tapisSemuaData() {
    var mainVal = (document.getElementById('filterProgramMain').value || 'SEMUA').trim();
    var subVal  = (document.getElementById('filterProgramSub').value || 'SEMUA').trim();

    var namaSenarai = null; // null = no restriction (SEMUA)
    if (subVal !== 'SEMUA') {
        // Specific sub program chosen
        namaSenarai = [subVal];
    } else if (mainVal !== 'SEMUA') {
        // Main program chosen, sub still on SEMUA -> include the main program itself
        // PLUS all of its sub programs (if any), so nothing gets hidden.
        namaSenarai = [mainVal];
        var mainOpt = [...document.getElementById('filterProgramMain').options].find(o => o.value === mainVal);
        var mainDbId = mainOpt ? mainOpt.dataset.dbId : null;
        if (mainDbId) {
            programCache.filter(p => p.parent_id && String(p.parent_id) === String(mainDbId))
                .forEach(p => namaSenarai.push(p.nama));
        }
    }

    var sekolah = masterData.sekolah.filter(r  => !namaSenarai || namaSenarai.includes(r.program));
    var awam    = masterData.orangAwam.filter(r => !namaSenarai || namaSenarai.includes(r.program));

    document.getElementById('statSekolah').textContent = sekolah.length;
    document.getElementById('statAwam').textContent    = awam.length;

    renderSekolah(sekolah);
    renderAwam(awam);
}

function renderSekolah(rows) {
    var tbody = document.getElementById('tableSekolah');
    if (!rows.length) { tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-slate-400 italic">Tiada rekod.</td></tr>'; return; }
    tbody.innerHTML = rows.map(function(r) {
        var guruList = Array.isArray(r.guru) ? r.guru : [];
        var guruHtml = guruList.length
            ? guruList.map(g => `<div class="text-[10px]"><b>${escapeHtml(g.nama_guru)}</b> <span class="text-slate-400">(${escapeHtml(g.ic_guru)})</span></div>`).join('')
            : '<span class="text-slate-400">—</span>';
        return `<tr class="hover:bg-slate-50">
            <td class="p-4 text-slate-500 whitespace-nowrap">${escapeHtml(r.timestamp)}</td>
            <td class="p-4 font-semibold">${escapeHtml(r.program)}</td>
            <td class="p-4">${escapeHtml(r.namaSekolah)}</td>
            <td class="p-4 font-mono text-[#8a0028]">${escapeHtml(r.kodSekolah)}</td>
            <td class="p-4">${guruHtml}</td>
            <td class="p-4 text-slate-500"><div>${escapeHtml(r.email||'—')}</div><div>${escapeHtml(r.tel||'—')}</div></td>
            <td class="p-4 text-center font-black text-[#8a0028]">${bilMuridCell(r)}</td>
        </tr>`;
    }).join('');
}

function bilMuridCell(r) {
    var n = parseInt(r.bilMurid, 10) || 0;
    if (n <= 0) return '0';
    return '<button type="button" onclick="tunjukMurid(' + Number(r.id) + ',\'' + escapeJs(r.namaSekolah || '') + '\')" ' +
        'class="count-link font-black text-[#8a0028] bg-transparent border-0 p-0 hover:text-[#520018]" title="Lihat senarai murid">' + n + '</button>';
}

function bilAhliCell(r) {
    var n = parseInt(r.bilAhli, 10) || 0;
    if (n <= 0) return '0';
    return '<button type="button" onclick="tunjukAhliKeluarga(' + Number(r.id) + ',\'' + escapeJs(r.nama || '') + '\')" ' +
        'class="count-link font-bold text-[#8a0028] bg-transparent border-0 p-0 hover:text-[#520018]" title="Lihat senarai ahli keluarga">' + n + '</button>';
}

function binaJadualPeserta(title, subtitle, rows, emptyMsg) {
    if (!rows.length) {
        return '<p class="text-sm text-slate-500 text-center py-4">' + escapeHtml(emptyMsg) + '</p>';
    }
    return '<div class="text-left">' +
        (subtitle ? '<p class="text-xs text-slate-500 mb-3">' + escapeHtml(subtitle) + '</p>' : '') +
        '<table class="w-full text-xs border border-slate-200 rounded-lg overflow-hidden">' +
        '<thead class="bg-slate-100 text-slate-600 uppercase font-bold">' +
        '<tr><th class="p-2 text-left">Nama</th><th class="p-2 text-left">IC / MyKid</th></tr></thead>' +
        '<tbody class="divide-y">' +
        rows.map(function(row, i) {
            return '<tr class="hover:bg-slate-50"><td class="p-2 font-semibold">' + escapeHtml(row.nama) +
                '</td><td class="p-2 font-mono text-slate-600">' + escapeHtml(row.ic) + '</td></tr>';
        }).join('') +
        '</tbody></table></div>';
}

async function tunjukMurid(registrationId, schoolName) {
    Swal.fire({ title: 'Memuatkan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const res    = await fetch('<?= base_url('admin/data/students') ?>/' + registrationId + '?t=' + Date.now(), { cache: 'no-store' });
        const result = await res.json();
        Swal.close();
        if (!result.success) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Gagal memuatkan senarai murid.' });
            return;
        }
        var subtitle = (result.program || '') + (schoolName ? ' — ' + schoolName : '');
        Swal.fire({
            icon: 'info',
            title: 'Senarai Murid',
            html: binaJadualPeserta('Senarai Murid', subtitle, result.students || [], 'Tiada murid didaftarkan.'),
            width: '520px',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#8a0028',
        });
    } catch (err) {
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Ralat', text: err.message });
    }
}

async function tunjukAhliKeluarga(registrationId, registrantName) {
    Swal.fire({ title: 'Memuatkan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const res    = await fetch('<?= base_url('admin/data/family') ?>/' + registrationId + '?t=' + Date.now(), { cache: 'no-store' });
        const result = await res.json();
        Swal.close();
        if (!result.success) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Gagal memuatkan senarai ahli keluarga.' });
            return;
        }
        var subtitle = (result.program || '') + (registrantName ? ' — ' + registrantName : '');
        Swal.fire({
            icon: 'info',
            title: 'Senarai Ahli Keluarga',
            html: binaJadualPeserta('Senarai Ahli Keluarga', subtitle, result.members || [], 'Tiada ahli keluarga didaftarkan.'),
            width: '520px',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#8a0028',
        });
    } catch (err) {
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Ralat', text: err.message });
    }
}

function renderAwam(rows) {
    var tbody = document.getElementById('tableAwam');
    if (!rows.length) { tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-slate-400 italic">Tiada rekod.</td></tr>'; return; }
    tbody.innerHTML = rows.map(r => `<tr class="hover:bg-slate-50">
        <td class="p-4 text-slate-500 whitespace-nowrap">${escapeHtml(r.timestamp)}</td>
        <td class="p-4 font-semibold">${escapeHtml(r.program)}</td>
        <td class="p-4">${escapeHtml(r.nama)}</td>
        <td class="p-4 font-mono">${escapeHtml(r.ic)}</td>
        <td class="p-4">${escapeHtml(r.tel)}</td>
        <td class="p-4">${escapeHtml(r.email)}</td>
        <td class="p-4 text-center font-bold">${bilAhliCell(r)}</td>
    </tr>`).join('');
}

// ============================================================
// ACCOUNTS
// ============================================================
async function muatAkaun(showLoading = true) {
    if (showLoading) Swal.fire({ title: 'Memuatkan akaun...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const res    = await fetch('<?= base_url('admin/accounts') ?>?t=' + Date.now(), { cache: 'no-store' });
        const result = await res.json();
        if (showLoading) Swal.close();
        if (!result.success) throw new Error(result.message || 'Gagal');
        accountCache = {
            school: Array.isArray(result.school)  ? result.school  : [],
            public: Array.isArray(result.public)  ? result.public  : [],
            admins: Array.isArray(result.admins)  ? result.admins  : [],
        };
        binaJadualAkaun();
    } catch (err) {
        if (showLoading) { Swal.close(); Swal.fire({ icon: 'error', title: 'Ralat', text: err.message }); }
    }
}

function binaJadualAkaun() {
    // Admin accounts (super admin only)
    var adminBody  = document.getElementById('tableAdminAccounts');
    var adminCount = document.getElementById('adminAccountCount');
    if (adminBody) {
        adminCount.textContent = accountCache.admins.length + ' akaun';
        if (!accountCache.admins.length) {
            adminBody.innerHTML = '<tr><td colspan="5" class="p-8 text-center text-slate-400 italic">Tiada akaun admin.</td></tr>';
        } else {
            adminBody.innerHTML = accountCache.admins.map(a => `<tr class="hover:bg-slate-50">
                <td class="p-4 font-black text-[#8a0028]">${escapeHtml(a.username)}</td>
                <td class="p-4 font-semibold">${escapeHtml(a.name)}</td>
                <td class="p-4 text-slate-500">${escapeHtml(a.email||'—')}</td>
                <td class="p-4 text-center">${a.is_active ? '<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px] font-bold">Aktif</span>' : '<span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-[10px] font-bold">Tidak Aktif</span>'}</td>
                <td class="p-4"><div class="flex justify-center gap-2">
                    <button onclick="bukaBorangAkaun('admin',${Number(a.id)})" class="bg-yellow-100 text-[#8a0028] w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-yellow-200" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button onclick="padamAkaun('admin',${Number(a.id)})" class="bg-red-100 text-red-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-red-200" title="Padam"><i class="fa-solid fa-trash"></i></button>
                </div></td>
            </tr>`).join('');
        }
    }

    // School accounts
    document.getElementById('schoolAccountCount').textContent = accountCache.school.length + ' akaun';
    var schoolBody = document.getElementById('tableSchoolAccounts');
    if (!accountCache.school.length) { schoolBody.innerHTML = '<tr><td colspan="4" class="p-8 text-center text-slate-400 italic">Tiada akaun sekolah.</td></tr>'; }
    else schoolBody.innerHTML = accountCache.school.map(a => `<tr class="hover:bg-slate-50">
        <td class="p-4 font-black text-[#8a0028] uppercase">${escapeHtml(a.school_code)}</td>
        <td class="p-4 font-semibold">${escapeHtml(a.school_name)}</td>
        <td class="p-4 text-slate-500">${escapeHtml(a.email||'—')}</td>
        <td class="p-4"><div class="flex justify-center gap-2">
            <button onclick="bukaBorangAkaun('school',${Number(a.id)})" class="bg-yellow-100 text-[#8a0028] w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-yellow-200" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
            <button onclick="padamAkaun('school',${Number(a.id)})" class="bg-red-100 text-red-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-red-200" title="Padam"><i class="fa-solid fa-trash"></i></button>
        </div></td>
    </tr>`).join('');

    // Public accounts
    document.getElementById('publicAccountCount').textContent = accountCache.public.length + ' akaun';
    var publicBody = document.getElementById('tablePublicAccounts');
    if (!accountCache.public.length) { publicBody.innerHTML = '<tr><td colspan="3" class="p-8 text-center text-slate-400 italic">Tiada akaun awam.</td></tr>'; }
    else publicBody.innerHTML = accountCache.public.map(a => `<tr class="hover:bg-slate-50">
        <td class="p-4 font-semibold">${escapeHtml(a.name)}</td>
        <td class="p-4 text-slate-500">${escapeHtml(a.email)}</td>
        <td class="p-4"><div class="flex justify-center gap-2">
            <button onclick="bukaBorangAkaun('public',${Number(a.id)})" class="bg-yellow-100 text-[#8a0028] w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-yellow-200" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
            <button onclick="padamAkaun('public',${Number(a.id)})" class="bg-red-100 text-red-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-red-200" title="Padam"><i class="fa-solid fa-trash"></i></button>
        </div></td>
    </tr>`).join('');
}

function cariAkaun(type, id) {
    var list = type === 'admin' ? accountCache.admins : (type === 'school' ? accountCache.school : accountCache.public);
    return list.find(a => Number(a.id) === Number(id)) || null;
}

async function bukaBorangAkaun(type, id = null) {
    var isEdit  = id !== null;
    var account = isEdit ? cariAkaun(type, id) : null;
    if (isEdit && !account) { Swal.fire({ icon: 'error', title: 'Akaun tidak ditemui' }); return; }

    var title, html;
    if (type === 'admin') {
        title = (isEdit ? 'Kemaskini ' : 'Tambah ') + 'Akaun Admin';
        html = `<div class="text-left space-y-3">
            <input id="swalUsername" class="swal2-input" style="width:100%;margin:0;" placeholder="Username" value="${escapeHtml(account ? account.username : '')}">
            <input id="swalAdminName" class="swal2-input" style="width:100%;margin:0;" placeholder="Nama Penuh" value="${escapeHtml(account ? account.name : '')}">
            <input id="swalAdminEmail" class="swal2-input" style="width:100%;margin:0;" type="email" placeholder="Emel" value="${escapeHtml(account ? (account.email||'') : '')}">
            <input id="swalAdminPass" class="swal2-input" style="width:100%;margin:0;" type="password" placeholder="${isEdit ? 'Kata laluan baharu (kosong = tidak ubah)' : 'Kata laluan *'}">
            ${isEdit ? `<label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" id="swalIsActive" ${account.is_active ? 'checked' : ''}> Akaun Aktif</label>` : ''}
        </div>`;
    } else if (type === 'school') {
        title = (isEdit ? 'Kemaskini ' : 'Tambah ') + 'Akaun Sekolah';
        html = `<div class="text-left space-y-3">
            <input id="swalSchoolCode" class="swal2-input" style="width:100%;margin:0;" placeholder="Kod sekolah" value="${escapeHtml(account ? account.school_code : '')}">
            <input id="swalSchoolName" class="swal2-input" style="width:100%;margin:0;" placeholder="Nama sekolah" value="${escapeHtml(account ? account.school_name : '')}">
            <input id="swalAccountEmail" class="swal2-input" style="width:100%;margin:0;" type="email" placeholder="Emel" value="${escapeHtml(account ? (account.email||'') : '')}">
            <input id="swalAccountPassword" class="swal2-input" style="width:100%;margin:0;" type="password" placeholder="${isEdit ? 'Kata laluan baharu (kosong = tidak ubah)' : 'Kata laluan *'}">
        </div>`;
    } else {
        title = (isEdit ? 'Kemaskini ' : 'Tambah ') + 'Akaun Awam';
        html = `<div class="text-left space-y-3">
            <input id="swalPublicName" class="swal2-input" style="width:100%;margin:0;" placeholder="Nama penuh" value="${escapeHtml(account ? account.name : '')}">
            <input id="swalAccountEmail" class="swal2-input" style="width:100%;margin:0;" type="email" placeholder="Emel" value="${escapeHtml(account ? account.email : '')}">
            <input id="swalAccountPassword" class="swal2-input" style="width:100%;margin:0;" type="password" placeholder="${isEdit ? 'Kata laluan baharu (kosong = tidak ubah)' : 'Kata laluan *'}">
        </div>`;
    }

    Swal.fire({
        title, html, showCancelButton: true,
        confirmButtonText: isEdit ? 'Kemaskini' : 'Cipta',
        cancelButtonText: 'Batal', confirmButtonColor: '#8a0028', width: '480px',
        preConfirm: function() {
            var body = new FormData();
            if (type === 'admin') {
                body.append('username', document.getElementById('swalUsername').value.trim());
                body.append('name',     document.getElementById('swalAdminName').value.trim());
                body.append('email',    document.getElementById('swalAdminEmail').value.trim());
                body.append('password', document.getElementById('swalAdminPass').value.trim());
                if (isEdit) body.append('is_active', document.getElementById('swalIsActive').checked ? 1 : 0);
                if (!document.getElementById('swalUsername').value.trim() || !document.getElementById('swalAdminName').value.trim()) {
                    Swal.showValidationMessage('Username dan nama diperlukan.'); return false;
                }
            } else if (type === 'school') {
                body.append('school_code', document.getElementById('swalSchoolCode').value.trim());
                body.append('school_name', document.getElementById('swalSchoolName').value.trim());
                body.append('email',       document.getElementById('swalAccountEmail').value.trim());
                body.append('password',    document.getElementById('swalAccountPassword').value.trim());
                if (!document.getElementById('swalSchoolCode').value.trim()) { Swal.showValidationMessage('Kod sekolah diperlukan.'); return false; }
            } else {
                body.append('name',     document.getElementById('swalPublicName').value.trim());
                body.append('email',    document.getElementById('swalAccountEmail').value.trim());
                body.append('password', document.getElementById('swalAccountPassword').value.trim());
                if (!document.getElementById('swalPublicName').value.trim()) { Swal.showValidationMessage('Nama diperlukan.'); return false; }
            }
            return body;
        }
    }).then(async result => {
        if (!result.isConfirmed) return;
        var url = isEdit
            ? '<?= base_url('admin/accounts/update') ?>/' + type + '/' + id
            : '<?= base_url('admin/accounts/create') ?>/' + type;
        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        try {
            const res  = await fetch(url, { method: 'POST', body: result.value });
            const data = await res.json();
            Swal.close();
            if (data.success) { Swal.fire({ icon: 'success', title: 'Berjaya', text: data.message, timer: 1600, showConfirmButton: false }); muatAkaun(false); }
            else Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menyimpan akaun.' });
        } catch (err) { Swal.close(); Swal.fire({ icon: 'error', title: 'Ralat', text: err.message }); }
    });
}

async function padamAkaun(type, id) {
    var confirm = await Swal.fire({ icon: 'warning', title: 'Padam akaun?', text: 'Tindakan ini tidak boleh dibatalkan.',
        showCancelButton: true, confirmButtonText: 'Ya, padam', cancelButtonText: 'Batal', confirmButtonColor: '#dc2626' });
    if (!confirm.isConfirmed) return;
    try {
        const res    = await fetch('<?= base_url('admin/accounts/delete') ?>/' + type + '/' + id, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' } });
        const result = await res.json();
        if (result.success) { Swal.fire({ icon: 'success', title: 'Berjaya', text: result.message, timer: 1600, showConfirmButton: false }); muatAkaun(false); }
        else Swal.fire({ icon: 'error', title: 'Gagal', text: result.message });
    } catch (err) { Swal.fire({ icon: 'error', title: 'Ralat', text: err.message }); }
}

// ============================================================
// POSTER LIGHTBOX
// ============================================================
function bukaLightbox(url, caption) {
    document.getElementById('lightboxImg').src       = url;
    document.getElementById('lightboxCaption').textContent = caption || '';
    document.getElementById('posterLightbox').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function tutupLightbox() {
    document.getElementById('posterLightbox').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') tutupLightbox(); });

// ============================================================
// SUPER ADMIN STATS
// ============================================================
async function muatStats() {
    try {
        const res    = await fetch('<?= base_url('admin/dashboard-stats') ?>?t=' + Date.now(), { cache: 'no-store' });
        const result = await res.json();
        if (!result.success) return;
        var s = result.stats;
        var el = id => document.getElementById(id);
        if (el('statTotalAdmins'))   el('statTotalAdmins').textContent   = (s.admins || []).length;
        if (el('statTotalPrograms')) el('statTotalPrograms').textContent  = s.total_programs || 0;
        if (el('statActivePrograms'))el('statActivePrograms').textContent = s.active_programs || 0;
        if (el('statTotalRegs'))     el('statTotalRegs').textContent      = s.total_registrations || 0;
        if (el('statSekolahRegs'))   el('statSekolahRegs').textContent    = s.sekolah_registrations || 0;
        if (el('statAwamRegs'))      el('statAwamRegs').textContent       = s.awam_registrations || 0;

        var tbody = el('tableAdminStats');
        if (tbody && Array.isArray(s.admins)) {
            if (!s.admins.length) { tbody.innerHTML = '<tr><td colspan="8" class="p-8 text-center text-slate-400 italic">Tiada admin didaftarkan.</td></tr>'; return; }
            tbody.innerHTML = s.admins.map(a => `<tr class="hover:bg-slate-50">
                <td class="p-4 font-semibold">${escapeHtml(a.name)}</td>
                <td class="p-4 text-slate-500 font-mono">${escapeHtml(a.username)}</td>
                <td class="p-4 text-center font-bold text-[#8a0028]">${a.total_programs}</td>
                <td class="p-4 text-center"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px] font-bold">${a.active_programs}</span></td>
                <td class="p-4 text-center"><span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold">${a.completed_programs}</span></td>
                <td class="p-4 text-center"><span class="bg-[#8a0028]/10 text-[#8a0028] px-2 py-0.5 rounded text-[10px] font-bold">${a.sekolah_registrations || 0}</span></td>
                <td class="p-4 text-center"><span class="bg-[#520018]/10 text-[#520018] px-2 py-0.5 rounded text-[10px] font-bold">${a.awam_registrations || 0}</span></td>
                <td class="p-4 text-center font-black text-lg text-[#8a0028]">${a.total_registrations}</td>
            </tr>`).join('');
        }
    } catch (err) { console.error('muatStats:', err); }
}

// ============================================================
// PER-PROGRAM EVENT STATISTICS
// ============================================================
var programStatsCache = [];

async function muatStatistikProgram() {
    try {
        const res    = await fetch('<?= base_url('admin/programs/stats') ?>?t=' + Date.now(), { cache: 'no-store' });
        const result = await res.json();
        if (!result.success) return;

        programStatsCache = result.data || [];
        binaStatistikProgram(programStatsCache);
    } catch (err) { console.error('muatStatistikProgram:', err); }
}

function binaStatistikProgram(list) {
    var tbody   = document.getElementById('tableProgramStats');
    var countEl = document.getElementById('programStatsCount');
    if (!tbody) return;

    countEl.textContent = list.length + ' program';

    var totalParticipants = 0, totalMurid = 0, totalAwam = 0;
    list.forEach(s => {
        totalParticipants += parseInt(s.total_participants || 0);
        totalMurid        += parseInt(s.total_murid || 0);
        totalAwam         += parseInt(s.awam_participants || 0);
    });

    var el = id => document.getElementById(id);
    if (el('psTotalPrograms'))     el('psTotalPrograms').textContent     = list.length;
    if (el('psTotalParticipants')) el('psTotalParticipants').textContent = totalParticipants;
    if (el('psTotalMurid'))        el('psTotalMurid').textContent        = totalMurid;
    if (el('psTotalAwam'))         el('psTotalAwam').textContent         = totalAwam;

    if (!list.length) {
        tbody.innerHTML = '<tr><td colspan="12" class="p-8 text-center text-slate-400 italic">Tiada program untuk dipaparkan.</td></tr>';
        return;
    }

    tbody.innerHTML = list.map(function(s) {
        var limit = parseInt(s.registration_limit || 0);
        var used  = parseInt(s.used_capacity || 0);
        var capHtml;
        if (limit <= 0) {
            capHtml = '<span class="text-slate-400 text-[10px]">' + used + ' / tiada had</span>';
        } else {
            var pct = Math.min(100, Math.round((used / limit) * 100));
            var barColor = pct >= 100 ? 'bg-red-500' : pct >= 75 ? 'bg-amber-400' : 'bg-green-500';
            capHtml = '<div class="min-w-[90px] mx-auto">' +
                '<div class="flex justify-between text-[10px] mb-1"><span class="font-bold">' + used + '/' + limit + '</span>' +
                '<span class="text-slate-500">' + (s.fill_percent != null ? s.fill_percent + '%' : '') + '</span></div>' +
                '<div class="capacity-bar"><div class="capacity-fill ' + barColor + '" style="width:' + pct + '%"></div></div></div>';
        }

        var statusBadge;
        if (s.event_status === 'past') {
            statusBadge = '<span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded text-[10px] font-bold">TAMAT</span>';
        } else if (s.event_status === 'ongoing') {
            statusBadge = '<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px] font-bold">BERLANGSUNG</span>';
        } else {
            statusBadge = '<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold">AKAN DATANG</span>';
        }

        var hadirHtml = '<span class="text-green-700 font-bold">' + (s.awam_hadir || 0) + '</span>' +
            ' / <span class="text-slate-400">' + (s.awam_belum_hadir || 0) + ' belum</span>';

        return '<tr class="hover:bg-slate-50">' +
            '<td class="p-3"><div class="font-black text-[#8a0028] text-[10px] uppercase">' + escapeHtml(s.program_code) + '</div>' +
            '<div class="font-semibold text-slate-800">' + escapeHtml(s.program_name) + '</div></td>' +
            '<td class="p-3 text-slate-500 whitespace-nowrap">' + formatTarikh(s.start_date) + ' – ' + formatTarikh(s.end_date) + '</td>' +
            '<td class="p-3 text-center">' + statusBadge + '</td>' +
            '<td class="p-3 text-center">' + capHtml + '</td>' +
            '<td class="p-3 text-center font-bold">' + (s.sekolah_registrations || 0) + '</td>' +
            '<td class="p-3 text-center font-bold text-blue-700">' + (s.total_murid || 0) + '</td>' +
            '<td class="p-3 text-center">' + (s.guru_pengiring || 0) + '</td>' +
            '<td class="p-3 text-center">' + (s.awam_registrations || 0) + '</td>' +
            '<td class="p-3 text-center font-bold text-[#520018]">' + (s.awam_participants || 0) + '</td>' +
            '<td class="p-3 text-center font-black text-lg text-[#8a0028]">' + (s.total_participants || 0) + '</td>' +
            '<td class="p-3 text-center text-[10px]">' + hadirHtml + '</td>' +
            '<td class="p-3 text-center">' +
            '<button type="button" onclick="eksportStatistikProgram(' + Number(s.program_id) + ')" ' +
            'class="bg-green-100 text-green-700 w-9 h-9 rounded-xl inline-flex items-center justify-center hover:bg-green-200 transition-all" title="Eksport CSV">' +
            '<i class="fa-solid fa-file-csv"></i></button></td></tr>';
    }).join('');
}

function eksportStatistikProgram(programId) {
    var url = '<?= base_url('admin/programs/stats/export') ?>?t=' + Date.now();
    if (programId) url += '&program_id=' + programId;
    window.location.href = url;
}
</script>
<?= view('partials/footer_watermark') ?>
</body>
</html>