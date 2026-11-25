<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
// Siapkan data awal untuk Alpine.js agar live search & filter berjalan instan
$initialSolutionsJson = array_map(static function (array $row): array {
    return [
        'id'                => (int) $row['id'],
        'sub_category_id'   => (int) $row['sub_category_id'],
        'category_id'       => (int) $row['category_id'],
        'title'             => $row['title'],
        'symptoms'          => $row['symptoms'],
        'root_cause'        => $row['root_cause'],
        'steps'             => $row['steps'],
        'attachment'        => ! empty($row['attachment']) ? base_url($row['attachment']) : null,
        'has_attachment'    => ! empty($row['attachment']),
        'is_pinned'         => (int) $row['is_pinned'] === 1,
        'category_name'     => $row['category_name'],
        'category_slug'     => $row['category_slug'],
        'category_icon'     => $row['category_icon'] ?? 'folder',
        'sub_category_name' => $row['sub_category_name'],
        'sub_category_slug' => $row['sub_category_slug'],
        'updated_at'        => ! empty($row['updated_at']) ? date('d M Y, H:i', strtotime($row['updated_at'])) : '-',
        'detail_url'        => site_url('solutions/' . $row['id']),
        'edit_url'          => site_url('solutions/' . $row['id'] . '/edit'),
    ];
}, $solutions);

$subCategoriesJson = array_map(static function (array $sub): array {
    return [
        'id'              => (int) $sub['id'],
        'category_id'     => (int) $sub['category_id'],
        'name'            => $sub['name'],
        'slug'            => $sub['slug'],
        'category_name'   => $sub['category_name'],
        'total_solutions' => (int) ($sub['total_solutions'] ?? 0),
    ];
}, $subCategories);
?>

<div x-data="knowledgeBaseDashboard()" x-init="initDashboard()" class="pb-10">

    <!-- Hero & Instant Search Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-slate-900 via-slate-900 to-brand-950 text-white py-10 sm:py-14 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/15 border border-brand-400/30 text-brand-200 text-xs font-medium mb-4">
                    <i data-lucide="life-buoy" class="w-3.5 h-3.5"></i>
                    <span>Internal IT Troubleshooting Wiki &bull; <?= (int) $totalSolutionsCount ?> Dokumentasi Solusi</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white">
                    Cari Solusi Masalah IT Operasional
                </h1>
                <p class="mt-2.5 text-sm sm:text-base text-slate-300">
                    Ketik gejala masalah, nama perangkat, atau kode error untuk menemukan langkah perbaikan standar secara instan.
                </p>

                <!-- Instant Search Bar -->
                <div class="mt-6 relative">
                    <div class="relative flex items-center">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="search" class="w-5 h-5" x-show="!isLoading"></i>
                            <svg x-show="isLoading" x-cloak class="animate-spin h-5 w-5 text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>

                        <input type="text"
                               x-ref="searchInput"
                               x-model="keyword"
                               @input.debounce.220ms="fetchSolutions()"
                               @keydown.escape="clearSearch()"
                               placeholder="Cth: Printer offline, spooler hang, WiFi no internet, port LAN lambat..."
                               class="w-full pl-12 pr-28 py-4 rounded-2xl bg-white text-slate-900 placeholder-slate-400 text-sm sm:text-base font-medium shadow-xl border-2 border-transparent focus:border-brand-500 focus:outline-none transition-all">

                        <div class="absolute inset-y-0 right-2 flex items-center gap-1.5">
                            <button type="button"
                                    x-show="keyword.length > 0"
                                    x-cloak
                                    @click="clearSearch()"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                                Reset
                            </button>
                            <kbd class="hidden sm:inline-flex items-center px-2 py-1 text-[11px] font-mono font-semibold text-slate-400 bg-slate-100 border border-slate-200 rounded-md">
                                /
                            </kbd>
                        </div>
                    </div>

                    <!-- Quick Search Suggestions -->
                    <div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs text-slate-300">
                        <span class="text-slate-400">Pencarian populer:</span>
                        <button type="button" @click="applyKeyword('Print Spooler')" class="px-2.5 py-1 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">
                            Print Spooler
                        </button>
                        <button type="button" @click="applyKeyword('No Internet')" class="px-2.5 py-1 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">
                            WiFi No Internet
                        </button>
                        <button type="button" @click="applyKeyword('MAC Address')" class="px-2.5 py-1 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">
                            MAC Address Router
                        </button>
                        <button type="button" @click="applyKeyword('LAN Melambat')" class="px-2.5 py-1 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">
                            LAN Putus-Nyambung
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8">

        <!-- Bagian Khusus: QUICK FIX (Sering Terjadi / Pinned Solutions) -->
        <?php if (! empty($pinnedSolutions)) : ?>
            <section x-show="!hasActiveFilter" x-transition class="bg-gradient-to-r from-amber-50/90 via-orange-50/60 to-amber-50/90 border border-amber-200/80 rounded-2xl p-5 sm:p-6 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center shadow-xs">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                        </span>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                                <span>Quick Fix — Kasus Paling Sering Terjadi</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                    <?= count($pinnedSolutions) ?> Pinned
                                </span>
                            </h2>
                            <p class="text-xs text-slate-600">Panduan penanganan cepat untuk kendala operasional harian yang paling sering dilaporkan.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($pinnedSolutions as $pin) : ?>
                        <a href="<?= site_url('solutions/' . $pin['id']) ?>"
                           class="group bg-white hover:bg-amber-50/30 rounded-xl p-4 border border-amber-200/90 hover:border-amber-400 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-1.5 text-xs font-semibold text-brand-700">
                                        <span class="px-2 py-0.5 rounded-md bg-brand-50 border border-brand-200">
                                            <?= esc($pin['category_name']) ?>
                                        </span>
                                        <span class="text-slate-400">&rsaquo;</span>
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                            <?= esc($pin['sub_category_name']) ?>
                                        </span>
                                    </div>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">
                                        <i data-lucide="pin" class="w-3 h-3"></i>
                                        Quick Fix
                                    </span>
                                </div>

                                <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-1">
                                    <?= esc($pin['title']) ?>
                                </h3>

                                <p class="mt-1.5 text-xs text-slate-600 line-clamp-2">
                                    <span class="font-semibold text-slate-700">Gejala:</span>
                                    <?= esc($pin['symptoms']) ?>
                                </p>
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-500 truncate max-w-[70%]">
                                    <strong class="text-slate-700">Solusi:</strong> <?= esc(character_limiter(preg_replace('/\s+/', ' ', $pin['steps']), 75)) ?>
                                </span>
                                <span class="inline-flex items-center gap-1 font-semibold text-brand-600 group-hover:translate-x-0.5 transition-transform shrink-0">
                                    Buka Panduan &rarr;
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Filter Kategori & Sub-Kategori (Pill / Tab) -->
        <section class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-2xs space-y-4">
            <!-- Baris 1: Filter Kategori Utama -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <i data-lucide="folder-tree" class="w-4 h-4 text-brand-600"></i>
                    <span>Filter Kategori Masalah</span>
                </div>

                <button type="button"
                        x-show="hasActiveFilter"
                        x-cloak
                        @click="resetAllFilters()"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 hover:text-rose-700 self-start sm:self-auto">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Tampilkan Semua Solusi</span>
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button"
                        @click="selectCategory(null)"
                        :class="selectedCategory === null
                            ? 'bg-slate-900 text-white border-slate-900 shadow-xs'
                            : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold border transition-all">
                    <span>Semua Kategori</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[11px] bg-white/20"
                          :class="selectedCategory === null ? 'text-white bg-white/20' : 'text-slate-600 bg-slate-200'">
                        <?= (int) $totalSolutionsCount ?>
                    </span>
                </button>

                <?php foreach ($categories as $cat) : ?>
                    <button type="button"
                            @click="selectCategory(<?= (int) $cat['id'] ?>)"
                            :class="selectedCategory === <?= (int) $cat['id'] ?>
                                ? 'bg-brand-600 text-white border-brand-600 shadow-xs'
                                : 'bg-white text-slate-700 border-slate-200 hover:border-brand-300 hover:bg-brand-50/40'"
                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold border transition-all">
                        <span><?= esc($cat['name']) ?></span>
                        <span class="px-1.5 py-0.5 rounded-md text-[11px]"
                              :class="selectedCategory === <?= (int) $cat['id'] ?> ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                            <?= (int) $cat['total_solutions'] ?>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Baris 2: Filter Sub-Kategori (Otomatis menyesuaikan Kategori yang dipilih) -->
            <div class="pt-3 border-t border-slate-100">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-slate-400 mr-1">Sub-Kategori:</span>

                    <button type="button"
                            @click="selectSubCategory(null)"
                            :class="selectedSubCategory === null
                                ? 'bg-brand-100 text-brand-800 border-brand-300 font-semibold'
                                : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'"
                            class="px-3 py-1.5 rounded-lg text-xs border transition-colors">
                        Semua Sub-Kategori
                    </button>

                    <template x-for="sub in filteredSubCategories" :key="sub.id">
                        <button type="button"
                                @click="selectSubCategory(sub.id)"
                                :class="selectedSubCategory === sub.id
                                    ? 'bg-brand-600 text-white border-brand-600 font-semibold shadow-2xs'
                                    : 'bg-white text-slate-700 border-slate-200 hover:border-brand-300 hover:text-brand-700'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border transition-all">
                            <span x-text="sub.name"></span>
                            <span class="text-[10px] opacity-75" x-show="selectedCategory === null" x-text="'(' + sub.category_name + ')'"></span>
                        </button>
                    </template>
                </div>
            </div>
        </section>

        <!-- Daftar Solusi (Grid Card) -->
        <section class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900">Daftar Panduan & Solusi</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-200 text-slate-800" x-text="solutions.length + ' Solusi'"></span>
                </div>

                <div class="text-xs text-slate-500" x-show="keyword.trim() !== ''" x-cloak>
                    Menampilkan hasil pencarian untuk kata kunci: <strong class="text-slate-900" x-text="'\"' + keyword + '\"'"></strong>
                </div>
            </div>

            <!-- Empty State jika hasil pencarian tidak ditemukan -->
            <div x-show="solutions.length === 0" x-cloak
                 class="bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center max-w-xl mx-auto my-6">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="search-x" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">Solusi Tidak Ditemukan</h3>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Tidak ada dokumentasi masalah yang cocok dengan filter atau kata kunci yang Anda masukkan.
                </p>
                <div class="mt-5 flex items-center justify-center gap-3">
                    <button type="button"
                            @click="resetAllFilters()"
                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                        Reset Filter Pencarian
                    </button>
                    <a href="<?= site_url('solutions/create') ?>"
                       class="px-4 py-2 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-700 text-white transition-colors">
                        + Tambah Kasus Baru
                    </a>
                </div>
            </div>

            <!-- Grid Kartu Solusi -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" x-show="solutions.length > 0">
                <template x-for="item in solutions" :key="item.id">
                    <article class="bg-white rounded-2xl border border-slate-200/90 hover:border-brand-400 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between overflow-hidden group">
                        <div class="p-5">
                            <!-- Badge Kategori > Sub-Kategori & Pin Indicator -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                                    <span class="font-semibold px-2.5 py-0.5 rounded-md bg-brand-50 text-brand-700 border border-brand-200/70"
                                          x-text="item.category_name"></span>
                                    <span class="text-slate-400 font-bold">&rsaquo;</span>
                                    <span class="font-medium px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700"
                                          x-text="item.sub_category_name"></span>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <template x-if="item.has_attachment">
                                        <span title="Memiliki Lampiran Gambar/Screenshot"
                                              class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Lampiran
                                        </span>
                                    </template>
                                    <template x-if="item.is_pinned">
                                        <span title="Quick Fix — Kasus Sering Terjadi"
                                              class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                            Quick Fix
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Judul Masalah -->
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-600 transition-colors leading-snug">
                                <a :href="item.detail_url" class="focus:outline-none">
                                    <span x-text="item.title"></span>
                                </a>
                            </h3>

                            <!-- Ringkasan Gejala -->
                            <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600 space-y-1.5">
                                <div>
                                    <span class="font-semibold text-slate-800">Gejala:</span>
                                    <p class="line-clamp-2 mt-0.5 leading-relaxed" x-text="item.symptoms"></p>
                                </div>
                                <div class="pt-1.5 border-t border-slate-200/70">
                                    <span class="font-semibold text-slate-800">Penyebab:</span>
                                    <p class="line-clamp-1 mt-0.5 text-slate-500" x-text="item.root_cause"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="px-5 py-3.5 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <a :href="item.edit_url"
                                   class="text-slate-500 hover:text-slate-800 font-medium px-2 py-1 rounded-md hover:bg-slate-200/60 transition-colors">
                                    Edit
                                </a>
                            </div>

                            <a :href="item.detail_url"
                               class="inline-flex items-center gap-1.5 font-semibold text-brand-600 hover:text-brand-800 group-hover:translate-x-0.5 transition-all">
                                <span>Lihat Detail Solusi</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </article>
                </template>
            </div>
        </section>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function knowledgeBaseDashboard() {
        return {
            keyword: <?= json_encode($initialKeyword ?? '') ?>,
            selectedCategory: <?= json_encode($initialCategoryId) ?>,
            selectedSubCategory: <?= json_encode($initialSubCategoryId) ?>,
            subCategories: <?= json_encode($subCategoriesJson) ?>,
            solutions: <?= json_encode($initialSolutionsJson) ?>,
            isLoading: false,

            get filteredSubCategories() {
                if (this.selectedCategory === null) {
                    return this.subCategories;
                }
                return this.subCategories.filter(sub => sub.category_id === this.selectedCategory);
            },

            get hasActiveFilter() {
                return this.keyword.trim() !== '' || this.selectedCategory !== null || this.selectedSubCategory !== null;
            },

            initDashboard() {
                // Shortcut keyboard '/' untuk langsung fokus ke kotak pencarian
                window.addEventListener('keydown', (e) => {
                    if (e.key === '/' && document.activeElement !== this.$refs.searchInput && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                        e.preventDefault();
                        this.$refs.searchInput.focus();
                    }
                });
            },

            selectCategory(categoryId) {
                this.selectedCategory = categoryId;
                // Reset sub-kategori jika tidak berada di bawah kategori yang baru dipilih
                if (categoryId !== null && this.selectedSubCategory !== null) {
                    const validSub = this.subCategories.find(s => s.id === this.selectedSubCategory && s.category_id === categoryId);
                    if (!validSub) {
                        this.selectedSubCategory = null;
                    }
                }
                this.fetchSolutions();
            },

            selectSubCategory(subCategoryId) {
                this.selectedSubCategory = subCategoryId;
                if (subCategoryId !== null) {
                    const sub = this.subCategories.find(s => s.id === subCategoryId);
                    if (sub) {
                        this.selectedCategory = sub.category_id;
                    }
                }
                this.fetchSolutions();
            },

            applyKeyword(word) {
                this.keyword = word;
                this.fetchSolutions();
            },

            clearSearch() {
                this.keyword = '';
                this.fetchSolutions();
            },

            resetAllFilters() {
                this.keyword = '';
                this.selectedCategory = null;
                this.selectedSubCategory = null;
                this.fetchSolutions();
            },

            async fetchSolutions() {
                this.isLoading = true;
                try {
                    const params = new URLSearchParams();
                    if (this.keyword.trim() !== '') {
                        params.set('q', this.keyword.trim());
                    }
                    if (this.selectedCategory !== null) {
                        params.set('category_id', this.selectedCategory);
                    }
                    if (this.selectedSubCategory !== null) {
                        params.set('sub_category_id', this.selectedSubCategory);
                    }

                    const response = await fetch(`<?= site_url('api/solutions/search') ?>?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const result = await response.json();
                        this.solutions = result.data || [];
                    }
                } catch (err) {
                    console.error('Gagal memuat data pencarian:', err);
                } finally {
                    this.isLoading = false;
                }
            }
        };
    }
</script>
<?= $this->endSection() ?>
