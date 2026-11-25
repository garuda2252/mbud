<?php

namespace App\Models;

use CodeIgniter\Model;

class SubCategoryModel extends Model
{
    protected $table            = 'sub_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id',
        'name',
        'slug',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'category_id' => 'required|is_natural_no_zero',
        'name'        => 'required|min_length[2]|max_length[100]',
        'slug'        => 'required|min_length[2]|max_length[120]|is_unique[sub_categories.slug,id,{id}]',
    ];

    protected $validationMessages = [
        'category_id' => [
            'required'            => 'Kategori induk wajib dipilih.',
            'is_natural_no_zero'  => 'Kategori induk tidak valid.',
        ],
        'name' => [
            'required'   => 'Nama sub-kategori wajib diisi.',
            'min_length' => 'Nama sub-kategori minimal 2 karakter.',
            'max_length' => 'Nama sub-kategori maksimal 100 karakter.',
        ],
        'slug' => [
            'required'  => 'Slug sub-kategori wajib diisi.',
            'is_unique' => 'Slug sub-kategori sudah terdaftar.',
        ],
    ];

    /**
     * Mengambil daftar sub-kategori beserta informasi kategori induk dan jumlah solusinya.
     */
    public function getWithCategory(?int $categoryId = null): array
    {
        $builder = $this->select('
                sub_categories.*,
                categories.name AS category_name,
                categories.slug AS category_slug,
                categories.icon AS category_icon,
                COUNT(solutions.id) AS total_solutions
            ')
            ->join('categories', 'categories.id = sub_categories.category_id', 'inner')
            ->join('solutions', 'solutions.sub_category_id = sub_categories.id', 'left')
            ->groupBy('sub_categories.id')
            ->orderBy('categories.name', 'ASC')
            ->orderBy('sub_categories.name', 'ASC');

        if (! empty($categoryId)) {
            $builder->where('sub_categories.category_id', $categoryId);
        }

        return $builder->findAll();
    }
}
