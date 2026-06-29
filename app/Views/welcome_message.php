<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EvenTraZ — Event Tracking, Registration &amp; Engagement Zone</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at 15% 10%, rgba(255,194,14,.28), transparent 30%),
                radial-gradient(circle at 85% 18%, rgba(138,0,40,.20), transparent 28%),
                radial-gradient(circle at 50% 85%, rgba(255,194,14,.15), transparent 35%),
                linear-gradient(135deg, #fffaf0 0%, #f7eef2 46%, #fff8df 100%);
            min-height: 100vh;
        }
        .glass {
            background: rgba(255,255,255,.62);
            border: 1px solid rgba(255,255,255,.85);
            box-shadow: 0 24px 58px rgba(82,0,24,.10), inset 0 1px 0 rgba(255,255,255,.9);
            backdrop-filter: blur(28px) saturate(160%);
        }
        .maroon-btn {
            background: linear-gradient(135deg, #8a0028, #520018);
            transition: filter .2s, transform .1s;
        }
        .maroon-btn:hover { filter: brightness(1.10); }
        .maroon-btn:active { transform: scale(.97); }
        .gold-btn {
            background: linear-gradient(135deg, #ffc20e, #e6a800);
            transition: filter .2s, transform .1s;
        }
        .gold-btn:hover { filter: brightness(1.08); }
        .gold-btn:active { transform: scale(.97); }
        .feature-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, rgba(138,0,40,.12), rgba(255,194,14,.18));
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
        }
        .float-anim { animation: floatY 4s ease-in-out infinite; }
        @keyframes floatY { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    </style>
    <?= view('partials/mobile_responsive', ['mobileLayout' => 'default']) ?>
</head>
<body class="mobile-default">

    <!-- NAVBAR -->
    <nav class="fixed top-0 left-0 right-0 z-50 px-8 py-4 flex items-center justify-between glass border-b border-white/60">
        <div class="flex items-center gap-3">
            <img src="<?= base_url('assets/eventraz-logo.jpeg') ?>" alt="EvenTraZ"
                class="w-10 h-10 rounded-xl object-cover shadow">
            <div>
                <span class="font-black text-[#520018] text-lg tracking-tight">EvenTraZ</span>
                <p class="text-[9px] text-slate-400 uppercase tracking-widest font-bold -mt-0.5">PSKT</p>
            </div>
        </div>
        <a href="<?= base_url('login') ?>"
            class="maroon-btn text-white text-xs font-bold px-4 sm:px-6 py-2.5 rounded-xl flex items-center gap-2 shadow-lg">
            <i class="fa-solid fa-right-to-bracket"></i> Log Masuk
        </a>
    </nav>

    <!-- HERO -->
    <section class="pt-32 pb-20 px-8 max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-12">
        <div class="flex-1 text-center md:text-left">
            <span class="inline-block bg-yellow-100 text-[#8a0028] text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest mb-5 border border-yellow-200">
                <i class="fa-solid fa-star mr-1 text-yellow-500"></i> Sistem Pengurusan Acara PSKT
            </span>
            <h1 class="text-4xl md:text-5xl font-black text-[#231f20] leading-tight mb-4">
                Urus Acara &amp;<br>
                <span class="text-[#8a0028]">Pendaftaran</span><br>
                Dengan Mudah
            </h1>
            <p class="text-slate-500 text-sm leading-relaxed mb-8 max-w-md">
                Platform pengurusan acara bersepadu untuk sekolah dan orang awam.
                Daftar program, pantau kapasiti, dan jejak kehadiran — semuanya di satu tempat.
            </p>
            <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                <a href="<?= base_url('login') ?>"
                    class="maroon-btn text-white font-bold px-8 py-3.5 rounded-xl flex items-center gap-2 shadow-xl text-sm">
                    <i class="fa-solid fa-right-to-bracket"></i> Log Masuk Sekarang
                </a>
                <a href="<?= base_url('login') ?>"
                    class="gold-btn text-[#520018] font-bold px-8 py-3.5 rounded-xl flex items-center gap-2 shadow-lg text-sm">
                    <i class="fa-solid fa-user-plus"></i> Daftar Akaun
                </a>
            </div>
        </div>

        <!-- Hero illustration -->
        <div class="flex-1 flex justify-center float-anim">
            <div class="glass rounded-3xl p-8 w-full max-w-sm shadow-2xl">
                <div class="text-center mb-6">
                    <img src="<?= base_url('assets/eventraz-logo.jpeg') ?>" alt="EvenTraZ"
                        class="w-24 h-24 rounded-2xl mx-auto shadow-lg object-cover mb-4">
                    <h3 class="font-black text-[#520018] text-lg">EvenTraZ</h3>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest">Event Tracking, Registration &amp; Engagement Zone</p>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 bg-white/60 rounded-xl p-3">
                        <div class="w-8 h-8 bg-[#8a0028]/10 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-school text-[#8a0028] text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700">Sekolah</p>
                            <p class="text-[10px] text-slate-400">Daftar sebagai sekolah</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white/60 rounded-xl p-3">
                        <div class="w-8 h-8 bg-yellow-400/20 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-user-group text-yellow-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700">Orang Awam</p>
                            <p class="text-[10px] text-slate-400">Daftar sebagai peserta awam</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white/60 rounded-xl p-3">
                        <div class="w-8 h-8 bg-green-400/20 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-shield-halved text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700">Penganjur</p>
                            <p class="text-[10px] text-slate-400">Urus program dan pendaftaran</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="pb-20 px-8 max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-2xl font-black text-[#520018] mb-2">Mengapa EvenTraZ?</h2>
            <p class="text-slate-500 text-sm">Semua yang anda perlukan untuk urus acara dengan cekap</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass rounded-2xl p-6">
                <div class="feature-icon mb-4"><i class="fa-solid fa-calendar-check text-[#8a0028] text-xl"></i></div>
                <h3 class="font-bold text-[#520018] mb-2">Pendaftaran Mudah</h3>
                <p class="text-slate-500 text-xs leading-relaxed">Borang pendaftaran automatik berdasarkan jenis pengguna — sekolah atau awam. Tiada kerumitan.</p>
            </div>
            <div class="glass rounded-2xl p-6">
                <div class="feature-icon mb-4"><i class="fa-solid fa-gauge-high text-[#8a0028] text-xl"></i></div>
                <h3 class="font-bold text-[#520018] mb-2">Pantau Kapasiti</h3>
                <p class="text-slate-500 text-xs leading-relaxed">Had pendaftaran dikuatkuasakan secara automatik. Kapasiti baki dikemas kini selepas setiap pendaftaran.</p>
            </div>
            <div class="glass rounded-2xl p-6">
                <div class="feature-icon mb-4"><i class="fa-solid fa-chalkboard-user text-[#8a0028] text-xl"></i></div>
                <h3 class="font-bold text-[#520018] mb-2">Guru Pengiring</h3>
                <p class="text-slate-500 text-xs leading-relaxed">Sokongan berbilang Guru Pengiring per pendaftaran. Pengesahan murid automatik (1 guru = maks 10 murid).</p>
            </div>
            <div class="glass rounded-2xl p-6">
                <div class="feature-icon mb-4"><i class="fa-solid fa-users-gear text-[#8a0028] text-xl"></i></div>
                <h3 class="font-bold text-[#520018] mb-2">Multi-Peringkat Admin</h3>
                <p class="text-slate-500 text-xs leading-relaxed">Super Admin mengurus keseluruhan sistem. Admin biasa hanya boleh akses program mereka sendiri.</p>
            </div>
            <div class="glass rounded-2xl p-6">
                <div class="feature-icon mb-4"><i class="fa-solid fa-chart-bar text-[#8a0028] text-xl"></i></div>
                <h3 class="font-bold text-[#520018] mb-2">Laporan &amp; Statistik</h3>
                <p class="text-slate-500 text-xs leading-relaxed">Papan pemuka dengan statistik terperinci untuk setiap program dan pentadbir.</p>
            </div>
            <div class="glass rounded-2xl p-6">
                <div class="feature-icon mb-4"><i class="fa-solid fa-mobile-screen text-[#8a0028] text-xl"></i></div>
                <h3 class="font-bold text-[#520018] mb-2">Mesra Mudah Alih</h3>
                <p class="text-slate-500 text-xs leading-relaxed">Reka bentuk responsif yang berfungsi dengan lancar di telefon, tablet, dan komputer.</p>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="pb-24 px-8 max-w-2xl mx-auto text-center">
        <div class="glass rounded-3xl p-10">
            <i class="fa-solid fa-calendar-plus text-4xl text-[#8a0028] mb-4"></i>
            <h2 class="text-2xl font-black text-[#520018] mb-3">Sedia untuk bermula?</h2>
            <p class="text-slate-500 text-sm mb-6">Log masuk atau daftar akaun baharu untuk mula mendaftar program.</p>
            <a href="<?= base_url('login') ?>"
                class="maroon-btn text-white font-bold px-10 py-4 rounded-xl inline-flex items-center gap-2 shadow-xl text-sm">
                <i class="fa-solid fa-right-to-bracket"></i> Log Masuk / Daftar
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t border-white/60 py-6 px-8 text-center glass">
        <p class="text-[10px] text-slate-400 uppercase tracking-widest">
            &copy; <?= date('Y') ?> EvenTraZ — PSKT &nbsp;|&nbsp; Developed by <span class="text-[#d4a0b0] font-semibold">Hadi</span>
        </p>
    </footer>

</body>
</html>