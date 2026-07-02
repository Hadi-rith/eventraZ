<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventraZ - Kehadiran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root { --maroon: #8a0028; --maroon-dark: #520018; --gold: #ffc20e; }
        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at 18% 8%, rgba(255,194,14,.22), transparent 28%),
                radial-gradient(circle at 88% 16%, rgba(138,0,40,.18), transparent 26%),
                linear-gradient(135deg, #fffaf0 0%, #f7eef2 46%, #fff8df 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .glass-card { background: rgba(255,255,255,.7); border: 1px solid rgba(255,255,255,.85); border-radius: 34px; box-shadow: 0 24px 58px rgba(82,0,24,.14); backdrop-filter: blur(26px) saturate(160%); }
        .eventraz-btn { background: linear-gradient(135deg, var(--maroon), var(--maroon-dark)); border-radius: 24px !important; box-shadow: 0 18px 36px rgba(138,0,40,.2); }
        .eventraz-btn:hover { filter: brightness(1.08); }
    </style>
</head>
<body>
    <div class="glass-card p-10 max-w-md w-full mx-4 text-center">
        <img src="<?= base_url('assets/eventraz-logo.jpeg') ?>" alt="EventraZ" class="w-32 mx-auto mb-6 rounded-2xl bg-white p-1">

        <?php if ($result === 'success'): ?>
            <div class="text-green-600 text-6xl mb-4"><i class="fa-solid fa-circle-check"></i></div>
            <h1 class="text-2xl font-black text-green-700 mb-2">Attendance Successful</h1>
            <p class="text-gray-600"><?= esc($session['session_name'] ?? '') ?></p>
            <p class="text-xs text-gray-400 mt-2">Anda telah berjaya menandakan kehadiran.</p>
        <?php elseif ($result === 'duplicate'): ?>
            <div class="text-yellow-500 text-6xl mb-4"><i class="fa-solid fa-circle-info"></i></div>
            <h1 class="text-2xl font-black text-yellow-600 mb-2">Already Checked In</h1>
            <p class="text-gray-600"><?= esc($session['session_name'] ?? '') ?></p>
            <p class="text-xs text-gray-400 mt-2">Kehadiran anda telah direkodkan sebelum ini.</p>
        <?php elseif ($result === 'expired'): ?>
            <div class="text-red-600 text-6xl mb-4"><i class="fa-solid fa-clock"></i></div>
            <h1 class="text-2xl font-black text-red-600 mb-2">Session Expired</h1>
            <p class="text-gray-500">Sesi kehadiran ini telah tamat tempoh.</p>
            <p class="text-xs text-gray-400 mt-2"><?= esc($message ?? 'Sila hubungi penganjur untuk bantuan.') ?></p>
        <?php elseif ($result === 'not_registered'): ?>
            <div class="text-orange-500 text-6xl mb-4"><i class="fa-solid fa-ban"></i></div>
            <h1 class="text-2xl font-black text-orange-600 mb-2">Not Registered for This Program</h1>
            <p class="text-gray-600"><?= esc($session['session_name'] ?? '') ?></p>
            <p class="text-xs text-gray-400 mt-2">Anda tidak berdaftar untuk program ini. Sila daftar terlebih dahulu.</p>
        <?php else: ?>
            <div class="text-red-600 text-6xl mb-4"><i class="fa-solid fa-circle-xmark"></i></div>
            <h1 class="text-2xl font-black text-red-600 mb-2">Invalid QR Code</h1>
            <p class="text-gray-500">Kod QR yang diimbas tidak sah.</p>
            <p class="text-xs text-gray-400 mt-2"><?= esc($message ?? 'Sila pastikan anda mengimbas kod QR yang betul.') ?></p>
        <?php endif; ?>

        <a href="<?= base_url('/') ?>" class="eventraz-btn inline-block mt-8 text-[#ffc20e] font-bold px-8 py-3">
            Kembali ke Portal
        </a>
    </div>
</body>
</html>