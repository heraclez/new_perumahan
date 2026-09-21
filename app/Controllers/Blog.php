<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Models\PropertyModel;
use App\Models\SettingModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Blog extends BaseController
{
    protected ArticleModel $articleModel;
    protected PropertyModel $propertyModel;
    protected array $settings;

    public function __construct()
    {
        $this->articleModel  = new ArticleModel();
        $this->propertyModel = new PropertyModel();
        $settingModel        = new SettingModel();
        $this->settings      = $settingModel->getAllSettings();
    }

    /**
     * Public Blog / Info Listing Page.
     */
    public function index(): string
    {
        $category = $this->request->getGet('kategori');
        $keyword  = $this->request->getGet('cari');

        $articles = $this->articleModel->getPublishedArticles(6, $category, $keyword);
        $pager    = $this->articleModel->pager;

        $categories = $this->articleModel->getCategories();
        $recentArticles = $this->articleModel->getRecentArticles(4);

        $pageTitle = 'Artikel & Info Seputar Hunian - ' . ($this->settings['company_name'] ?? 'Grand Harmoni');
        if (!empty($category)) {
            $pageTitle = 'Kategori: ' . esc($category) . ' - ' . ($this->settings['company_name'] ?? 'Grand Harmoni');
        }

        $data = [
            'title'            => $pageTitle,
            'meta_description' => 'Kumpulan artikel edukasi properti, tips pengajuan KPR, panduan legalitas tanah & rumah, serta berita progres pembangunan perumahan.',
            'settings'         => $this->settings,
            'articles'         => $articles,
            'pager'            => $pager,
            'categories'       => $categories,
            'recentArticles'   => $recentArticles,
            'currentCategory'  => $category,
            'currentKeyword'   => $keyword,
        ];

        return view('frontend/articles/index', $data);
    }

    /**
     * Public Article Detail Reading Page.
     */
    public function detail(string $slug): string
    {
        $article = $this->articleModel->getArticleBySlug($slug);

        if (!$article) {
            throw PageNotFoundException::forPageNotFound("Artikel dengan judul '{$slug}' tidak ditemukan atau belum diterbitkan.");
        }

        // Reading time calculation (average 200 words per minute)
        $wordCount   = str_word_count(strip_tags($article['content']));
        $readingTime = max(1, ceil($wordCount / 200));

        // Related articles in same category
        $relatedArticles = $this->articleModel->where('category', $article['category'])
                                              ->where('id !=', $article['id'])
                                              ->where('status', 'published')
                                              ->orderBy('created_at', 'DESC')
                                              ->findAll(3);

        // Featured properties recommendation CTA
        $featuredProperties = $this->propertyModel->getPropertiesWithThumbnail(['status' => 'Tersedia'], 3);

        $ogImage = $article['featured_image'] ?: base_url('assets/images/default-house.jpg');

        $data = [
            'title'              => ($article['meta_title'] ?: $article['title']) . ' - ' . ($this->settings['company_name'] ?? 'Grand Harmoni'),
            'meta_description'   => $article['meta_description'] ?: $article['excerpt'],
            'og_title'           => $article['title'],
            'og_description'     => $article['excerpt'],
            'og_image'           => $ogImage,
            'og_url'             => current_url(),
            'settings'           => $this->settings,
            'article'            => $article,
            'readingTime'        => $readingTime,
            'relatedArticles'    => $relatedArticles,
            'featuredProperties' => $featuredProperties,
        ];

        return view('frontend/articles/detail', $data);
    }
}
