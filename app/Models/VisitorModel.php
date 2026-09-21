<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitorModel extends Model
{
    protected $table            = 'visitor_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'ip_address',
        'page_url',
        'page_title',
        'source',
        'device',
        'user_agent',
        'visited_date',
        'created_at',
    ];

    /**
     * Get summary KPI statistics.
     */
    public function getSummaryCounts(): array
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));

        $todayCount = $this->where('visited_date', $today)->countAllResults();
        $yesterdayCount = $this->where('visited_date', $yesterday)->countAllResults();
        $weekCount = $this->where('visited_date >=', $sevenDaysAgo)->countAllResults();
        $monthCount = $this->where('visited_date >=', $thirtyDaysAgo)->countAllResults();
        $totalCount = $this->countAllResults();

        // Calculate growth trend today vs yesterday
        $growth = 0;
        if ($yesterdayCount > 0) {
            $growth = round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100);
        }

        return [
            'today'     => $todayCount,
            'yesterday' => $yesterdayCount,
            'last7days' => $weekCount,
            'last30days'=> $monthCount,
            'total'     => $totalCount,
            'growth'    => $growth,
        ];
    }

    /**
     * Get daily visitor trends for the last N days (for Chart.js line chart).
     */
    public function getDailyStats(int $days = 14): array
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        $records = $this->select('visited_date, COUNT(id) as total')
                        ->where('visited_date >=', $startDate)
                        ->groupBy('visited_date')
                        ->orderBy('visited_date', 'ASC')
                        ->findAll();

        $map = [];
        foreach ($records as $row) {
            $map[$row['visited_date']] = (int) $row['total'];
        }

        $labels = [];
        $data = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d M', strtotime($date));
            $data[] = $map[$date] ?? 0;
        }

        return [
            'labels' => $labels,
            'data'   => $data,
        ];
    }

    /**
     * Get traffic source breakdown (Google, WhatsApp, Social, Direct).
     */
    public function getSourceBreakdown(): array
    {
        $records = $this->select('source, COUNT(id) as total')
                        ->groupBy('source')
                        ->orderBy('total', 'DESC')
                        ->findAll();

        $defaultSources = [
            'Google Search' => 0,
            'WhatsApp'      => 0,
            'Social Media'  => 0,
            'Direct'        => 0,
            'Other'         => 0,
        ];

        foreach ($records as $r) {
            $defaultSources[$r['source']] = (int) $r['total'];
        }

        return $defaultSources;
    }

    /**
     * Get top visited pages (properties, blog articles, siteplan).
     */
    public function getTopPages(int $limit = 5): array
    {
        return $this->select('page_url, MAX(page_title) as page_title, COUNT(id) as views')
                    ->groupBy('page_url')
                    ->orderBy('views', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Get device breakdown (Mobile vs Desktop).
     */
    public function getDeviceBreakdown(): array
    {
        $records = $this->select('device, COUNT(id) as total')
                        ->groupBy('device')
                        ->findAll();

        $devices = ['Mobile' => 0, 'Desktop' => 0];
        $total = 0;
        foreach ($records as $r) {
            $devices[$r['device']] = (int) $r['total'];
            $total += (int) $r['total'];
        }

        $mobilePct = $total > 0 ? round(($devices['Mobile'] / $total) * 100) : 75;
        $desktopPct = 100 - $mobilePct;

        return [
            'mobile'      => $devices['Mobile'],
            'desktop'     => $devices['Desktop'],
            'mobile_pct'  => $mobilePct,
            'desktop_pct' => $desktopPct,
        ];
    }
}
