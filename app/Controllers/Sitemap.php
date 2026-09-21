<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\ArticleModel;
use CodeIgniter\HTTP\ResponseInterface;

class Sitemap extends BaseController
{
    /**
     * Generate dynamic XML Sitemap compliant with sitemaps.org protocol.
     */
    public function index(): ResponseInterface
    {
        $propertyModel = new PropertyModel();
        $properties = $propertyModel->where('deleted_at', null)->orderBy('updated_at', 'DESC')->findAll();

        $articleModel = new ArticleModel();
        $articles = $articleModel->where('status', 'published')->orderBy('updated_at', 'DESC')->findAll();

        $urls = [
            [
                'loc'        => base_url('/'),
                'lastmod'    => date('Y-m-d'),
                'changefreq' => 'daily',
                'priority'   => '1.0',
            ],
            [
                'loc'        => base_url('properti'),
                'lastmod'    => date('Y-m-d'),
                'changefreq' => 'daily',
                'priority'   => '0.9',
            ],
            [
                'loc'        => base_url('siteplan'),
                'lastmod'    => date('Y-m-d'),
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ],
            [
                'loc'        => base_url('kpr-calculator'),
                'lastmod'    => date('Y-m-d'),
                'changefreq' => 'monthly',
                'priority'   => '0.7',
            ],
            [
                'loc'        => base_url('blog'),
                'lastmod'    => date('Y-m-d'),
                'changefreq' => 'daily',
                'priority'   => '0.8',
            ],
        ];

        foreach ($properties as $prop) {
            $lastmod = !empty($prop['updated_at']) ? date('Y-m-d', strtotime($prop['updated_at'])) : date('Y-m-d');
            $urls[] = [
                'loc'        => base_url('properti/' . $prop['slug']),
                'lastmod'    => $lastmod,
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ];
        }

        foreach ($articles as $art) {
            $lastmod = !empty($art['updated_at']) ? date('Y-m-d', strtotime($art['updated_at'])) : date('Y-m-d');
            $urls[] = [
                'loc'        => base_url('blog/' . $art['slug']),
                'lastmod'    => $lastmod,
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . esc($url['loc']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . esc($url['lastmod']) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . esc($url['changefreq']) . '</changefreq>' . "\n";
            $xml .= '    <priority>' . esc($url['priority']) . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        $xml .= '</urlset>';

        return $this->response
            ->setContentType('application/xml; charset=utf-8')
            ->setBody($xml);
    }
}
