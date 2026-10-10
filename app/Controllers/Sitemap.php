<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\ArticleModel;
use CodeIgniter\HTTP\ResponseInterface;

class Sitemap extends BaseController
{
    /**
     * Generate dynamic XML Sitemap compliant with sitemaps.org & Google Image protocol.
     */
    public function index(): ResponseInterface
    {
        $companyName = $this->settings['company_name'] ?? 'Grand Harmoni Residence';

        $propertyModel = new PropertyModel();
        $properties    = $propertyModel->getPropertiesWithThumbnail([], 0);

        $articleModel = new ArticleModel();
        $articles     = $articleModel->where('status', 'published')->orderBy('updated_at', 'DESC')->findAll();

        $nowIso = date('c');

        $urls = [
            [
                'loc'        => base_url('/'),
                'lastmod'    => $nowIso,
                'changefreq' => 'daily',
                'priority'   => '1.0',
                'image'      => null,
            ],
            [
                'loc'        => base_url('properti'),
                'lastmod'    => $nowIso,
                'changefreq' => 'daily',
                'priority'   => '0.9',
                'image'      => null,
            ],
            [
                'loc'        => base_url('siteplan'),
                'lastmod'    => $nowIso,
                'changefreq' => 'weekly',
                'priority'   => '0.8',
                'image'      => null,
            ],
            [
                'loc'        => base_url('kpr-calculator'),
                'lastmod'    => $nowIso,
                'changefreq' => 'monthly',
                'priority'   => '0.7',
                'image'      => null,
            ],
            [
                'loc'        => base_url('blog'),
                'lastmod'    => $nowIso,
                'changefreq' => 'daily',
                'priority'   => '0.8',
                'image'      => null,
            ],
        ];

        // 1. URLs for Properties with Google Image tags
        foreach ($properties as $prop) {
            $timestamp = !empty($prop['updated_at']) ? strtotime($prop['updated_at']) : time();
            $lastmod   = date('c', $timestamp);

            $imgUrl = null;
            if (!empty($prop['primary_image'])) {
                $imgUrl = str_starts_with($prop['primary_image'], 'http')
                    ? $prop['primary_image']
                    : base_url('uploads/properties/' . $prop['primary_image']);
            }

            $urls[] = [
                'loc'        => base_url('properti/' . $prop['slug']),
                'lastmod'    => $lastmod,
                'changefreq' => 'weekly',
                'priority'   => '0.85',
                'image'      => $imgUrl ? [
                    'loc'     => $imgUrl,
                    'title'   => $prop['title'] . ' - ' . $companyName,
                    'caption' => 'Tipe rumah ' . $prop['title'] . ' harga Rp ' . number_format($prop['harga'], 0, ',', '.') . ' di ' . $companyName,
                ] : null,
            ];
        }

        // 2. URLs for Articles with Google Image tags
        foreach ($articles as $art) {
            $timestamp = !empty($art['updated_at']) ? strtotime($art['updated_at']) : (!empty($art['created_at']) ? strtotime($art['created_at']) : time());
            $lastmod   = date('c', $timestamp);

            $imgUrl = null;
            if (!empty($art['featured_image'])) {
                $imgUrl = str_starts_with($art['featured_image'], 'http')
                    ? $art['featured_image']
                    : base_url('uploads/articles/' . $art['featured_image']);
            }

            $urls[] = [
                'loc'        => base_url('blog/' . $art['slug']),
                'lastmod'    => $lastmod,
                'changefreq' => 'weekly',
                'priority'   => '0.8',
                'image'      => $imgUrl ? [
                    'loc'     => $imgUrl,
                    'title'   => $art['title'],
                    'caption' => $art['excerpt'] ?? $art['title'],
                ] : null,
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . esc($url['loc']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . esc($url['lastmod']) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . esc($url['changefreq']) . '</changefreq>' . "\n";
            $xml .= '    <priority>' . esc($url['priority']) . '</priority>' . "\n";

            if (!empty($url['image'])) {
                $xml .= '    <image:image>' . "\n";
                $xml .= '      <image:loc>' . esc($url['image']['loc']) . '</image:loc>' . "\n";
                $xml .= '      <image:title>' . esc($url['image']['title']) . '</image:title>' . "\n";
                $xml .= '      <image:caption>' . esc($url['image']['caption']) . '</image:caption>' . "\n";
                $xml .= '    </image:image>' . "\n";
            }

            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return $this->response
            ->setContentType('application/xml; charset=utf-8')
            ->setBody($xml);
    }
}
