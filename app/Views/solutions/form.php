<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$selectedSubCategoryId = (int) old('sub_category_id', $solution['sub_category_id'] ?? 0);
$selectedCategoryId    = (int) old('category_id', $solution['category_id'] ?? 0);

// Jika category_id belum terset tetapi sub_category_id sudah ada, cari category_id-nya
if ($selectedCategoryId === 0 && $selectedSubCategoryId > 0) {
    foreach ($subCategories as $sub) {
        if ((int) $sub['id'] === $selectedSubCategoryId) {
            $selectedCategoryId = (int) $sub['category_id'];
            break;
        }
    }
}

$subCategoriesForJs = array_map(static function (array $sub): array {
    return [
        'id'          => (int) $sub['id'],
        'category_id' => (int) $sub['category_id'],
        'name'        => $sub['name'],
    ];
}, $subCategories);

$formAction = $isEdit
    ? site_url('solutions/' . $solution['id'] . '/update')
    : site_url('solutions');
?>

<div x-data="solutionFormHandler()" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header & Navigasi Kembali -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <a href="<?= $isEdit ? site_url('solutions/' . $solution['id']) : site_url('/') ?>"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-800 mb-2">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span><?= $isEdit ? 'Kembali ke Detail Solusi' : 'Kembali ke Beranda' ?></span>
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900">
                <?= $isEdit ? 'Edit Dokumentasi Solusi' : 'Tambah Dokumentasi Solusi Baru' ?>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Dokumentasikan gejala masalah, akar penyebab, serta langkah perbaikan teknis secara terstruktur.
            </p>
        </div>
    </div>

    <!-- Form Utama -->
    <form action="<?= $formAction ?>" method="post" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <?= csrf_field() ?>

        <div class="p-6 sm:p-8 space-y-6">

            <!-- 1. Pilihan Kategori & Sub-Kategori Dinamis -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                <!-- Dropdown Kategori Utama -->
                <div>
                    <label for="category_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        1. Kategori Masalah <span class="text-rose-500">*</span>
                    </label>
                    <select id="category_id"
                            name="category_id"
                            x-model.number="categoryId"
                            @change="onCategoryChange()"
                            required
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none">
                        <option value="0">-- Pilih Kategori Utama --</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?= (int) $cat['id'] ?>">
                                <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="mt-1.5 text-[11px] text-slate-500">
                        Belum ada kategori yang sesuai?
                        <a href="<?= site_url('master') ?>" class="text-brand-600 font-semibold hover:underline">+ Kelola Kategori</a>
                    </p>
                </div>

                <!-- Dropdown Sub-Kategori (Otomatis Terfilter) -->
                <div>
                    <label for="sub_category_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        2. Sub-Kategori / Perangkat <span class="text-rose-500">*</span>
                    </label>
                    <select id="sub_category_id"
                            name="sub_category_id"
                            x-model.number="subCategoryId"
                            :disabled="categoryId === 0 || availableSubCategories.length === 0"
                            required
                            class="w-full rounded-xl border border-slate-300 bg-white disabled:bg-slate-100 disabled:text-slate-400 px-3.5 py-2.5 text-sm font-medium text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none">
                        <option value="0" x-text="categoryId === 0 ? '-- Pilih Kategori Terlebih Dahulu --' : (availableSubCategories.length === 0 ? '-- Belum Ada Sub-Kategori --' : '-- Pilih Sub-Kategori --')"></option>
                        <template x-for="sub in availableSubCategories" :key="sub.id">
                            <option :value="sub.id" :selected="sub.id === subCategoryId" x-text="sub.name"></option>
                        </template>
                    </select>
                    <p class="mt-1.5 text-[11px] text-slate-500">
                        Pilihan sub-kategori menyesuaikan kategori utama secara otomatis.
                    </p>
                </div>
            </div>

            <!-- 2. Judul Masalah -->
            <div>
                <label for="title" class="block text-sm font-bold text-slate-800 mb-1.5">
                    Judul Singkat Masalah <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="title"
                       name="title"
                       value="<?= esc(old('title', $solution['title'] ?? '')) ?>"
                       required
                       minlength="5"
                       maxlength="200"
                       placeholder="Contoh: Printer Tidak Merespons Perintah Cetak (Offline/Spooler Hang)"
                       class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none">
            </div>

            <!-- 3. Gejala Masalah (Symptoms) -->
            <div>
                <label for="symptoms" class="block text-sm font-bold text-slate-800 mb-1">
                    Gejala Masalah di Lapangan (Symptoms) <span class="text-rose-500">*</span>
                </label>
                <p class="text-xs text-slate-500 mb-2">
                    Jelaskan tanda-tanda, pesan error, atau keluhan yang terlihat dari sisi pengguna.
                </p>
                <textarea id="symptoms"
                          name="symptoms"
                          rows="3"
                          required
                          placeholder="Contoh: Perangkat terhubung ke SSID WiFi kantor, namun muncul status 'No Internet' atau halaman web tidak dapat diakses..."
                          class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none"><?= esc(old('symptoms', $solution['symptoms'] ?? '')) ?></textarea>
            </div>

            <!-- 4. Akar Penyebab (Root Cause) -->
            <div>
                <label for="root_cause" class="block text-sm font-bold text-slate-800 mb-1">
                    Akar Penyebab Teknis (Root Cause) <span class="text-rose-500">*</span>
                </label>
                <p class="text-xs text-slate-500 mb-2">
                    Jelaskan penyebab teknis mengapa masalah tersebut terjadi.
                </p>
                <textarea id="root_cause"
                          name="root_cause"
                          rows="3"
                          required
                          placeholder="Contoh: Klien/perangkat belum terdaftar pada daftar klien resmi router (DHCP Static / MAC Address Binding)..."
                          class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none"><?= esc(old('root_cause', $solution['root_cause'] ?? '')) ?></textarea>
            </div>

            <!-- 5. Langkah-Langkah Penyelesaian (Steps) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="steps" class="block text-sm font-bold text-slate-800">
                        Langkah-Langkah Penyelesaian (Step-by-Step) <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                        Tips: Pisahkan tiap langkah dengan baris baru (Enter)
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-2">
                    Tuliskan urutan tindakan perbaikan secara jelas dari awal hingga pengecekan akhir.
                </p>
                <textarea id="steps"
                          name="steps"
                          rows="6"
                          required
                          placeholder="1. Periksa fisik kabel LAN pada port router&#10;2. Cabut konektor RJ45 dan tunggu 5-10 detik&#10;3. Pasang kembali hingga terdengar bunyi klik dan lampu indikator menyala..."
                          class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-mono text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none"><?= esc(old('steps', $solution['steps'] ?? '')) ?></textarea>
            </div>

            <!-- 6. Upload Gambar Lampiran & Quick Fix Pin -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-100">

                <!-- Upload Gambar Lampiran -->
                <div>
                    <label for="attachment" class="block text-sm font-bold text-slate-800 mb-1.5">
                        Lampiran Gambar / Screenshot Teknis <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                    </label>
                    <input type="file"
                           id="attachment"
                           name="attachment"
                           accept="image/png,image/jpeg,image/jpg,image/webp,image/gif"
                           @change="previewAttachment($event)"
                           class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 border border-slate-300 rounded-xl p-1.5">
                    <p class="mt-1 text-[11px] text-slate-500">Format: JPG, PNG, WEBP, GIF (Maks. 5 MB).</p>

                    <!-- Preview Gambar Baru yang Dipilih -->
                    <div x-show="previewUrl" x-cloak class="mt-3 relative rounded-xl overflow-hidden border border-brand-200 bg-slate-50 p-2">
                        <p class="text-[11px] font-semibold text-brand-700 mb-1">Preview Gambar Baru:</p>
                        <img :src="previewUrl" alt="Preview" class="max-h-40 rounded-lg object-contain mx-auto">
                    </div>

                    <!-- Gambar Saat Ini (Jika Mode Edit) -->
                    <?php if ($isEdit && ! empty($solution['attachment'])) : ?>
                        <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2" x-show="!previewUrl">
                            <p class="text-xs font-semibold text-slate-700">Lampiran Saat Ini:</p>
                            <img src="<?= base_url($solution['attachment']) ?>" alt="Lampiran" class="max-h-36 rounded-lg object-contain border border-slate-200 bg-white p-1">
                            <label class="inline-flex items-center gap-2 text-xs text-rose-600 font-medium cursor-pointer pt-1">
                                <input type="checkbox" name="remove_attachment" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                <span>Hapus gambar lampiran ini</span>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Opsi Pin sebagai Quick Fix -->
                <div>
                    <span class="block text-sm font-bold text-slate-800 mb-1.5">Prioritas & Penanda Quick Fix</span>
                    <label for="is_pinned"
                           class="flex items-start gap-3.5 p-4 rounded-2xl border border-amber-200 bg-amber-50/60 hover:bg-amber-50 cursor-pointer transition-colors">
                        <input type="checkbox"
                               id="is_pinned"
                               name="is_pinned"
                               value="1"
                               <?= old('is_pinned', $solution['is_pinned'] ?? 0) ? 'checked' : '' ?>
                               class="mt-1 h-4 w-4 rounded border-amber-400 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="flex items-center gap-1.5 text-sm font-bold text-slate-900">
                                <i data-lucide="zap" class="w-4 h-4 text-amber-600"></i>
                                <span>Pin sebagai "Quick Fix" (Sering Terjadi)</span>
                            </span>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Aktifkan opsi ini agar solusi ditampilkan di bagian paling atas halaman utama sebagai panduan penanganan cepat untuk masalah yang sering berulang.
                            </p>
                        </div>
                    </label>
                </div>

            </div>

        </div>

        <!-- Form Footer Actions -->
        <div class="bg-slate-50 border-t border-slate-200 px-6 sm:px-8 py-4 flex items-center justify-end gap-3">
            <a href="<?= $isEdit ? site_url('solutions/' . $solution['id']) : site_url('/') ?>"
               class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-brand-600 hover:bg-brand-700 text-white shadow-xs transition-all">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Solusi Baru' ?></span>
            </button>
        </div>

    </form>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function solutionFormHandler() {
        return {
            categoryId: <?= (int) $selectedCategoryId ?>,
            subCategoryId: <?= (int) $selectedSubCategoryId ?>,
            allSubCategories: <?= json_encode($subCategoriesForJs) ?>,
            previewUrl: null,

            get availableSubCategories() {
                if (!this.categoryId || this.categoryId === 0) {
                    return [];
                }
                return this.allSubCategories.filter(item => item.category_id === Number(this.categoryId));
            },

            onCategoryChange() {
                const exists = this.availableSubCategories.some(item => item.id === Number(this.subCategoryId));
                if (!exists) {
                    this.subCategoryId = this.availableSubCategories.length > 0 ? this.availableSubCategories[0].id : 0;
                }
            },

            previewAttachment(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.previewUrl = null;
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewUrl = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };
    }
</script>
<?= $this->endSection() ?>
