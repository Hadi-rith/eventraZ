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
            top: 0;
            left: 0;
            border-right: 1px solid rgba(255,255,255,.25);
            box-shadow: 24px 0 60px rgba(82, 0, 24, .22);
            backdrop-filter: blur(24px) saturate(160%);
            -webkit-backdrop-filter: blur(24px) saturate(160%);
        }
        .active-tab { background-color: #ffc20e !important; color: #520018 !important; border-radius: 24px !important; }
        .brand-logo {
            width: 172px;
            background: #fff;
            border-radius: 28px;
            padding: 6px;
            filter: drop-shadow(0 14px 20px rgba(82, 0, 24, .16));
        }
        .glass-card {
            background: rgba(255,255,255,.58);
            border: 1px solid rgba(255,255,255,.82);
            border-radius: 34px;
            box-shadow: 0 24px 58px rgba(82, 0, 24, .12), inset 0 1px 0 rgba(255,255,255,.9);
            backdrop-filter: blur(26px) saturate(160%);
            -webkit-backdrop-filter: blur(26px) saturate(160%);
        }
        .eventraz-field { background: rgba(255,255,255,.58) !important; border-color: rgba(138,0,40,.15) !important; border-radius: 24px !important; }
        .eventraz-field:focus { box-shadow: 0 0 0 3px rgba(255,194,14,.28); }
        .program-select, .program-select option { background: #fff !important; color: #111827 !important; }
        .eventraz-btn { background: linear-gradient(135deg, var(--maroon), var(--maroon-dark)); border-radius: 24px !important; box-shadow: 0 18px 36px rgba(138,0,40,.2); }
        .eventraz-btn:hover { filter: brightness(1.08); }
        @media (max-width: 900px) {
            body.flex { display: block; }
            .sidebar { position: relative; width: 100%; height: auto; }
            .ml-\[280px\] { margin-left: 0 !important; }
            .grid-cols-2 { grid-template-columns: repeat(1, minmax(0, 1fr)) !important; }
        }
    </style>
</head>
<body class="flex">

    <!-- Sidebar -->
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
                <button class="w-full text-left p-4 text-xs font-bold active-tab flex items-center gap-3 rounded-xl shadow-md">
                    <i class="fa-solid fa-file-signature"></i> DAFTAR PROGRAM TRG
                </button>
            </nav>
        </div>
        <a href="<?= base_url('logout') ?>"
            class="text-xs text-yellow-100 font-bold p-3 flex items-center gap-2 hover:text-white hover:bg-white/10 rounded-xl transition-all">
            <i class="fa-solid fa-power-off"></i> LOG KELUAR PORTAL
        </a>
    </div>

    <!-- Main Content -->
    <div class="ml-[280px] w-full p-10 flex justify-center items-start min-h-screen py-12">
        <div class="glass-card w-full max-w-4xl p-10 rounded-3xl">
            <h2 class="text-2xl font-bold text-[#520018] mb-2 uppercase tracking-tight">Borang Urus Setia</h2>
            <p class="text-xs text-slate-400 mb-8 border-b pb-4">Sekolah Terengganu — sila lengkapkan semua maklumat yang diperlukan.</p>

            <form id="regFormTRG" class="space-y-6">

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Pilihan Program *</label>
                    <select name="programId" id="progList" class="program-select eventraz-field w-full p-4 border rounded-2xl outline-none text-sm transition-all" required>
                        <option value="">-- Sila Pilih Program --</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Nama Penuh Sekolah *</label>
                    <input type="text" name="namaSekolah" placeholder="Nama Sekolah Terengganu"
                        class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Kod Sekolah *</label>
                        <input type="text" name="kodSekolah" placeholder="Contoh: TBA1001"
                            class="eventraz-field p-4 w-full border rounded-2xl text-sm outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Nama Guru Pengiring *</label>
                        <input type="text" name="namaGuru" placeholder="Nama Guru Pengiring"
                            class="eventraz-field p-4 w-full border rounded-2xl text-sm outline-none transition-all" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">No. Kad Pengenalan Guru *</label>
                        <input type="text" name="icGuru" placeholder="Tanpa sengkang (-)"
                            class="eventraz-field p-4 w-full border rounded-2xl text-sm outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">No. Telefon Guru *</label>
                        <input type="text" name="telGuru" placeholder="Contoh: 019XXXXXXXX"
                            class="eventraz-field p-4 w-full border rounded-2xl text-sm outline-none transition-all" required>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Bilangan Murid Terlibat (Maks 10) *</label>
                    <input type="number" name="bilMurid" id="bilMurid" min="1" max="10" oninput="janaBorangMurid()"
                        placeholder="Sila taip angka bilangan murid sahaja"
                        class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                </div>

                <div id="boxMurid" class="space-y-3"></div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1">Emel Rasmi Hubungan *</label>
                    <input type="email" name="email" placeholder="Contoh: sekolah@moe.edu.my"
                        class="eventraz-field w-full p-4 border rounded-2xl text-sm outline-none transition-all" required>
                </div>

                <button type="button" onclick="hantarFormTRG()"
                    class="eventraz-btn w-full text-white font-bold py-4 rounded-2xl uppercase tracking-widest text-xs transition-all active:scale-[0.98]">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Sahkan & Hantar Pendaftaran
                </button>
            </form>
        </div>
    </div>

    <script>
        window.onload = function () {
            muatSenaraiProgram();
            window.addEventListener('focus', muatSenaraiProgram);
            setInterval(muatSenaraiProgram, 30000);
        };

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

        async function muatSenaraiProgram() {
            try {
                const res = await fetch('<?= base_url('school/programs') ?>?t=' + Date.now(), { cache: 'no-store' });
                const list = await res.json();
                var sel = document.getElementById('progList');
                var selected = sel.value;

                sel.innerHTML = '<option value="">-- Sila Pilih Program --</option>';
                list.forEach(p => {
                    var option = document.createElement('option');
                    option.value = p.id;
                    option.textContent = p.nama;
                    sel.appendChild(option);
                });

                if ([...sel.options].some(option => option.value === selected)) {
                    sel.value = selected;
                }
            } catch (err) {
                console.error('Gagal memuatkan senarai program', err);
            }
        }

        function janaBorangMurid() {
            var bil = parseInt(document.getElementById('bilMurid').value) || 0;
            if (bil > 10) { bil = 10; document.getElementById('bilMurid').value = 10; }
            var box = document.getElementById('boxMurid');
            box.innerHTML = '';
            for (var i = 0; i < bil; i++) {
                box.innerHTML += `
                    <div class="grid grid-cols-2 gap-4 p-4 border border-yellow-100 rounded-2xl bg-white/35">
                        <div>
                            <label class="block text-[9px] font-bold text-[#8a0028] uppercase mb-1">Nama Penuh Murid ${i + 1}</label>
                            <input type="text" name="namaMurid_${i}" placeholder="Nama Penuh Murid"
                                class="eventraz-field w-full p-3 border rounded-xl text-xs outline-none" required>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-[#8a0028] uppercase mb-1">No. MyKid / IC Murid ${i + 1}</label>
                            <input type="text" name="icMurid_${i}" placeholder="Tanpa tanda (-)"
                                class="eventraz-field w-full p-3 border rounded-xl text-xs outline-none" required>
                        </div>
                    </div>`;
            }
        }

        async function hantarFormTRG() {
            var form   = document.getElementById('regFormTRG');
            var inputs = form.querySelectorAll('[required]');
            for (var inp of inputs) {
                if (!inp.value.trim()) {
                    Swal.fire({ icon: 'warning', title: 'Borang tidak lengkap', text: 'Sila isi semua medan yang bertanda *.' });
                    inp.focus();
                    return;
                }
            }

            Swal.fire({ title: 'Memproses...', text: 'Menyimpan maklumat sekolah...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            var data = new FormData(form);
            // Append murid fields dynamically
            var bil = parseInt(document.getElementById('bilMurid').value) || 0;
            data.append('bilMurid', bil);

            try {
                const res = await fetch('<?= base_url('school/daftar') ?>', {
                    method: 'POST',
                    body: data
                });
                const result = await res.json();
                Swal.close();

                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Berjaya Disimpan!', text: 'Pendaftaran Sekolah Terengganu telah direkodkan.' });
                    form.reset();
                    document.getElementById('boxMurid').innerHTML = '';
                } else {
                    Swal.fire({ icon: 'error', title: 'Ralat', text: result.message });
                }
            } catch (err) {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Ralat Sistem', text: 'Sila cuba sebentar lagi.' });
            }
        }
    </script>
</body>
</html>
