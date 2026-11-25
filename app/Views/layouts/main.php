<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Internal IT Knowledge Base — Troubleshooting Wiki') ?></title>

    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js via CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- Lucide Icons via CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-800 antialiased selection:bg-brand-600 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200/80 shadow-xs no-print" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo & Title -->
                <div class="flex items-center gap-3">
                    <a href="<?= site_url('/') ?>" class="flex items-center gap-3 group focus:outline-none">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-600 to-indigo-700 flex items-center justify-center text-white shadow-sm group-hover:shadow-md transition-all">
                            <i data-lucide="terminal-square" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 tracking-tight text-base sm:text-lg">ITASK Wiki</span>
                                <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-brand-50 text-brand-700 border border-brand-200">
                                    Internal IT KB
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 hidden sm:block">Pusat Solusi & Troubleshooting Operasional</p>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <?php $active = $activeMenu ?? 'dashboard'; ?>
                <nav class="hidden md:flex items-center gap-1.5">
                    <a href="<?= site_url('/') ?>"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors <?= $active === 'dashboard' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Beranda / Cari Solusi</span>
                    </a>

                    <a href="<?= site_url('master') ?>"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors <?= $active === 'master' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                        <i data-lucide="layers" class="w-4 h-4"></i>
                        <span>Kelola Kategori</span>
                    </a>

                    <div class="h-5 w-px bg-slate-200 mx-1.5"></div>

                    <a href="<?= site_url('solutions/create') ?>"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-xs <?= $active === 'create_solution' ? 'bg-brand-700 text-white ring-2 ring-brand-300' : 'bg-brand-600 hover:bg-brand-700 text-white' ?>">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Tambah Solusi</span>
                    </a>
                </nav>

                <!-- Mobile Hamburger Button -->
                <div class="flex md:hidden">
                    <button type="button"
                            @click="mobileOpen = !mobileOpen"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none"
                            aria-label="Toggle Menu">
                        <i data-lucide="menu" class="w-6 h-6" x-show="!mobileOpen"></i>
                        <i data-lucide="x" class="w-6 h-6" x-show="mobileOpen" x-cloak></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileOpen" x-cloak x-transition class="md:hidden border-t border-slate-200 bg-white px-4 pt-2 pb-4 space-y-1.5">
            <a href="<?= site_url('/') ?>"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium <?= $active === 'dashboard' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' ?>">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Beranda / Cari Solusi</span>
            </a>
            <a href="<?= site_url('master') ?>"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium <?= $active === 'master' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' ?>">
                <i data-lucide="layers" class="w-4 h-4"></i>
                <span>Kelola Kategori</span>
            </a>
            <a href="<?= site_url('solutions/create') ?>"
               class="flex items-center justify-center gap-2 w-full mt-2 px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-600 text-white hover:bg-brand-700">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Solusi Baru</span>
            </a>
        </div>
    </header>

    <!-- Flash Notification Area -->
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 mt-4 no-print">
        <?php if (session()->getFlashdata('success')) : ?>
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="mb-4 flex items-start justify-between gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3.5 text-emerald-800 shadow-xs">
                <div class="flex items-center gap-2.5 text-sm font-medium">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
                <button type="button" @click="show = false" class="text-emerald-600 hover:text-emerald-900 p-0.5 rounded-md">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="mb-4 flex items-start justify-between gap-3 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3.5 text-rose-800 shadow-xs">
                <div class="flex items-start gap-2.5 text-sm">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-semibold"><?= esc(session()->getFlashdata('error')) ?></p>
                        <?php $validationErrors = session()->getFlashdata('errors'); ?>
                        <?php if (! empty($validationErrors) && is_array($validationErrors)) : ?>
                            <ul class="mt-1.5 list-disc list-inside text-xs text-rose-700 space-y-0.5">
                                <?php foreach ($validationErrors as $err) : ?>
                                    <li><?= esc($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <button type="button" @click="show = false" class="text-rose-600 hover:text-rose-900 p-0.5 rounded-md">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Page Content -->
    <main class="flex-1">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-16 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="font-semibold text-slate-700">ITASK — Internal IT Knowledge Base</span>
                <span>&bull;</span>
                <span>Troubleshooting Wiki Operasional Perusahaan</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="<?= site_url('/') ?>" class="hover:text-brand-600 transition-colors">Beranda</a>
                <a href="<?= site_url('solutions/create') ?>" class="hover:text-brand-600 transition-colors">Tambah Solusi</a>
                <a href="<?= site_url('master') ?>" class="hover:text-brand-600 transition-colors">Master Kategori</a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
