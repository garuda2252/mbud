<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\SubCategoryModel;
use CodeIgniter\HTTP\RedirectResponse;

class MasterDataController extends BaseController
{
    protected CategoryModel $categoryModel;
    protected SubCategoryModel $subCategoryModel;

    public function __construct()
    {
        $this->categoryModel    = new CategoryModel();
        $this->subCategoryModel = new SubCategoryModel();
    }

    /**
     * Menampilkan halaman manajemen master Kategori dan Sub-Kategori dalam tampilan dua kolom.
     */
    public function index(): string
    {
        $categories    = $this->categoryModel->getCategoriesWithCounts();
        $subCategories = $this->subCategoryModel->getWithCategory();

        $data = [
            'title'         => 'Kelola Kategori & Sub-Kategori — IT Knowledge Base',
            'activeMenu'    => 'master',
            'categories'    => $categories,
            'subCategories' => $subCategories,
        ];

        return view('master/index', $data);
    }

    /**
     * Menyimpan kategori baru.
     */
    public function storeCategory(): RedirectResponse
    {
        $name        = trim((string) $this->request->getPost('name'));
        $icon        = trim((string) $this->request->getPost('icon')) ?: 'folder';
        $description = trim((string) $this->request->getPost('description'));

        if ($name === '') {
            return redirect()->to(site_url('master'))->withInput()->with('error', 'Nama kategori wajib diisi.');
        }

        $slug = $this->generateUniqueSlug($name, 'categories');

        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'icon'        => $icon,
            'description' => $description !== '' ? $description : null,
        ];

        if (! $this->categoryModel->insert($data)) {
            return redirect()->to(site_url('master'))
                ->withInput()
                ->with('errors', $this->categoryModel->errors())
                ->with('error', 'Gagal menambahkan kategori baru.');
        }

        return redirect()->to(site_url('master'))
            ->with('success', 'Kategori "' . $name . '" berhasil ditambahkan.');
    }

    /**
     * Memperbarui kategori yang sudah ada.
     */
    public function updateCategory(int $id): RedirectResponse
    {
        $category = $this->categoryModel->find($id);
        if (! $category) {
            return redirect()->to(site_url('master'))->with('error', 'Data kategori tidak ditemukan.');
        }

        $name        = trim((string) $this->request->getPost('name'));
        $icon        = trim((string) $this->request->getPost('icon')) ?: 'folder';
        $description = trim((string) $this->request->getPost('description'));

        if ($name === '') {
            return redirect()->to(site_url('master'))->with('error', 'Nama kategori wajib diisi.');
        }

        $slug = ($name !== $category['name'])
            ? $this->generateUniqueSlug($name, 'categories', $id)
            : $category['slug'];

        $this->categoryModel->skipValidation(true)->update($id, [
            'name'        => $name,
            'slug'        => $slug,
            'icon'        => $icon,
            'description' => $description !== '' ? $description : null,
        ]);

        return redirect()->to(site_url('master'))
            ->with('success', 'Kategori "' . $name . '" berhasil diperbarui.');
    }

    /**
     * Menghapus kategori (beserta sub-kategori & solusi terkait melalui ON DELETE CASCADE).
     */
    public function deleteCategory(int $id): RedirectResponse
    {
        $category = $this->categoryModel->find($id);
        if (! $category) {
            return redirect()->to(site_url('master'))->with('error', 'Data kategori tidak ditemukan.');
        }

        $this->categoryModel->delete($id);

        return redirect()->to(site_url('master'))
            ->with('success', 'Kategori "' . $category['name'] . '" beserta data terkait berhasil dihapus.');
    }

    /**
     * Menyimpan sub-kategori baru.
     */
    public function storeSubCategory(): RedirectResponse
    {
        $categoryId = (int) $this->request->getPost('category_id');
        $name       = trim((string) $this->request->getPost('name'));

        if ($categoryId <= 0 || $name === '') {
            return redirect()->to(site_url('master'))
                ->withInput()
                ->with('error', 'Kategori induk dan nama sub-kategori wajib diisi.');
        }

        $slug = $this->generateUniqueSlug($name, 'sub_categories');

        $data = [
            'category_id' => $categoryId,
            'name'        => $name,
            'slug'        => $slug,
        ];

        if (! $this->subCategoryModel->insert($data)) {
            return redirect()->to(site_url('master'))
                ->withInput()
                ->with('errors', $this->subCategoryModel->errors())
                ->with('error', 'Gagal menambahkan sub-kategori baru.');
        }

        return redirect()->to(site_url('master'))
            ->with('success', 'Sub-kategori "' . $name . '" berhasil ditambahkan.');
    }

    /**
     * Memperbarui sub-kategori yang sudah ada.
     */
    public function updateSubCategory(int $id): RedirectResponse
    {
        $subCategory = $this->subCategoryModel->find($id);
        if (! $subCategory) {
            return redirect()->to(site_url('master'))->with('error', 'Data sub-kategori tidak ditemukan.');
        }

        $categoryId = (int) $this->request->getPost('category_id');
        $name       = trim((string) $this->request->getPost('name'));

        if ($categoryId <= 0 || $name === '') {
            return redirect()->to(site_url('master'))->with('error', 'Kategori induk dan nama sub-kategori wajib diisi.');
        }

        $slug = ($name !== $subCategory['name'])
            ? $this->generateUniqueSlug($name, 'sub_categories', $id)
            : $subCategory['slug'];

        $this->subCategoryModel->skipValidation(true)->update($id, [
            'category_id' => $categoryId,
            'name'        => $name,
            'slug'        => $slug,
        ]);

        return redirect()->to(site_url('master'))
            ->with('success', 'Sub-kategori "' . $name . '" berhasil diperbarui.');
    }

    /**
     * Menghapus sub-kategori (beserta solusi di dalamnya melalui ON DELETE CASCADE).
     */
    public function deleteSubCategory(int $id): RedirectResponse
    {
        $subCategory = $this->subCategoryModel->find($id);
        if (! $subCategory) {
            return redirect()->to(site_url('master'))->with('error', 'Data sub-kategori tidak ditemukan.');
        }

        $this->subCategoryModel->delete($id);

        return redirect()->to(site_url('master'))
            ->with('success', 'Sub-kategori "' . $subCategory['name'] . '" berhasil dihapus.');
    }
}
