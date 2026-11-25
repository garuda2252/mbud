<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="masterDataManager()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 mb-1">
                <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                <span>Manajemen Master Data Taksonomi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                Kelola Kategori & Sub-Kategori Masalah
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Atur pengelompokan kategori utama (misal: Hardware, Jaringan, Software, CCTV) beserta sub-kategori perangkatnya.
            </p>
        </div>

        <a href="<?= site_url('solutions/create') ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-brand-600 hover:bg-brand-700 text-white shadow-xs self-start sm:self-auto">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tambah Solusi Baru</span>
        </a>
    </div>

    <!-- Layout Dua Kolom: Kiri (Kategori Utama) & Kanan (Sub-Kategori) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- ================================================================
             KOLOM KIRI (5 Kolom): KATEGORI UTAMA
        ================================================================= -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Form Tambah Kategori Baru -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-5 py-4 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center">
                        <i data-lucide="folder-plus" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Tambah Kategori Utama</h2>
                        <p class="text-[11px] text-slate-500">Contoh: Software / Aplikasi, Server, CCTV, Telepon IP</p>
                    </div>
                </div>

                <form action="<?= site_url('master/categories') ?>" method="post" class="p-5 space-y-4">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Kategori <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   required
                                   maxlength="100"
                                   placeholder="Misal: Software / Aplikasi"
                                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Icon
                            </label>
                            <select name="icon"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:outline-none">
                                <option value="cpu">cpu (Hardware)</option>
                                <option value="wifi">wifi (Jaringan)</option>
                                <option value="monitor">monitor (Software/OS)</option>
                                <option value="server">server (Server/DB)</option>
                                <option value="video">video (CCTV)</option>
                                <option value="printer">printer (Printer)</option>
                                <option value="shield">shield (Keamanan)</option>
                                <option value="folder">folder (Umum)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Deskripsi Singkat <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea name="description"
                                  rows="2"
                                  placeholder="Ringkasan ruang lingkup kategori masalah ini..."
                                  class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-slate-900 hover:bg-brand-600 text-white transition-colors">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Simpan Kategori Baru</span>
                    </button>
                </form>
            </div>

            <!-- Daftar Kategori Utama -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Daftar Kategori Terdaftar</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                        <?= count($categories) ?> Kategori
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    <?php if (empty($categories)) : ?>
                        <div class="p-8 text-center text-xs text-slate-500">
                            Belum ada data kategori. Silakan tambahkan kategori baru pada formulir di atas.
                        </div>
                    <?php else : ?>
                        <?php foreach ($categories as $cat) : ?>
                            <div class="p-4 sm:p-5 hover:bg-slate-50/80 transition-colors">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 border border-brand-200 flex items-center justify-center shrink-0 mt-0.5">
                                            <i data-lucide="<?= esc($cat['icon'] ?: 'folder') ?>" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-sm font-bold text-slate-900"><?= esc($cat['name']) ?></h4>
                                                <span class="text-[11px] font-mono text-slate-400">/<?= esc($cat['slug']) ?></span>
                                            </div>
                                            <?php if (! empty($cat['description'])) : ?>
                                                <p class="text-xs text-slate-500 mt-1 line-clamp-2"><?= esc($cat['description']) ?></p>
                                            <?php endif; ?>

                                            <div class="mt-2.5 flex flex-wrap items-center gap-2 text-[11px]">
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold">
                                                    <?= (int) $cat['total_sub_categories'] ?> Sub-Kategori
                                                </span>
                                                <span class="px-2 py-0.5 rounded-md bg-brand-50 text-brand-700 font-semibold">
                                                    <?= (int) $cat['total_solutions'] ?> Solusi
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tombol Edit & Hapus Kategori -->
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button type="button"
                                                @click="openEditCategory(<?= htmlspecialchars(json_encode([
                                                    'id'          => (int) $cat['id'],
                                                    'name'        => $cat['name'],
                                                    'icon'        => $cat['icon'] ?: 'folder',
                                                    'description' => $cat['description'] ?? '',
                                                ]), ENT_QUOTES, 'UTF-8') ?>)"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition-colors"
                                                title="Edit Kategori">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </button>

                                        <form action="<?= site_url('master/categories/' . $cat['id'] . '/delete') ?>"
                                              method="post"
                                              onsubmit="return confirm('Hapus kategori \'<?= esc(addslashes($cat['name'])) ?>\'? Seluruh sub-kategori dan solusi di dalamnya juga akan ikut terhapus.')">
                                            <?= csrf_field() ?>
                                            <button type="submit"
                                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                    title="Hapus Kategori">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- ================================================================
             KOLOM KANAN (7 Kolom): SUB-KATEGORI
        ================================================================= -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Form Tambah Sub-Kategori Baru -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-5 py-4 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center">
                        <i data-lucide="git-branch-plus" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Tambah Sub-Kategori Baru</h2>
                        <p class="text-[11px] text-slate-500">Contoh: Printer, Kabel, WiFi, Router / LAN, Email Outlook, ERP</p>
                    </div>
                </div>

                <form action="<?= site_url('master/sub-categories') ?>" method="post" class="p-5">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        <div class="sm:col-span-5">
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Kategori Induk <span class="text-rose-500">*</span>
                            </label>
                            <select name="category_id"
                                    required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($categories as $cat) : ?>
                                    <option value="<?= (int) $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="sm:col-span-5">
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Sub-Kategori <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   required
                                   maxlength="100"
                                   placeholder="Misal: Printer / Scanner"
                                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 focus:outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Tambah</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Daftar Sub-Kategori dengan Filter Cepat -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-900">Daftar Sub-Kategori</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            <?= count($subCategories) ?> Total
                        </span>
                    </div>

                    <!-- Filter Pill Kategori di Tabel Sub-Kategori -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <button type="button"
                                @click="filterCatId = 0"
                                :class="filterCatId === 0 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors">
                            Semua
                        </button>
                        <?php foreach ($categories as $cat) : ?>
                            <button type="button"
                                    @click="filterCatId = <?= (int) $cat['id'] ?>"
                                    :class="filterCatId === <?= (int) $cat['id'] ?> ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors">
                                <?= esc($cat['name']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3 px-5">Sub-Kategori</th>
                                <th class="py-3 px-4">Kategori Induk</th>
                                <th class="py-3 px-4 text-center">Jumlah Solusi</th>
                                <th class="py-3 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if (empty($subCategories)) : ?>
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-xs text-slate-500">
                                        Belum ada data sub-kategori terdaftar.
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($subCategories as $sub) : ?>
                                    <tr x-show="filterCatId === 0 || filterCatId === <?= (int) $sub['category_id'] ?>"
                                        class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-3.5 px-5">
                                            <div class="font-bold text-slate-900"><?= esc($sub['name']) ?></div>
                                            <div class="text-[11px] font-mono text-slate-400">/<?= esc($sub['slug']) ?></div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200">
                                                <?= esc($sub['category_name']) ?>
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <a href="<?= site_url('/?category_id=' . (int) $sub['category_id'] . '&sub_category_id=' . (int) $sub['id']) ?>"
                                               class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 hover:bg-brand-100 text-slate-700 hover:text-brand-700 transition-colors">
                                                <span><?= (int) $sub['total_solutions'] ?> Solusi</span>
                                            </a>
                                        </td>
                                        <td class="py-3.5 px-5 text-right">
                                            <div class="inline-flex items-center justify-end gap-1">
                                                <button type="button"
                                                        @click="openEditSubCategory(<?= htmlspecialchars(json_encode([
                                                            'id'          => (int) $sub['id'],
                                                            'category_id' => (int) $sub['category_id'],
                                                            'name'        => $sub['name'],
                                                        ]), ENT_QUOTES, 'UTF-8') ?>)"
                                                        class="p-1.5 rounded-lg text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition-colors"
                                                        title="Edit Sub-Kategori">
                                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                                </button>

                                                <form action="<?= site_url('master/sub-categories/' . $sub['id'] . '/delete') ?>"
                                                      method="post"
                                                      onsubmit="return confirm('Hapus sub-kategori \'<?= esc(addslashes($sub['name'])) ?>\' beserta solusi di dalamnya?')">
                                                    <?= csrf_field() ?>
                                                    <button type="submit"
                                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                            title="Hapus Sub-Kategori">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <!-- Modal Edit Kategori -->
    <div x-show="editCategoryModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="editCategoryModal = false"
             x-transition
             class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Edit Kategori Utama</h3>
                <button type="button" @click="editCategoryModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'<?= site_url('master/categories') ?>/' + editingCategory.id + '/update'" method="post" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kategori</label>
                    <input type="text" name="name" x-model="editingCategory.name" required
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Icon</label>
                    <select name="icon" x-model="editingCategory.icon"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:outline-none">
                        <option value="cpu">cpu (Hardware)</option>
                        <option value="wifi">wifi (Jaringan)</option>
                        <option value="monitor">monitor (Software/OS)</option>
                        <option value="server">server (Server/DB)</option>
                        <option value="video">video (CCTV)</option>
                        <option value="printer">printer (Printer)</option>
                        <option value="shield">shield (Keamanan)</option>
                        <option value="folder">folder (Umum)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" x-model="editingCategory.description"
                              class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="editCategoryModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-brand-600 hover:bg-brand-700 text-white">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Sub-Kategori -->
    <div x-show="editSubCategoryModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="editSubCategoryModal = false"
             x-transition
             class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Edit Sub-Kategori</h3>
                <button type="button" @click="editSubCategoryModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'<?= site_url('master/sub-categories') ?>/' + editingSubCategory.id + '/update'" method="post" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Induk</label>
                    <select name="category_id" x-model.number="editingSubCategory.category_id" required
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-800 focus:border-brand-500 focus:outline-none">
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?= (int) $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Sub-Kategori</label>
                    <input type="text" name="name" x-model="editingSubCategory.name" required
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="editSubCategoryModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function masterDataManager() {
        return {
            filterCatId: 0,
            editCategoryModal: false,
            editSubCategoryModal: false,
            editingCategory: { id: 0, name: '', icon: 'folder', description: '' },
            editingSubCategory: { id: 0, category_id: 0, name: '' },

            openEditCategory(cat) {
                this.editingCategory = { ...cat };
                this.editCategoryModal = true;
            },

            openEditSubCategory(sub) {
                this.editingSubCategory = { ...sub };
                this.editSubCategoryModal = true;
            }
        };
    }
</script>
<?= $this->endSection() ?>
