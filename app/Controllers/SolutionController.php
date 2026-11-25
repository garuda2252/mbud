<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\SolutionModel;
use App\Models\SubCategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class SolutionController extends BaseController
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
     * Menampilkan form tambah solusi baru.
     */
    public function create(): string
    {
        $categories    = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        $subCategories = $this->subCategoryModel->getWithCategory();

        $data = [
            'title'         => 'Tambah Solusi Baru — IT Knowledge Base',
            'activeMenu'    => 'create_solution',
            'isEdit'        => false,
            'solution'      => null,
            'categories'    => $categories,
            'subCategories' => $subCategories,
            'validation'    => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ];

        return view('solutions/form', $data);
    }

    /**
     * Menyimpan data solusi baru ke database beserta upload lampiran gambar.
     */
    public function store(): RedirectResponse
    {
        $rules = [
            'sub_category_id' => 'required|is_natural_no_zero',
            'title'           => 'required|min_length[5]|max_length[200]',
            'symptoms'        => 'required|min_length[10]',
            'root_cause'      => 'required|min_length[10]',
            'steps'           => 'required|min_length[10]',
        ];

        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $rules['attachment'] = 'uploaded[attachment]|max_size[attachment,5120]|is_image[attachment]|mime_in[attachment,image/jpg,image/jpeg,image/png,image/webp,image/gif]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Gagal menyimpan solusi. Mohon periksa kembali isian formulir.');
        }

        $attachmentPath = null;
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/solutions';
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $attachmentPath = 'uploads/solutions/' . $newName;
        }

        $payload = [
            'sub_category_id' => (int) $this->request->getPost('sub_category_id'),
            'title'           => trim((string) $this->request->getPost('title')),
            'symptoms'        => trim((string) $this->request->getPost('symptoms')),
            'root_cause'      => trim((string) $this->request->getPost('root_cause')),
            'steps'           => trim((string) $this->request->getPost('steps')),
            'attachment'      => $attachmentPath,
            'is_pinned'       => $this->request->getPost('is_pinned') ? 1 : 0,
        ];

        $this->solutionModel->insert($payload);
        $newId = $this->solutionModel->getInsertID();

        return redirect()->to(site_url('solutions/' . $newId))
            ->with('success', 'Dokumentasi solusi berhasil ditambahkan ke Knowledge Base.');
    }

    /**
     * Menampilkan form edit untuk solusi tertentu.
     */
    public function edit(int $id): string
    {
        $solution = $this->solutionModel->getSolutionDetail($id);

        if (! $solution) {
            throw PageNotFoundException::forPageNotFound('Data solusi dengan ID #' . $id . ' tidak ditemukan.');
        }

        $categories    = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        $subCategories = $this->subCategoryModel->getWithCategory();

        $data = [
            'title'         => 'Edit Solusi: ' . $solution['title'] . ' — IT Knowledge Base',
            'activeMenu'    => 'dashboard',
            'isEdit'        => true,
            'solution'      => $solution,
            'categories'    => $categories,
            'subCategories' => $subCategories,
            'validation'    => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ];

        return view('solutions/form', $data);
    }

    /**
     * Memperbarui data solusi yang sudah ada beserta pengelolaan file lampiran.
     */
    public function update(int $id): RedirectResponse
    {
        $solution = $this->solutionModel->find($id);

        if (! $solution) {
            throw PageNotFoundException::forPageNotFound('Data solusi dengan ID #' . $id . ' tidak ditemukan.');
        }

        $rules = [
            'sub_category_id' => 'required|is_natural_no_zero',
            'title'           => 'required|min_length[5]|max_length[200]',
            'symptoms'        => 'required|min_length[10]',
            'root_cause'      => 'required|min_length[10]',
            'steps'           => 'required|min_length[10]',
        ];

        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $rules['attachment'] = 'uploaded[attachment]|max_size[attachment,5120]|is_image[attachment]|mime_in[attachment,image/jpg,image/jpeg,image/png,image/webp,image/gif]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Gagal memperbarui solusi. Mohon periksa kembali isian formulir.');
        }

        $attachmentPath = $solution['attachment'];

        // Cek apakah user mencentang hapus lampiran lama
        if ($this->request->getPost('remove_attachment') === '1' && ! empty($attachmentPath)) {
            $oldFile = FCPATH . ltrim($attachmentPath, '/\\');
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }
            $attachmentPath = null;
        }

        // Cek apakah ada unggahan gambar baru
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/solutions';
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Hapus file lama jika ada
            if (! empty($solution['attachment'])) {
                $oldFile = FCPATH . ltrim($solution['attachment'], '/\\');
                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            }

            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $attachmentPath = 'uploads/solutions/' . $newName;
        }

        $payload = [
            'sub_category_id' => (int) $this->request->getPost('sub_category_id'),
            'title'           => trim((string) $this->request->getPost('title')),
            'symptoms'        => trim((string) $this->request->getPost('symptoms')),
            'root_cause'      => trim((string) $this->request->getPost('root_cause')),
            'steps'           => trim((string) $this->request->getPost('steps')),
            'attachment'      => $attachmentPath,
            'is_pinned'       => $this->request->getPost('is_pinned') ? 1 : 0,
        ];

        $this->solutionModel->update($id, $payload);

        return redirect()->to(site_url('solutions/' . $id))
            ->with('success', 'Perubahan dokumentasi solusi berhasil disimpan.');
    }

    /**
     * Menghapus data solusi beserta file lampirannya.
     */
    public function delete(int $id): RedirectResponse
    {
        $solution = $this->solutionModel->find($id);

        if (! $solution) {
            return redirect()->to(site_url('/'))
                ->with('error', 'Data solusi tidak ditemukan atau sudah dihapus.');
        }

        if (! empty($solution['attachment'])) {
            $filePath = FCPATH . ltrim($solution['attachment'], '/\\');
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }

        $this->solutionModel->delete($id);

        return redirect()->to(site_url('/'))
            ->with('success', 'Dokumentasi solusi "' . $solution['title'] . '" berhasil dihapus.');
    }

    /**
     * Mengubah (toggle) status Quick Fix (is_pinned) pada suatu solusi.
     */
    public function togglePin(int $id): ResponseInterface|RedirectResponse
    {
        $solution = $this->solutionModel->find($id);

        if (! $solution) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Solusi tidak ditemukan.',
                ]);
            }

            return redirect()->back()->with('error', 'Solusi tidak ditemukan.');
        }

        $newStatus = ((int) $solution['is_pinned'] === 1) ? 0 : 1;
        $this->solutionModel->update($id, ['is_pinned' => $newStatus]);

        $message = $newStatus === 1
            ? 'Solusi berhasil disematkan ke daftar Quick Fix (Sering Terjadi).'
            : 'Solusi dilepas dari daftar Quick Fix.';

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'    => 'success',
                'is_pinned' => $newStatus === 1,
                'message'   => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}
