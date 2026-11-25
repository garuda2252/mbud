<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
// Pecah langkah penyelesaian per baris agar mudah dibaca secara berurutan
$rawLines = preg_split('/\r\n|\r|\n/', trim((string) $solution['steps']));
$stepItems = [];
foreach ($rawLines as $line) {
    $cleaned = trim($line);
    if ($cleaned !== '') {
        // Hilangkan penomoran manual di awal baris (misal "1. " atau "1) ") agar tidak ganda dengan nomor otomatis
        $cleaned = preg_replace('/^\d+[\.\)]\s*/', '', $cleaned);
        $stepItems[] = $cleaned;
    }
}
?>

<div x-data="{ showDeleteModal: false, showImageModal: false, copied: false }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Breadcrumb Navigation -->
    <nav class="flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500 mb-6 no-print">
        <a href="<?= site_url('/') ?>" class="hover:text-brand-600 inline-flex items-center gap-1">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>Beranda</span>
        </a>
        <span>/</span>
        <a href="<?= site_url('/?category_id=' . (int) $solution['category_id']) ?>" class="hover:text-brand-600">
            <?= esc($solution['category_name']) ?>
        </a>
        <span>/</span>
        <a href="<?= site_url('/?category_id=' . (int) $solution['category_id'] . '&sub_category_id=' . (int) $solution['sub_category_id']) ?>" class="hover:text-brand-600">
            <?= esc($solution['sub_category_name']) ?>
        </a>
        <span>/</span>
        <span class="text-slate-800 font-semibold truncate max-w-xs sm:max-w-md"><?= esc($solution['title']) ?></span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Kolom Utama (8 Kolom): Dokumen Troubleshooting -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Header Kartu Dokumen -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-2xs">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="<?= site_url('/?category_id=' . (int) $solution['category_id']) ?>"
                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200 hover:bg-brand-100 transition-colors">
                            <i data-lucide="folder" class="w-3.5 h-3.5"></i>
                            <span><?= esc($solution['category_name']) ?></span>
                        </a>
                        <span class="text-slate-400 font-bold">&rsaquo;</span>
                        <a href="<?= site_url('/?category_id=' . (int) $solution['category_id'] . '&sub_category_id=' . (int) $solution['sub_category_id']) ?>"
                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                            <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                            <span><?= esc($solution['sub_category_name']) ?></span>
                        </a>

                        <?php if ((int) $solution['is_pinned'] === 1) : ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                                <span>Quick Fix (Sering Terjadi)</span>
                            </span>
                        <?php endif; ?>
                    </div>

                    <span class="text-xs font-mono text-slate-400">ID #KB-<?= str_pad((string) $solution['id'], 4, '0', STR_PAD_LEFT) ?></span>
                </div>

                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 leading-tight">
                    <?= esc($solution['title']) ?>
                </h1>

                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-500">
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>Dibuat: <?= $solution['created_at'] ? date('d M Y, H:i', strtotime($solution['created_at'])) : '-' ?></span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>Diperbarui: <?= $solution['updated_at'] ? date('d M Y, H:i', strtotime($solution['updated_at'])) : '-' ?></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- BLOK 1: Gejala Masalah (Symptoms) -->
            <section class="bg-white rounded-2xl border border-amber-200/90 overflow-hidden shadow-2xs">
                <div class="bg-amber-50/90 border-b border-amber-200/80 px-6 py-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-2xs">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">1. Gejala Masalah di Lapangan (Symptoms)</h2>
                        <p class="text-xs text-slate-600">Kondisi atau indikasi error yang dialami oleh pengguna / perangkat</p>
                    </div>
                </div>
                <div class="p-6 text-sm sm:text-base text-slate-700 leading-relaxed whitespace-pre-line">
                    <?= esc($solution['symptoms']) ?>
                </div>
            </section>

            <!-- BLOK 2: Akar Penyebab (Root Cause) -->
            <section class="bg-white rounded-2xl border border-rose-200/90 overflow-hidden shadow-2xs">
                <div class="bg-rose-50/80 border-b border-rose-200/80 px-6 py-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                        <i data-lucide="cpu" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">2. Akar Penyebab Teknis (Root Cause)</h2>
                        <p class="text-xs text-slate-600">Penyebab utama terjadinya kendala dari sisi teknis sistem/perangkat</p>
                    </div>
                </div>
                <div class="p-6 text-sm sm:text-base text-slate-700 leading-relaxed whitespace-pre-line">
                    <?= esc($solution['root_cause']) ?>
                </div>
            </section>

            <!-- BLOK 3: Langkah-Langkah Penyelesaian (Step-by-Step Resolution) -->
            <section class="bg-white rounded-2xl border border-emerald-200/90 overflow-hidden shadow-2xs">
                <div class="bg-emerald-50/80 border-b border-emerald-200/80 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                            <i data-lucide="check-square" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">3. Langkah-Langkah Penyelesaian (Step-by-Step)</h2>
                            <p class="text-xs text-slate-600">Ikuti urutan instruksi perbaikan di bawah ini secara berurutan</p>
                        </div>
                    </div>

                    <button type="button"
                            @click="navigator.clipboard.writeText(<?= json_encode($solution['steps']) ?>); copied = true; setTimeout(() => copied = false, 2000)"
                            class="no-print inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-100 transition-colors self-start sm:self-auto">
                        <i data-lucide="copy" class="w-3.5 h-3.5" x-show="!copied"></i>
                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600" x-show="copied" x-cloak></i>
                        <span x-text="copied ? 'Tersalin!' : 'Salin Langkah'"></span>
                    </button>
                </div>

                <div class="p-6">
                    <?php if (count($stepItems) > 1) : ?>
                        <ol class="space-y-4">
                            <?php foreach ($stepItems as $idx => $stepText) : ?>
                                <li class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-emerald-300 transition-colors">
                                    <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                                        <?= $idx + 1 ?>
                                    </span>
                                    <div class="text-sm sm:text-base text-slate-800 leading-relaxed">
                                        <?= esc($stepText) ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else : ?>
                        <div class="text-sm sm:text-base text-slate-800 leading-relaxed whitespace-pre-line">
                            <?= esc($solution['steps']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- BLOK 4: Foto / Screenshot Panduan Teknis -->
            <section class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                            <i data-lucide="image" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">4. Lampiran Foto / Screenshot Teknis</h2>
                            <p class="text-xs text-slate-600">Dokumentasi visual topologi, letak port, atau tampilan konfigurasi</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <?php if (! empty($solution['attachment'])) : ?>
                        <div class="space-y-3">
                            <div class="relative group rounded-xl overflow-hidden border border-slate-200 bg-slate-100">
                                <img src="<?= base_url($solution['attachment']) ?>"
                                     alt="<?= esc($solution['title']) ?>"
                                     @click="showImageModal = true"
                                     class="w-full max-h-[480px] object-contain mx-auto cursor-zoom-in">
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-500 no-print">
                                <span>Klik pada gambar untuk memperbesar tampilan penuh.</span>
                                <a href="<?= base_url($solution['attachment']) ?>" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 font-semibold text-brand-600 hover:underline">
                                    <span>Buka di Tab Baru</span>
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/60 p-6 text-center text-xs sm:text-sm text-slate-500">
                            Belum ada screenshot atau gambar teknis yang dilampirkan pada solusi ini.
                            <a href="<?= site_url('solutions/' . $solution['id'] . '/edit') ?>" class="font-semibold text-brand-600 hover:underline ml-1 no-print">
                                Tambahkan gambar &rarr;
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </div>

        <!-- Sidebar Kanan (4 Kolom): Panel Aksi & Solusi Terkait -->
        <aside class="lg:col-span-4 space-y-6 no-print">

            <!-- Panel Manajemen Dokumentasi -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Tindakan Dokumentasi</h3>

                <!-- Edit Button -->
                <a href="<?= site_url('solutions/' . $solution['id'] . '/edit') ?>"
                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-brand-600 hover:bg-brand-700 text-white transition-colors shadow-xs">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    <span>Edit Dokumentasi Solusi</span>
                </a>

                <!-- Toggle Pin Quick Fix -->
                <form action="<?= site_url('solutions/' . $solution['id'] . '/toggle-pin') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border transition-colors <?= (int) $solution['is_pinned'] === 1 ? 'bg-amber-50 border-amber-300 text-amber-800 hover:bg-amber-100' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
                        <i data-lucide="pin" class="w-4 h-4"></i>
                        <span><?= (int) $solution['is_pinned'] === 1 ? 'Lepas dari Quick Fix' : 'Sematkan ke Quick Fix' ?></span>
                    </button>
                </form>

                <!-- Print Button -->
                <button type="button"
                        @click="window.print()"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Panduan / PDF</span>
                </button>

                <!-- Delete Button -->
                <div class="pt-2 border-t border-slate-100">
                    <button type="button"
                            @click="showDeleteModal = true"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        <span>Hapus Solusi Ini</span>
                    </button>
                </div>
            </div>

            <!-- Daftar Solusi Terkait -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Solusi Terkait</h3>

                <?php if (! empty($relatedSolutions)) : ?>
                    <div class="space-y-3">
                        <?php foreach ($relatedSolutions as $rel) : ?>
                            <a href="<?= site_url('solutions/' . $rel['id']) ?>"
                               class="block p-3 rounded-xl border border-slate-100 hover:border-brand-300 hover:bg-brand-50/30 transition-all group">
                                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 mb-1">
                                    <span><?= esc($rel['category_name']) ?></span>
                                    <span>&rsaquo;</span>
                                    <span class="text-brand-700"><?= esc($rel['sub_category_name']) ?></span>
                                </div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-brand-600 line-clamp-2">
                                    <?= esc($rel['title']) ?>
                                </h4>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="text-xs text-slate-500">Belum ada dokumentasi solusi lain pada kategori ini.</p>
                <?php endif; ?>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="<?= site_url('/') ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-800">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                        <span>Kembali ke Daftar Semua Solusi</span>
                    </a>
                </div>
            </div>

        </aside>

    </div>

    <!-- Modal Konfirmasi Hapus Solusi -->
    <div x-show="showDeleteModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showDeleteModal = false"
             x-transition
             class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
            <div class="w-11 h-11 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                <i data-lucide="alert-octagon" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Hapus Dokumentasi Solusi?</h3>
            <p class="mt-1.5 text-sm text-slate-600">
                Apakah Anda yakin ingin menghapus solusi <strong>"<?= esc($solution['title']) ?>"</strong>? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button"
                        @click="showDeleteModal = false"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                    Batal
                </button>
                <form action="<?= site_url('solutions/' . $solution['id'] . '/delete') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white transition-colors">
                        Ya, Hapus Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal untuk Preview Gambar Lampiran -->
    <?php if (! empty($solution['attachment'])) : ?>
        <div x-show="showImageModal" x-cloak
             @keydown.escape.window="showImageModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-xs">
            <div class="relative max-w-5xl w-full">
                <button type="button"
                        @click="showImageModal = false"
                        class="absolute -top-10 right-0 text-white hover:text-slate-300 text-sm font-semibold inline-flex items-center gap-1">
                    <span>Tutup (ESC)</span>
                </button>
                <img src="<?= base_url($solution['attachment']) ?>"
                     alt="<?= esc($solution['title']) ?>"
                     @click.away="showImageModal = false"
                     class="w-full max-h-[85vh] object-contain rounded-xl shadow-2xl bg-slate-900">
            </div>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
