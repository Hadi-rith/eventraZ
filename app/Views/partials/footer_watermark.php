<?php
/**
 * Site footer watermark — include on every page.
 * Optional: pass $watermarkClass to override positioning/styles.
 */
$watermarkClass = $watermarkClass ?? 'fixed bottom-3 right-4 text-[10px] text-slate-300 tracking-wide pointer-events-none select-none z-[100]';
?>
<p class="eventraz-watermark <?= esc($watermarkClass, 'attr') ?>">
    Developed by <span class="text-[#d4a0b0] font-semibold">Hadi</span>
</p>
