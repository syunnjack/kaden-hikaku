<?php

namespace App\Http\Controllers;

class SitemapController extends Controller
{
    private const CATEGORIES = [
        '冷蔵庫', '洗濯機', '掃除機', 'テレビ', 'スマホ・タブレット',
        'イヤホン・ヘッドホン', 'パソコン', 'キッチン家電', '空気清浄機',
    ];

    public function index()
    {
        $urls = collect([
            ['loc' => route('kaden.index'), 'priority' => '1.0'],
            ['loc' => route('about'), 'priority' => '0.3'],
        ])->merge(
            collect(self::CATEGORIES)->map(fn ($category) => [
                'loc' => route('kaden.search', ['keyword' => $category]),
                'priority' => '0.8',
            ])
        );

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
