<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ArticleModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Articles extends BaseController
{
    protected ArticleModel $articleModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
    }

    /**
     * List all articles with filters & pagination.
     */
    public function index(): string
    {
        $keyword  = $this->request->getGet('q');
        $category = $this->request->getGet('category');
        $status   = $this->request->getGet('status');

        $builder = $this->articleModel;

        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('title', $keyword)
                    ->orLike('author_name', $keyword)
                    ->groupEnd();
        }

        if (!empty($category)) {
            $builder->where('category', $category);
        }

        if (!empty($status)) {
            $builder->where('status', $status);
        }

        $articles = $builder->orderBy('created_at', 'DESC')->paginate(10);
        $pager    = $this->articleModel->pager;

        $categories = $this->articleModel->select('category')
                                          ->distinct()
                                          ->findAll();

        $data = [
            'title'      => 'Manajemen Artikel & Berita',
            'articles'   => $articles,
            'pager'      => $pager,
            'categories' => array_column($categories, 'category'),
            'filters'    => [
                'q'        => $keyword,
                'category' => $category,
                'status'   => $status,
            ],
        ];

        return view('admin/articles/index', $data);
    }

    /**
     * Display create article form.
     */
    public function create(): string
    {
        $data = [
            'title' => 'Tulis Artikel & Berita Baru',
        ];

        return view('admin/articles/create', $data);
    }

    /**
     * Store newly created article.
     */
    public function store()
    {
        $rules = [
            'title'          => 'required|min_length[5]|max_length[255]',
            'category'       => 'required|max_length[100]',
            'content'        => 'required|min_length[20]',
            'status'         => 'required|in_list[published,draft]',
            'author_name'    => 'permit_empty|max_length[100]',
            'excerpt'        => 'permit_empty',
            'featured_image' => 'permit_empty|is_image[featured_image]|mime_in[featured_image,image/jpg,image/jpeg,image/png,image/webp]|max_size[featured_image,3072]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $title = trim((string) $this->request->getPost('title'));
        $slug  = $this->articleModel->generateUniqueSlug($title);

        $excerpt = trim((string) $this->request->getPost('excerpt'));
        if (empty($excerpt)) {
            $excerpt = character_limiter(strip_tags((string) $this->request->getPost('content')), 180);
        }

        $author = trim((string) $this->request->getPost('author_name'));
        if (empty($author)) {
            $author = session()->get('admin_name') ?? 'Tim Redaksi';
        }

        $articleData = [
            'title'            => $title,
            'slug'             => $slug,
            'category'         => trim((string) $this->request->getPost('category')),
            'excerpt'          => $excerpt,
            'content'          => (string) $this->request->getPost('content'),
            'author_name'      => $author,
            'status'           => $this->request->getPost('status'),
            'meta_title'       => trim((string) $this->request->getPost('meta_title')) ?: $title,
            'meta_description' => trim((string) $this->request->getPost('meta_description')) ?: $excerpt,
        ];

        // Handle image upload or image url
        $imgUrl = trim((string) $this->request->getPost('featured_image_url'));
        if (!empty($imgUrl)) {
            $articleData['featured_image'] = $imgUrl;
        }

        $imgFile = $this->request->getFile('featured_image');
        if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
            $targetDir = FCPATH . 'uploads/articles';
            $opt = \App\Libraries\ImageOptimizer::convertToWebp($imgFile, $targetDir, $articleData['slug'] . '-cover');
            $articleData['featured_image'] = base_url('uploads/articles/' . $opt['filename']);
        }

        $this->articleModel->insert($articleData);

        return redirect()->to(site_url('admin/articles'))->with('success', 'Artikel berhasil diterbitkan.');
    }

    /**
     * Display edit article form.
     */
    public function edit(int $id): string
    {
        $article = $this->articleModel->find($id);
        if (!$article) {
            throw PageNotFoundException::forPageNotFound("Artikel dengan ID {$id} tidak ditemukan.");
        }

        $data = [
            'title'   => 'Edit Artikel: ' . $article['title'],
            'article' => $article,
        ];

        return view('admin/articles/edit', $data);
    }

    /**
     * Update existing article.
     */
    public function update(int $id)
    {
        $article = $this->articleModel->find($id);
        if (!$article) {
            throw PageNotFoundException::forPageNotFound("Artikel dengan ID {$id} tidak ditemukan.");
        }

        $rules = [
            'title'          => 'required|min_length[5]|max_length[255]',
            'category'       => 'required|max_length[100]',
            'content'        => 'required|min_length[20]',
            'status'         => 'required|in_list[published,draft]',
            'author_name'    => 'permit_empty|max_length[100]',
            'excerpt'        => 'permit_empty',
            'featured_image' => 'permit_empty|is_image[featured_image]|mime_in[featured_image,image/jpg,image/jpeg,image/png,image/webp]|max_size[featured_image,3072]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $title = trim((string) $this->request->getPost('title'));
        $slug  = $article['slug'];
        if ($title !== $article['title']) {
            $slug = $this->articleModel->generateUniqueSlug($title, $id);
        }

        $excerpt = trim((string) $this->request->getPost('excerpt'));
        if (empty($excerpt)) {
            $excerpt = character_limiter(strip_tags((string) $this->request->getPost('content')), 180);
        }

        $author = trim((string) $this->request->getPost('author_name'));
        if (empty($author)) {
            $author = $article['author_name'] ?? 'Tim Redaksi';
        }

        $updateData = [
            'title'            => $title,
            'slug'             => $slug,
            'category'         => trim((string) $this->request->getPost('category')),
            'excerpt'          => $excerpt,
            'content'          => (string) $this->request->getPost('content'),
            'author_name'      => $author,
            'status'           => $this->request->getPost('status'),
            'meta_title'       => trim((string) $this->request->getPost('meta_title')) ?: $title,
            'meta_description' => trim((string) $this->request->getPost('meta_description')) ?: $excerpt,
        ];

        // Handle image url or file upload
        $imgUrl = trim((string) $this->request->getPost('featured_image_url'));
        if (!empty($imgUrl)) {
            $updateData['featured_image'] = $imgUrl;
        }

        $imgFile = $this->request->getFile('featured_image');
        if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
            $targetDir = FCPATH . 'uploads/articles';
            $baseSlug = !empty($updateData['slug']) ? $updateData['slug'] : ($article['slug'] ?? 'artikel');
            $opt = \App\Libraries\ImageOptimizer::convertToWebp($imgFile, $targetDir, $baseSlug . '-cover');
            $updateData['featured_image'] = base_url('uploads/articles/' . $opt['filename']);
        }

        $this->articleModel->update($id, $updateData);

        return redirect()->to(site_url('admin/articles'))->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Soft delete article.
     */
    public function delete(int $id)
    {
        $article = $this->articleModel->find($id);
        if (!$article) {
            return redirect()->back()->with('error', 'Artikel tidak ditemukan.');
        }

        $this->articleModel->delete($id);

        return redirect()->to(site_url('admin/articles'))->with('success', 'Artikel berhasil dihapus.');
    }
}
