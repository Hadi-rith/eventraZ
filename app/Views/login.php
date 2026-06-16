<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventraZ - Log Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root { --maroon: #8a0028; --maroon-dark: #520018; --gold: #ffc20e; --ink: #231f20; }
        body {
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at 18% 18%, rgba(255, 194, 14, 0.28), transparent 28%),
                radial-gradient(circle at 82% 8%, rgba(138, 0, 40, 0.25), transparent 30%),
                linear-gradient(135deg, #fffaf0 0%, #f8edf1 48%, #fff8df 100%);
        }
        .login-card {
            background: rgba(255, 255, 255, 0.58);
            border: 1px solid rgba(255, 255, 255, 0.78);
            border-radius: 42px;
            box-shadow: 0 28px 80px rgba(82, 0, 24, 0.18), inset 0 1px 0 rgba(255,255,255,.85);
            backdrop-filter: blur(28px) saturate(165%);
            -webkit-backdrop-filter: blur(28px) saturate(165%);
        }
        .brand-logo {
            width: 280px;
            max-width: 88%;
            margin: 0 auto 1.25rem;
            background: #fff;
            border-radius: 34px;
            padding: 8px;
            filter: drop-shadow(0 18px 24px rgba(82, 0, 24, .12));
        }
        .tab-shell, .mode-shell {
            background: rgba(255,255,255,.36);
            border: 1px solid rgba(138,0,40,.1);
            border-radius: 26px;
            padding: 5px;
        }
        .tab-btn, .mode-btn { border-radius: 20px; transition: all .25s ease; }
        .tab-active, .mode-active { background: rgba(255, 194, 14, .72); color: var(--maroon); font-weight: 800; box-shadow: inset 0 1px 0 rgba(255,255,255,.8); }
        .liquid-input { background: rgba(255,255,255,.68); border-color: rgba(138,0,40,.14); border-radius: 24px; color: #111827; }
        .liquid-input:focus { box-shadow: 0 0 0 3px rgba(255,194,14,.28); }
        .primary-btn { background: linear-gradient(135deg, var(--maroon), var(--maroon-dark)); border-radius: 24px; box-shadow: 0 18px 36px rgba(138,0,40,.22); }
        .primary-btn:hover { filter: brightness(1.08); }
        .secondary-btn { border-radius: 24px; }
        .soft-divider { height: 1px; border-radius: 999px; background: rgba(138,0,40,.12); }
        .form-panel { display: none; }
        .form-panel.active { display: block; }
    </style>
</head>
<body class="flex items-center justify-center p-6">
    <div class="login-card p-8 md:p-10 w-full max-w-md text-center">
        <img src="<?= base_url('assets/eventraz-logo.jpeg') ?>" alt="EventraZ" class="brand-logo">
        <h2 class="text-3xl font-black text-slate-800">EventraZ</h2>
        <p class="text-[11px] text-slate-400 mb-6 uppercase tracking-[0.2em] font-bold">Portal Log Masuk Rasmi</p>

        <div class="tab-shell flex mb-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
            <button onclick="setRole('school')" id="tabSchool" class="flex-1 py-3 tab-btn tab-active">
                <i class="fa-solid fa-school mr-1"></i> Sekolah
            </button>
            <button onclick="setRole('awam')" id="tabAwam" class="flex-1 py-3 tab-btn">
                <i class="fa-solid fa-user-group mr-1"></i> Awam
            </button>
        </div>

        <div id="userModes" class="mode-shell flex mb-6 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            <button onclick="setMode('login')" id="modeLogin" class="flex-1 py-2.5 mode-btn mode-active">Log Masuk</button>
            <button onclick="setMode('signup')" id="modeSignup" class="flex-1 py-2.5 mode-btn">Daftar Akaun</button>
        </div>

        <div id="loginPanel" class="form-panel active space-y-5 text-left">
            <div>
                <label id="lblUser" class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Kod Sekolah / Emel *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i id="iconUser" class="fa-solid fa-address-card"></i>
                    </span>
                    <input type="text" id="user" placeholder="Masukkan Kod Sekolah atau Emel"
                        class="liquid-input w-full pl-12 pr-4 py-4 border text-sm outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Kata Laluan *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" id="pass" placeholder="Kata Laluan"
                        class="liquid-input w-full pl-12 pr-4 py-4 border text-sm outline-none transition-all">
                </div>
            </div>

            <button onclick="doLogin()" class="primary-btn w-full text-white font-bold py-4 text-sm flex items-center justify-center gap-3 transition-all active:scale-[0.98]">
                <i class="fa-solid fa-right-to-bracket"></i> LOG MASUK
            </button>
        </div>

        <form id="signupSchoolPanel" class="form-panel space-y-4 text-left" onsubmit="doSignup(event)">
            <input type="hidden" name="signupRole" value="school">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Nama Sekolah *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i class="fa-solid fa-school"></i>
                    </span>
                    <input name="school_name" type="text" placeholder="Nama Sekolah"
                        class="liquid-input w-full pl-12 pr-4 py-4 border text-sm outline-none">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Kod Sekolah *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i class="fa-solid fa-address-card"></i>
                    </span>
                    <input name="school_code" type="text" placeholder="Kod Sekolah"
                        class="liquid-input w-full pl-12 pr-4 py-4 border text-sm outline-none uppercase">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Emel Sekolah *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input name="email" type="email" placeholder="Emel Sekolah"
                        class="liquid-input w-full pl-12 pr-4 py-4 border text-sm outline-none">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Kata Laluan *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input name="password" type="password" placeholder="Kata Laluan"
                        class="liquid-input w-full pl-12 pr-4 py-4 border text-sm outline-none">
                </div>
            </div>
            <button type="submit" class="primary-btn w-full text-white font-bold py-4 text-sm flex items-center justify-center gap-3 transition-all active:scale-[0.98]">
                <i class="fa-solid fa-user-plus"></i> DAFTAR SEKOLAH
            </button>
        </form>

        <form id="signupAwamPanel" class="form-panel space-y-4 text-left" onsubmit="doSignup(event)">
            <input type="hidden" name="signupRole" value="awam">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Nama Penuh *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input name="name" type="text" placeholder="Nama Penuh"
                        class="liquid-input w-full pl-12 pr-4 py-4 border text-sm outline-none">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Emel *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input name="email" type="email" placeholder="Emel"
                        class="liquid-input w-full pl-12 pr-4 py-4 border text-sm outline-none">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2 ml-1 tracking-wider">Kata Laluan *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input name="password" type="password" placeholder="Kata Laluan"
                        class="liquid-input w-full pl-12 pr-4 py-4 border text-sm outline-none">
                </div>
            </div>
            <button type="submit" class="primary-btn w-full text-white font-bold py-4 text-sm flex items-center justify-center gap-3 transition-all active:scale-[0.98]">
                <i class="fa-solid fa-user-plus"></i> DAFTAR AWAM
            </button>
        </form>

        <div class="relative flex py-7 items-center">
            <div class="flex-grow soft-divider"></div>
            <span class="mx-4 text-[10px] text-slate-400 font-bold uppercase tracking-widest">Admin</span>
            <div class="flex-grow soft-divider"></div>
        </div>

        <button onclick="setRole('admin')" id="adminBtn"
            class="secondary-btn w-full border-2 border-[#ffc20e] text-[#8a0028] font-bold py-4 text-sm flex items-center justify-center gap-3 hover:bg-yellow-50 transition-all active:scale-[0.98] bg-white/35">
            <i class="fa-solid fa-user-shield"></i> LOG MASUK ADMIN
        </button>

        <p id="helperText" class="text-[10px] text-slate-400 mt-4 italic">
            Sekolah dan awam boleh daftar akaun sebelum log masuk.
        </p>
    </div>

    <script>
        var currentRole = 'school';
        var currentMode = 'login';

        function setRole(role) {
            currentRole = role;

            document.getElementById('tabSchool').classList.toggle('tab-active', role === 'school');
            document.getElementById('tabAwam').classList.toggle('tab-active', role === 'awam');
            document.getElementById('adminBtn').classList.toggle('mode-active', role === 'admin');

            var lblUser = document.getElementById('lblUser');
            var txtUser = document.getElementById('user');
            var iconUser = document.getElementById('iconUser');
            var userModes = document.getElementById('userModes');

            if (role === 'admin') {
                currentMode = 'login';
                userModes.style.display = 'none';
                lblUser.innerText = 'ID Admin *';
                txtUser.placeholder = 'Masukkan ID Admin';
                iconUser.className = 'fa-solid fa-user-shield';
            } else {
                userModes.style.display = 'flex';
                lblUser.innerText = role === 'school' ? 'Kod Sekolah / Emel *' : 'Emel Awam *';
                txtUser.placeholder = role === 'school' ? 'Masukkan Kod Sekolah atau Emel' : 'Masukkan Emel Awam';
                iconUser.className = role === 'school' ? 'fa-solid fa-school' : 'fa-solid fa-envelope';
            }

            setMode(currentMode);
        }

        function setMode(mode) {
            if (currentRole === 'admin') {
                mode = 'login';
            }

            currentMode = mode;
            document.getElementById('modeLogin').classList.toggle('mode-active', mode === 'login');
            document.getElementById('modeSignup').classList.toggle('mode-active', mode === 'signup');
            document.getElementById('loginPanel').classList.toggle('active', mode === 'login');
            document.getElementById('signupSchoolPanel').classList.toggle('active', mode === 'signup' && currentRole === 'school');
            document.getElementById('signupAwamPanel').classList.toggle('active', mode === 'signup' && currentRole === 'awam');
        }

        async function doLogin() {
            var userVal = document.getElementById('user').value.trim();
            var passVal = document.getElementById('pass').value.trim();

            if (!userVal || !passVal) {
                Swal.fire({ icon: 'warning', title: 'Sila lengkapkan maklumat', text: 'ID/Emel dan Kata Laluan diperlukan.' });
                return;
            }

            Swal.fire({ title: 'Menyemak Akses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const res = await fetch('<?= base_url('login/proses') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ username: userVal, password: passVal, role: currentRole })
                });
                const result = await res.json();
                Swal.close();

                if (result.success) {
                    window.location.href = '<?= base_url() ?>' + result.redirect;
                } else {
                    Swal.fire({ icon: 'error', title: 'Akses Ditolak', text: result.message });
                }
            } catch (err) {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Ralat', text: 'Sistem sedang sibuk. Sila cuba sebentar lagi.' });
            }
        }

        async function doSignup(event) {
            event.preventDefault();
            var form = event.target;
            var role = new FormData(form).get('signupRole');
            var endpoint = role === 'school' ? '<?= base_url('signup/school') ?>' : '<?= base_url('signup/awam') ?>';

            for (var input of form.querySelectorAll('input:not([type="hidden"])')) {
                if (!input.value.trim()) {
                    Swal.fire({ icon: 'warning', title: 'Maklumat belum lengkap', text: 'Sila lengkapkan semua medan.' });
                    input.focus();
                    return;
                }
            }

            Swal.fire({ title: 'Mendaftar Akaun...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const res = await fetch(endpoint, { method: 'POST', body: new FormData(form) });
                const result = await res.json();
                Swal.close();

                if (result.success) {
                    form.reset();
                    setMode('login');
                    Swal.fire({ icon: 'success', title: 'Berjaya', text: result.message });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message });
                }
            } catch (err) {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Ralat', text: 'Akaun tidak dapat didaftarkan buat masa ini.' });
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && currentMode === 'login') doLogin();
        });
    </script>
</body>
</html>
