<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventraZ - Log Masuk</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fffaf0 0%, #f8edf1 50%, #fff8df 100%);
            padding: 2rem;
        }

        .card {
            background: #ffffff;
            border: 1px solid rgba(138, 0, 40, 0.1);
            border-radius: 28px;
            padding: 2.5rem 2rem 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(82, 0, 24, 0.12);
            text-align: center;
        }

        /* Logo */
        .logo-wrap { margin-bottom: 1.75rem; }
        .logo-wrap img {
            width: 110px;
            height: 110px;
            border-radius: 24px;
            object-fit: cover;
            box-shadow: 0 10px 28px rgba(82, 0, 24, 0.15);
            display: block;
            margin: 0 auto;
        }
        .logo-wrap h1 {
            font-size: 24px;
            font-weight: 800;
            color: #1a1a1a;
            margin-top: 12px;
        }
        .logo-wrap p {
            font-size: 10px;
            color: #888888;
            letter-spacing: 0.18em;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* Role underline tabs */
        .role-tabs {
            display: flex;
            gap: 24px;
            justify-content: center;
            border-bottom: 1.5px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }
        .role-tabs button {
            background: none;
            border: none;
            padding: 0 0 10px;
            font-size: 12px;
            font-weight: 700;
            color: #aaaaaa;
            cursor: pointer;
            position: relative;
            font-family: 'Poppins', sans-serif;
            transition: color 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }
        .role-tabs button:hover { color: #8a0028; }
        .role-tabs button.active { color: #8a0028; }
        .role-tabs button.active::after {
            content: '';
            position: absolute;
            bottom: -1.5px;
            left: 0;
            right: 0;
            height: 2px;
            background: #8a0028;
            border-radius: 99px;
        }

        /* Fields */
        .field { margin-bottom: 1rem; text-align: left; }
        .field label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: #555555;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 5px;
        }
        .inp-wrap { position: relative; }
        .inp-wrap i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaaaaa;
            font-size: 13px;
            pointer-events: none;
        }
        .inp-wrap input {
            width: 100%;
            padding: 11px 13px 11px 38px;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 13px;
            font-size: 13px;
            color: #1a1a1a;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: 'Poppins', sans-serif;
        }
        .inp-wrap input::placeholder { color: #bbbbbb; }
        .inp-wrap input:focus {
            border-color: rgba(138, 0, 40, 0.35);
            box-shadow: 0 0 0 3px rgba(255, 194, 14, 0.25);
            background: #ffffff;
        }

        /* Primary button */
        .btn-main {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #8a0028, #520018);
            color: #ffffff;
            border: none;
            border-radius: 13px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 0.25rem;
            transition: filter 0.2s, transform 0.1s;
            letter-spacing: 0.04em;
        }
        .btn-main:hover { filter: brightness(1.1); }
        .btn-main:active { transform: scale(0.98); }

        /* Text link */
        .text-link {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 12px;
            color: #777777;
        }
        .text-link a {
            color: #8a0028;
            font-weight: 700;
            text-decoration: underline;
            cursor: pointer;
        }
        .text-link a:hover { color: #520018; }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1.5rem 0 1rem;
        }
        .div-line { flex: 1; height: 1px; background: #e5e7eb; border-radius: 99px; }
        .divider span {
            font-size: 10px;
            font-weight: 700;
            color: #aaaaaa;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Admin button */
        .btn-admin {
            width: 100%;
            padding: 11px;
            background: #fffdf5;
            border: 1.5px solid #ffc20e;
            color: #8a0028;
            border-radius: 13px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-admin:hover { background: #fff8d6; }
        .btn-admin:active { transform: scale(0.98); }

        /* Panels */
        .panel { display: none; }
        .panel.active { display: block; }
    </style>
</head>
<body>

<div class="card">
    <div class="logo-wrap">
        <img src="<?= base_url('assets/eventraz-logo.jpeg') ?>" alt="EventraZ Logo">
        <h1>EventraZ</h1>
        <p>Portal Log Masuk Rasmi</p>
    </div>

    <div class="role-tabs" id="roleTabs">
        <button id="tabSchool" class="active" onclick="setRole('school')">
            <i class="fa-solid fa-school" style="margin-right:5px;font-size:10px"></i>Sekolah
        </button>
        <button id="tabAwam" onclick="setRole('awam')">
            <i class="fa-solid fa-user-group" style="margin-right:5px;font-size:10px"></i>Awam
        </button>
    </div>

    <!-- Login panel -->
    <div id="loginPanel" class="panel active">
        <div class="field">
            <label id="lblUser">Kod Sekolah / Emel</label>
            <div class="inp-wrap">
                <i id="iconUser" class="fa-solid fa-school"></i>
                <input type="text" id="user" placeholder="Kod Sekolah atau Emel">
            </div>
        </div>
        <div class="field">
            <label>Kata Laluan</label>
            <div class="inp-wrap">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="pass" placeholder="Kata Laluan">
            </div>
        </div>
        <button class="btn-main" onclick="doLogin()">
            <i class="fa-solid fa-right-to-bracket"></i> Log Masuk
        </button>
        <p class="text-link">Belum ada akaun? <a onclick="setMode('signup')">Daftar Akaun</a></p>
    </div>

    <!-- Signup school panel -->
    <div id="signupSchoolPanel" class="panel">
        <form onsubmit="doSignup(event,'school')">
            <input type="hidden" name="signupRole" value="school">
            <div class="field">
                <label>Nama Sekolah</label>
                <div class="inp-wrap">
                    <i class="fa-solid fa-school"></i>
                    <input name="school_name" type="text" placeholder="Nama Sekolah" required>
                </div>
            </div>
            <div class="field">
                <label>Kod Sekolah</label>
                <div class="inp-wrap">
                    <i class="fa-solid fa-address-card"></i>
                    <input name="school_code" type="text" placeholder="Kod Sekolah" style="text-transform:uppercase" required>
                </div>
            </div>
            <div class="field">
                <label>Emel Sekolah</label>
                <div class="inp-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input name="email" type="email" placeholder="Emel Sekolah" required>
                </div>
            </div>
            <div class="field">
                <label>Kata Laluan</label>
                <div class="inp-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input name="password" type="password" placeholder="Kata Laluan" required>
                </div>
            </div>
            <button type="submit" class="btn-main">
                <i class="fa-solid fa-user-plus"></i> Daftar Sekolah
            </button>
        </form>
        <p class="text-link">Sudah ada akaun? <a onclick="setMode('login')">Log Masuk</a></p>
    </div>

    <!-- Signup awam panel -->
    <div id="signupAwamPanel" class="panel">
        <form onsubmit="doSignup(event,'awam')">
            <input type="hidden" name="signupRole" value="awam">
            <div class="field">
                <label>Nama Penuh</label>
                <div class="inp-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input name="name" type="text" placeholder="Nama Penuh" required>
                </div>
            </div>
            <div class="field">
                <label>Emel</label>
                <div class="inp-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input name="email" type="email" placeholder="Emel" required>
                </div>
            </div>
            <div class="field">
                <label>Kata Laluan</label>
                <div class="inp-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input name="password" type="password" placeholder="Kata Laluan" required>
                </div>
            </div>
            <button type="submit" class="btn-main">
                <i class="fa-solid fa-user-plus"></i> Daftar Awam
            </button>
        </form>
        <p class="text-link">Sudah ada akaun? <a onclick="setMode('login')">Log Masuk</a></p>
    </div>

    <!-- Admin login panel -->
    <div id="adminPanel" class="panel">
        <div class="field">
            <label>ID Admin</label>
            <div class="inp-wrap">
                <i class="fa-solid fa-user-shield"></i>
                <input type="text" id="adminUser" placeholder="Masukkan ID Admin">
            </div>
        </div>
        <div class="field">
            <label>Kata Laluan</label>
            <div class="inp-wrap">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="adminPass" placeholder="Kata Laluan">
            </div>
        </div>
        <button class="btn-main" onclick="doLoginAdmin()">
            <i class="fa-solid fa-right-to-bracket"></i> Log Masuk Admin
        </button>
        <p class="text-link"><a onclick="exitAdmin()">← Kembali</a></p>
    </div>

    <div class="divider" id="adminDivider">
        <div class="div-line"></div>
        <span>Admin</span>
        <div class="div-line"></div>
    </div>
    <button class="btn-admin" id="adminBtn" onclick="toggleAdmin()">
        <i class="fa-solid fa-user-shield"></i> Log Masuk Admin
    </button>
</div>

<script>
    var currentRole = 'school';
    var currentMode = 'login';
    var adminMode   = false;

    function setRole(role) {
        currentRole = role;
        document.getElementById('tabSchool').classList.toggle('active', role === 'school');
        document.getElementById('tabAwam').classList.toggle('active', role === 'awam');

        var lbl  = document.getElementById('lblUser');
        var inp  = document.getElementById('user');
        var icon = document.getElementById('iconUser');

        if (role === 'school') {
            lbl.innerText   = 'Kod Sekolah / Emel';
            inp.placeholder = 'Kod Sekolah atau Emel';
            icon.className  = 'fa-solid fa-school';
        } else {
            lbl.innerText   = 'Emel Awam';
            inp.placeholder = 'Masukkan Emel Awam';
            icon.className  = 'fa-solid fa-envelope';
        }
        syncPanels();
    }

    function setMode(mode) {
        currentMode = mode;
        adminMode   = false;
        showMainUI(true);
        syncPanels();
    }

    function toggleAdmin() {
        adminMode = true;
        showMainUI(false);
        syncPanels();
    }

    function exitAdmin() {
        adminMode = false;
        showMainUI(true);
        syncPanels();
    }

    function showMainUI(show) {
        document.getElementById('roleTabs').style.display     = show ? 'flex' : 'none';
        document.getElementById('adminDivider').style.display = show ? 'flex' : 'none';
        document.getElementById('adminBtn').style.display     = show ? 'flex' : 'none';
    }

    function syncPanels() {
        ['loginPanel','signupSchoolPanel','signupAwamPanel','adminPanel'].forEach(function(id) {
            document.getElementById(id).classList.remove('active');
        });
        if (adminMode) {
            document.getElementById('adminPanel').classList.add('active');
            return;
        }
        if (currentMode === 'login') {
            document.getElementById('loginPanel').classList.add('active');
        } else {
            document.getElementById(currentRole === 'school' ? 'signupSchoolPanel' : 'signupAwamPanel').classList.add('active');
        }
    }

    async function doLogin() {
        var userVal = document.getElementById('user').value.trim();
        var passVal = document.getElementById('pass').value.trim();

        if (!userVal || !passVal) {
            Swal.fire({ icon: 'warning', title: 'Maklumat diperlukan', text: 'Sila isi ID/Emel dan Kata Laluan.' });
            return;
        }

        Swal.fire({ title: 'Menyemak Akses...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

        try {
            var res = await fetch('<?= base_url("login/proses") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ username: userVal, password: passVal, role: currentRole })
            });
            var result = await res.json();
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

    async function doLoginAdmin() {
        var userVal = document.getElementById('adminUser').value.trim();
        var passVal = document.getElementById('adminPass').value.trim();

        if (!userVal || !passVal) {
            Swal.fire({ icon: 'warning', title: 'Maklumat diperlukan', text: 'Sila isi ID Admin dan Kata Laluan.' });
            return;
        }

        Swal.fire({ title: 'Menyemak Akses...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

        try {
            var res = await fetch('<?= base_url("login/proses") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ username: userVal, password: passVal, role: 'admin' })
            });
            var result = await res.json();
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

    async function doSignup(event, role) {
        event.preventDefault();
        var form     = event.target;
        var endpoint = role === 'school' ? '<?= base_url("signup/school") ?>' : '<?= base_url("signup/awam") ?>';

        Swal.fire({ title: 'Mendaftar Akaun...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

        try {
            var res    = await fetch(endpoint, { method: 'POST', body: new FormData(form) });
            var result = await res.json();
            Swal.close();

            if (result.success) {
                form.reset();
                setMode('login');
                Swal.fire({ icon: 'success', title: 'Berjaya!', text: result.message });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: result.message });
            }
        } catch (err) {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Ralat', text: 'Akaun tidak dapat didaftarkan buat masa ini.' });
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            if (adminMode) { doLoginAdmin(); }
            else if (currentMode === 'login') { doLogin(); }
        }
    });
</script>
</body>
</html>