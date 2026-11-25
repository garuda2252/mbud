<?php

namespace App\Models;

use CodeIgniter\Model;

class SolutionModel extends Model
{
    protected $table            = 'solutions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sub_category_id',
        'title',
        'symptoms',
        'root_cause',
        'steps',
        'attachment',
        'is_pinned',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'sub_category_id' => 'required|is_natural_no_zero',
        'title'           => 'required|min_length[5]|max_length[200]',
        'symptoms'        => 'required|min_length[10]',
        'root_cause'      => 'required|min_length[10]',
        'steps'           => 'required|min_length[10]',
        'attachment'      => 'permit_empty|max_length[255]',
        'is_pinned'       => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'sub_category_id' => [
            'required'           => 'Sub-kategori wajib dipilih.',
            'is_natural_no_zero' => 'Sub-kategori yang dipilih tidak valid.',
        ],
        'title' => [
            'required'   => 'Judul masalah wajib diisi.',
            'min_length' => 'Judul masalah minimal 5 karakter.',
            'max_length' => 'Judul masalah maksimal 200 karakter.',
        ],
        'symptoms' => [
            'required'   => 'Gejala masalah (symptoms) wajib diisi.',
            'min_length' => 'Deskripsi gejala minimal 10 karakter.',
        ],
        'root_cause' => [
            'required'   => 'Akar penyebab (root cause) wajib diisi.',
            'min_length' => 'Deskripsi akar penyebab minimal 10 karakter.',
        ],
        'steps' => [
            'required'   => 'Langkah penyelesaian (steps) wajib diisi.',
            'min_length' => 'Langkah penyelesaian minimal 10 karakter.',
        ],
    ];

    /**
     * Mengambil daftar solusi lengkap dengan JOIN ke sub_categories dan categories,
     * serta mendukung pencarian berdasarkan title, symptoms, root_cause, steps,
     * maupun filter kategori dan sub-kategori.
     */
    public function getSolutionsWithRelations(
        ?string $keyword = null,
        ?int $categoryId = null,
        ?int $subCategoryId = null,
        bool $onlyPinned = false
    ): array {
        $builder = $this->select('
                solutions.*,
                sub_categories.name AS sub_category_name,
                sub_categories.slug AS sub_category_slug,
                sub_categories.category_id,
                categories.name AS category_name,
                categories.slug AS category_slug,
                categories.icon AS category_icon
            ')
            ->join('sub_categories', 'sub_categories.id = solutions.sub_category_id', 'inner')
            ->join('categories', 'categories.id = sub_categories.category_id', 'inner');

        if ($onlyPinned) {
            $builder->where('solutions.is_pinned', 1);
        }

        if (! empty($categoryId)) {
            $builder->where('categories.id', $categoryId);
        }

        if (! empty($subCategoryId)) {
            $builder->where('solutions.sub_category_id', $subCategoryId);
        }

        $keyword = trim((string) $keyword);
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('solutions.title', $keyword)
                ->orLike('solutions.symptoms', $keyword)
                ->orLike('solutions.root_cause', $keyword)
                ->orLike('solutions.steps', $keyword)
                ->orLike('sub_categories.name', $keyword)
                ->orLike('categories.name', $keyword)
                ->groupEnd();
        }

        return $builder
            ->orderBy('solutions.is_pinned', 'DESC')
            ->orderBy('solutions.updated_at', 'DESC')
            ->orderBy('solutions.id', 'DESC')
            ->findAll();
    }

    /**
     * Mengambil satu solusi berdasarkan ID beserta informasi sub-kategori dan kategori.
     */
    public function getSolutionDetail(int $id): ?array
    {
        return $this->select('
                solutions.*,
                sub_categories.name AS sub_category_name,
                sub_categories.slug AS sub_category_slug,
                sub_categories.category_id,
                categories.name AS category_name,
                categories.slug AS category_slug,
                categories.icon AS category_icon
            ')
            ->join('sub_categories', 'sub_categories.id = solutions.sub_category_id', 'inner')
            ->join('categories', 'categories.id = sub_categories.category_id', 'inner')
            ->where('solutions.id', $id)
            ->first();
    }

    /**
     * Mengambil solusi terkait pada sub-kategori atau kategori yang sama.
     */
    public function getRelatedSolutions(int $excludeId, int $subCategoryId, int $categoryId, int $limit = 4): array
    {
        return $this->select('
                solutions.id,
                solutions.title,
                solutions.is_pinned,
                solutions.updated_at,
                sub_categories.name AS sub_category_name,
                categories.name AS category_name
            ')
            ->join('sub_categories', 'sub_categories.id = solutions.sub_category_id', 'inner')
            ->join('categories', 'categories.id = sub_categories.category_id', 'inner')
            ->where('solutions.id !=', $excludeId)
            ->groupStart()
                ->where('solutions.sub_category_id', $subCategoryId)
                ->orWhere('categories.id', $categoryId)
            ->groupEnd()
            ->orderBy('solutions.sub_category_id = ' . (int) $subCategoryId, 'DESC', false)
            ->orderBy('solutions.is_pinned', 'DESC')
            ->orderBy('solutions.updated_at', 'DESC')
            ->findAll($limit);
    }
}
