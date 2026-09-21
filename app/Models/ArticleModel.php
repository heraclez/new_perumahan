<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table            = 'articles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'featured_image',
        'author_name',
        'status',
        'views_count',
        'meta_title',
        'meta_description',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation Rules
    protected $validationRules = [
        'title'       => 'required|min_length[3]|max_length[255]',
        'category'    => 'required|max_length[100]',
        'content'     => 'required|min_length[10]',
        'status'      => 'required|in_list[published,draft]',
    ];

    /**
     * Get published articles with optional category and search filters.
     */
    public function getPublishedArticles(int $perPage = 6, ?string $category = null, ?string $keyword = null)
    {
        $builder = $this->where('status', 'published');

        if (!empty($category)) {
            $builder->where('category', $category);
        }

        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('title', $keyword)
                    ->orLike('excerpt', $keyword)
                    ->orLike('content', $keyword)
                    ->groupEnd();
        }

        return $builder->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    /**
     * Get single article by slug and increment view count.
     */
    public function getArticleBySlug(string $slug): ?array
    {
        $article = $this->where('slug', $slug)
                        ->where('status', 'published')
                        ->first();

        if ($article) {
            $this->where('id', $article['id'])->increment('views_count');
            $article['views_count']++;
        }

        return $article;
    }

    /**
     * Get recent published articles for sidebar/footer widgets.
     */
    public function getRecentArticles(int $limit = 4, ?int $excludeId = null): array
    {
        $builder = $this->where('status', 'published');

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->orderBy('created_at', 'DESC')
                       ->findAll($limit);
    }

    /**
     * Get list of unique categories.
     */
    public function getCategories(): array
    {
        return $this->select('category, COUNT(id) as total')
                    ->where('status', 'published')
                    ->groupBy('category')
                    ->orderBy('total', 'DESC')
                    ->findAll();
    }

    /**
     * Helper to generate a unique SEO slug.
     */
    public function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $baseSlug = url_title($title, '-', true);
        if (empty($baseSlug)) {
            $baseSlug = 'artikel-' . time();
        }

        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $builder = $this->where('slug', $slug);
            if ($excludeId !== null) {
                $builder->where('id !=', $excludeId);
            }
            $exists = $builder->first();

            if (!$exists) {
                break;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
