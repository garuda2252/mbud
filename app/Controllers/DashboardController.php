<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\SolutionModel;
use App\Models\SubCategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    protected CategoryModel $categoryModel;
    protected SubCategoryModel $subCategoryModel;
    protected SolutionModel $solutionModel;

    public function __construct()
    {
        $this->categoryModel    = new CategoryModel();
        $this->subCategoryModel = new SubCategoryModel();
        $this->solutionModel    = new SolutionModel();
    }

    /**
     * Menampilkan halaman utama Knowledge Base:
     * - Search Bar Instan
     * - Daftar Solusi Quick Fix (is_pinned = 1)
     * - Filter Kategori & Sub-Kategori
     * - Daftar Seluruh Solusi
     */
    public function index(): string
    {
        $keyword       = trim((string) $this->request->getGet('q'));
        $categoryId    = $this->request->getGet('category_id') ? (int) $this->request->getGet('category_id') : null;
        $subCategoryId = $this->request->getGet('sub_category_id') ? (int) $this->request->getGet('sub_category_id') : null;

        $categories      = $this->categoryModel->getCategoriesWithCounts();
        $subCategories   = $this->subCategoryModel->getWithCategory();
        $pinnedSolutions = $this->solutionModel->getSolutionsWithRelations(null, null, null, true);
        $solutions       = $this->solutionModel->getSolutionsWithRelations($keyword, $categoryId, $subCategoryId, false);

        $data = [
            'title'                 => 'Beranda & Pencarian Solusi — IT Knowledge Base',
            'activeMenu'            => 'dashboard',
            'categories'            => $categories,
            'subCategories'         => $subCategories,
            'pinnedSolutions'       => $pinnedSolutions,
            'solutions'             => $solutions,
            'initialKeyword'        => $keyword,
            'initialCategoryId'     => $categoryId,
            'initialSubCategoryId'  => $subCategoryId,
            'totalSolutionsCount'   => $this->solutionModel->countAllResults(),
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Endpoint AJAX/JSON untuk live search & filter berdasarkan kategori dan sub-kategori
     * tanpa perlu reload halaman.
     */
    public function search(): ResponseInterface
    {
        $keyword       = trim((string) $this->request->getGet('q'));
        $categoryId    = $this->request->getGet('category_id') ? (int) $this->request->getGet('category_id') : null;
        $subCategoryId = $this->request->getGet('sub_category_id') ? (int) $this->request->getGet('sub_category_id') : null;

        $solutions = $this->solutionModel->getSolutionsWithRelations($keyword, $categoryId, $subCategoryId, false);

        $formatted = array_map(static function (array $row): array {
            return [
                'id'                => (int) $row['id'],
                'sub_category_id'   => (int) $row['sub_category_id'],
                'category_id'       => (int) $row['category_id'],
                'title'             => $row['title'],
                'symptoms'          => $row['symptoms'],
                'root_cause'        => $row['root_cause'],
                'steps'             => $row['steps'],
                'attachment'        => $row['attachment'] ? base_url($row['attachment']) : null,
                'has_attachment'    => ! empty($row['attachment']),
                'is_pinned'         => (int) $row['is_pinned'] === 1,
                'category_name'     => $row['category_name'],
                'category_slug'     => $row['category_slug'],
                'category_icon'     => $row['category_icon'] ?? 'folder',
                'sub_category_name' => $row['sub_category_name'],
                'sub_category_slug' => $row['sub_category_slug'],
                'updated_at'        => $row['updated_at'] ? date('d M Y, H:i', strtotime($row['updated_at'])) : '-',
                'detail_url'        => site_url('solutions/' . $row['id']),
                'edit_url'          => site_url('solutions/' . $row['id'] . '/edit'),
            ];
        }, $solutions);

        return $this->response->setJSON([
            'status'  => 'success',
            'count'   => count($formatted),
            'filters' => [
                'q'               => $keyword,
                'category_id'     => $categoryId,
                'sub_category_id' => $subCategoryId,
            ],
            'data'    => $formatted,
        ]);
    }

    /**
     * Menampilkan halaman detail lengkap dari satu solusi:
     * Gejala, Akar Penyebab, Langkah Penyelesaian, dan Gambar Lampiran.
     */
    public function detail(int $id): string
    {
        $solution = $this->solutionModel->getSolutionDetail($id);

        if (! $solution) {
            throw PageNotFoundException::forPageNotFound('Data solusi dengan ID #' . $id . ' tidak ditemukan.');
        }

        $relatedSolutions = $this->solutionModel->getRelatedSolutions(
            (int) $solution['id'],
            (int) $solution['sub_category_id'],
            (int) $solution['category_id'],
            4
        );

        $data = [
            'title'            => $solution['title'] . ' — IT Knowledge Base',
            'activeMenu'       => 'dashboard',
            'solution'         => $solution,
            'relatedSolutions' => $relatedSolutions,
        ];

        return view('solutions/detail', $data);
    }
}
