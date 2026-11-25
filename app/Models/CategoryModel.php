<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'icon',
        'description',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'        => 'required|min_length[2]|max_length[100]',
        'slug'        => 'required|min_length[2]|max_length[120]|is_unique[categories.slug,id,{id}]',
        'icon'        => 'permit_empty|max_length[50]',
        'description' => 'permit_empty|max_length[1000]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Nama kategori wajib diisi.',
            'min_length' => 'Nama kategori minimal 2 karakter.',
            'max_length' => 'Nama kategori maksimal 100 karakter.',
        ],
        'slug' => [
            'required'  => 'Slug kategori wajib diisi.',
            'is_unique' => 'Nama/slug kategori tersebut sudah digunakan.',
        ],
    ];

    /**
     * Mengambil daftar seluruh kategori beserta jumlah sub-kategori dan jumlah solusi di dalamnya.
     */
    public function getCategoriesWithCounts(): array
    {
        return $this->select('
                categories.*,
                COUNT(DISTINCT sub_categories.id) AS total_sub_categories,
                COUNT(DISTINCT solutions.id) AS total_solutions
            ')
            ->join('sub_categories', 'sub_categories.category_id = categories.id', 'left')
            ->join('solutions', 'solutions.sub_category_id = sub_categories.id', 'left')
            ->groupBy('categories.id')
            ->orderBy('categories.name', 'ASC')
            ->findAll();
    }
}
