<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class VisitorTracker implements FilterInterface
{
    /**
     * Inspect incoming request and record public visitor traffic.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Only track GET requests
        if ($request->getMethod() !== 'GET') {
            return;
        }

        // Get current relative path
        $uri = trim($request->getUri()->getPath(), '/');

        // Exclude admin, api, static assets, and system routes
        if (str_starts_with($uri, 'admin') ||
            str_starts_with($uri, 'api') ||
            str_starts_with($uri, 'uploads') ||
            str_starts_with($uri, 'assets') ||
            str_starts_with($uri, 'adminlte') ||
            str_ends_with($uri, '.css') ||
            str_ends_with($uri, '.js') ||
            str_ends_with($uri, '.ico') ||
            str_ends_with($uri, '.png') ||
            str_ends_with($uri, '.jpg') ||
            str_ends_with($uri, '.xml') ||
            str_ends_with($uri, '.txt')) {
            return;
        }

        // Ignore common automated bots
        $agent = $request->getUserAgent();
        $agentStr = (string) $agent;
        if ($agent->isRobot()) {
            return;
        }

        // Throttle tracking by session to avoid duplicate logs on rapid reloads (once per 60s per URL)
        $session = session();
        $trackedKey = 'trk_' . md5($uri);
        if ($session->get($trackedKey)) {
            return;
        }
        $session->set($trackedKey, time());

        // Determine device
        $device = 'Desktop';
        if ($agent->isMobile()) {
            $device = 'Mobile';
        }

        // Determine traffic source from HTTP Referer
        $referer = (string) $request->getHeaderLine('Referer');
        $source = 'Direct';

        if (!empty($referer)) {
            $refHost = parse_url($referer, PHP_URL_HOST) ?? '';
            $currentHost = parse_url(base_url(), PHP_URL_HOST) ?? '';

            if ($refHost === $currentHost) {
                $source = 'Direct';
            } elseif (str_contains($refHost, 'google.') || str_contains($refHost, 'bing.') || str_contains($refHost, 'yahoo.')) {
                $source = 'Google Search';
            } elseif (str_contains($refHost, 'whatsapp.') || str_contains($refHost, 'wa.me') || str_contains($refHost, 'api.whatsapp.com')) {
                $source = 'WhatsApp';
            } elseif (str_contains($refHost, 'facebook.') || str_contains($refHost, 'instagram.') || str_contains($refHost, 'tiktok.') || str_contains($refHost, 'twitter.') || str_contains($refHost, 'x.com')) {
                $source = 'Social Media';
            } else {
                $source = 'Other';
            }
        }

        $ip = $request->getIPAddress();
        $pathUrl = '/' . $uri;
        $today = date('Y-m-d');

        // Insert log safely without blocking page response
        try {
            $db = \Config\Database::connect();
            $db->table('visitor_logs')->insert([
                'ip_address'   => $ip,
                'page_url'     => $pathUrl,
                'page_title'   => null,
                'source'       => $source,
                'device'       => $device,
                'user_agent'   => substr($agentStr, 0, 255),
                'visited_date' => $today,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('notice', 'VisitorTracker error: ' . $e->getMessage());
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after response
    }
}
