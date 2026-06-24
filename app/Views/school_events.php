<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventraZ - Acara</title>
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
        .event-card {
            transition: all 0.3s ease;
            cursor: default;
        }
        .event-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(82, 0, 24, .15);
        }
        .event-poster {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }
        .badge-ongoing { background: #10b981; color: white; }
        .badge-upcoming { background: #3b82f6; color: white; }
        .badge-past { background: #6b7280; color: white; }
        
        .page-section { display: none; }
        .page-section.active { display: block; }
        
        @media (max-width: 900px) {
            body.flex { display: block; }
            .sidebar { position: relative; width: 100%; height: auto; }
            .ml-\[280px\] { margin-left: 0 !important; }
            .grid-cols-3 { grid-template-columns: repeat(1, minmax(0, 1fr)) !important; }
            .grid-cols-2 { grid-template-columns: repeat(1, minmax(0, 1fr)) !important; }
        }
    </style>
</head>
<body class="flex">

    <!-- SIDEBAR -->
    <div class="sidebar p-6 flex flex-col justify-between text-white shadow-2xl z-10">
        <div>
            <div class="mb-8 border-b border-white/15 pb-4 text-center">
                <img src="<?= base_url('assets/eventraz-logo.jpeg') ?>" alt="EventraZ" class="brand-logo mx-auto mb-3">
                <h1 class="text-lg font-black text-white tracking-widest">EventraZ Portal</h1>
                <p class="text-[9px] text-yellow-200 font-bold mt-1">Sekolah Terengganu</p>
                <p class="text-[10px] text-white mt-2 font-semibold"><?= esc(session('school_name')) ?></p>
                <p class="text-[9px] text-yellow-100"><?= esc(session('school_code')) ?></p>
            </div>
            <nav class="space-y-3">
                <a href="<?= base_url('school/portal') ?>" 
                    class="w-full text-left p-4 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-arrow-left"></i> KEMBALI
                </a>
                <button onclick="tunjukSeksyen('semua')" id="menuSemua"
                    class="w-full text-left p-4 text-xs font-bold active-tab flex items-center gap-3 rounded-xl transition-all shadow-md">
                    <i class="fa-solid fa-calendar-days"></i> SEMUA ACARA
                </button>
                <button onclick="tunjukSeksyen('upcoming')" id="menuUpcoming"
                    class="w-full text-left p-4 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-clock"></i> AKAN DATANG
                </button>
                <button onclick="tunjukSeksyen('ongoing')" id="menuOngoing"
                    class="w-full text-left p-4 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-play"></i> SEDANG BERLANGSUNG
                </button>
                <button onclick="tunjukSeksyen('past')" id="menuPast"
                    class="w-full text-left p-4 text-xs font-bold text-yellow-100 hover:bg-white/10 flex items-center gap-3 rounded-xl transition-all">
                    <i class="fa-solid fa-flag-checkered"></i> TELAH TAMAT
                </button>
            </nav>
        </div>
        <a href="<?= base_url('logout') ?>"
            class="text-xs text-yellow-100 font-bold p-3 flex items-center gap-2 hover:text-white hover:bg-white/10 rounded-xl transition-all">
            <i class="fa-solid fa-power-off"></i> LOG KELUAR PORTAL
        </a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="ml-[280px] w-full p-10 min-h-screen">
        
        <!-- Header -->
        <div class="glass-card p-8 rounded-3xl mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black text-[#520018]">📅 Acara & Aktiviti</h1>
                    <p class="text-sm text-slate-400 mt-1">Semua acara dan program yang dianjurkan</p>
                </div>
                <div class="flex gap-2">
                    <span id="totalEvents" class="bg-[#8a0028]/10 text-[#8a0028] px-4 py-2 rounded-xl text-xs font-bold">0 acara</span>
                </div>
            </div>
        </div>

        <!-- Featured Events -->
        <div id="featuredSection" class="mb-10">
            <h2 class="text-lg font-bold text-[#520018] mb-4 flex items-center gap-2">
                <i class="fa-solid fa-star text-yellow-400"></i> Acara Pilihan
            </h2>
            <div id="featuredGrid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <p class="col-span-2 text-center text-slate-400 text-sm py-8">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuatkan...
                </p>
            </div>
        </div>

        <!-- All Events -->
        <div id="seksyenSemua" class="page-section active">
            <h2 class="text-lg font-bold text-[#520018] mb-4">Semua Acara</h2>
            <div id="allEventsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <p class="col-span-3 text-center text-slate-400 text-sm py-8">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuatkan...
                </p>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div id="seksyenUpcoming" class="page-section">
            <h2 class="text-lg font-bold text-[#520018] mb-4 flex items-center gap-2">
                <i class="fa-solid fa-clock text-blue-500"></i> Akan Datang
            </h2>
            <div id="upcomingGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
        </div>

        <!-- Ongoing Events -->
        <div id="seksyenOngoing" class="page-section">
            <h2 class="text-lg font-bold text-[#520018] mb-4 flex items-center gap-2">
                <i class="fa-solid fa-play text-green-500"></i> Sedang Berlangsung
            </h2>
            <div id="ongoingGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
        </div>

        <!-- Past Events -->
        <div id="seksyenPast" class="page-section">
            <h2 class="text-lg font-bold text-[#520018] mb-4 flex items-center gap-2">
                <i class="fa-solid fa-flag-checkered text-gray-500"></i> Telah Tamat
            </h2>
            <div id="pastGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
        </div>
    </div>

    <script>
        // ── Page section switching ──
        function tunjukSeksyen(seksyen) {
            document.querySelectorAll('.page-section').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('nav button').forEach(btn => {
                btn.classList.remove('active-tab');
                btn.classList.add('text-yellow-100', 'hover:bg-white/10');
            });

            var map = {
                'semua': 'seksyenSemua',
                'upcoming': 'seksyenUpcoming',
                'ongoing': 'seksyenOngoing',
                'past': 'seksyenPast'
            };
            
            document.getElementById(map[seksyen]).classList.add('active');
            var btn = document.getElementById('menu' + seksyen.charAt(0).toUpperCase() + seksyen.slice(1));
            if (btn) {
                btn.classList.add('active-tab');
                btn.classList.remove('text-yellow-100', 'hover:bg-white/10');
            }
        }

        // ── Load Events ──
        window.onload = function() {
            muatAcara();
        };

        async function muatAcara() {
            try {
                const res = await fetch('<?= base_url('school/events-data') ?>?t=' + Date.now());
                const result = await res.json();
                
                if (!result.success) {
                    showError('Gagal memuatkan acara');
                    return;
                }
                
                // Update total count
                var total = (result.upcoming?.length || 0) + (result.ongoing?.length || 0) + (result.past?.length || 0);
                document.getElementById('totalEvents').textContent = total + ' acara';
                
                // Render Featured
                renderFeatured(result.featured || []);
                
                // Render All
                var allEvents = [...(result.ongoing || []), ...(result.upcoming || []), ...(result.past || [])];
                renderEvents('allEventsGrid', allEvents);
                
                // Render by category
                renderEvents('upcomingGrid', result.upcoming || []);
                renderEvents('ongoingGrid', result.ongoing || []);
                renderEvents('pastGrid', result.past || []);
                
                // Show message if no events in section
                ['upcomingGrid', 'ongoingGrid', 'pastGrid'].forEach(id => {
                    var grid = document.getElementById(id);
                    if (grid && grid.children.length === 0) {
                        grid.innerHTML = `
                            <div class="col-span-3 text-center py-12">
                                <i class="fa-solid fa-calendar-day text-4xl text-slate-300 mb-3"></i>
                                <p class="text-slate-400 text-sm">Tiada acara dalam kategori ini.</p>
                            </div>`;
                    }
                });
                
            } catch (err) {
                console.error('muatAcara error:', err);
                showError('Ralat memuatkan acara');
            }
        }

        function renderFeatured(events) {
            var grid = document.getElementById('featuredGrid');
            if (!events || events.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-2 text-center py-8">
                        <p class="text-slate-400 text-sm">Tiada acara pilihan buat masa ini.</p>
                    </div>`;
                return;
            }
            
            grid.innerHTML = '';
            events.forEach(event => {
                grid.innerHTML += createEventCard(event, true);
            });
        }

        function renderEvents(containerId, events) {
            var grid = document.getElementById(containerId);
            if (!grid) return;
            
            if (!events || events.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-3 text-center py-12">
                        <i class="fa-solid fa-calendar-day text-4xl text-slate-300 mb-3"></i>
                        <p class="text-slate-400 text-sm">Tiada acara dalam kategori ini.</p>
                    </div>`;
                return;
            }
            
            grid.innerHTML = '';
            events.forEach(event => {
                grid.innerHTML += createEventCard(event, false);
            });
        }

        function createEventCard(event, isFeatured) {
            var statusBadge = event.event_status === 'ongoing' ? 
                '<span class="badge-ongoing text-[9px] font-bold px-2 py-1 rounded-full uppercase">Sedang Berlangsung</span>' :
                event.event_status === 'upcoming' ?
                '<span class="badge-upcoming text-[9px] font-bold px-2 py-1 rounded-full uppercase">Akan Datang</span>' :
                '<span class="badge-past text-[9px] font-bold px-2 py-1 rounded-full uppercase">Telah Tamat</span>';
            
            var posterHtml = event.poster_image ? 
                `<img src="<?= base_url() ?>${event.poster_image}" alt="${event.program_name}" class="event-poster">` :
                `<div class="event-poster bg-gradient-to-br from-[#8a0028]/10 to-[#ffc20e]/10 flex items-center justify-center">
                    <i class="fa-solid fa-image text-5xl text-slate-300"></i>
                </div>`;
            
            var featuredBadge = isFeatured ? 
                '<span class="absolute top-2 right-2 bg-yellow-400 text-[#520018] text-[9px] font-bold px-2 py-1 rounded-full"><i class="fa-solid fa-star mr-1"></i>Featured</span>' :
                '';
            
            return `
                <div class="glass-card event-card rounded-2xl overflow-hidden relative">
                    ${featuredBadge}
                    ${posterHtml}
                    <div class="p-5">
                        <div class="flex justify-between items-start gap-2 mb-2">
                            <h3 class="font-bold text-[#520018] text-sm flex-1">${escHtml(event.program_name)}</h3>
                            ${statusBadge}
                        </div>
                        ${event.location ? `<p class="text-xs text-slate-500"><i class="fa-solid fa-location-dot text-[#8a0028]"></i> ${escHtml(event.location)}</p>` : ''}
                        <p class="text-xs text-slate-500 mt-1"><i class="fa-solid fa-calendar-days text-[#8a0028]"></i> ${formatTarikh(event.start_date)} - ${formatTarikh(event.end_date)}</p>
                        ${event.pic_nama ? `
                            <div class="mt-3 pt-3 border-t border-white/50 text-[10px] text-slate-500">
                                <i class="fa-solid fa-user-tie text-[#8a0028]"></i> PIC: ${escHtml(event.pic_nama)} 
                                ${event.pic_tel ? `| <i class="fa-solid fa-phone text-[#8a0028]"></i> ${escHtml(event.pic_tel)}` : ''}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }

        function showError(message) {
            ['featuredGrid', 'allEventsGrid', 'upcomingGrid', 'ongoingGrid', 'pastGrid'].forEach(id => {
                var el = document.getElementById(id);
                if (el) {
                    el.innerHTML = `
                        <div class="col-span-3 text-center py-12">
                            <i class="fa-solid fa-exclamation-triangle text-4xl text-red-400 mb-3"></i>
                            <p class="text-red-400 text-sm">${message}</p>
                        </div>`;
                }
            });
        }

        function formatTarikh(dateStr) {
            if (!dateStr) return '-';
            var d = new Date(dateStr);
            return d.toLocaleDateString('ms-MY', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        function escHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
    </script>
</body>
</html>