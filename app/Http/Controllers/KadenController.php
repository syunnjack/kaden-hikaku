<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class KadenController extends Controller
{
    private const CATEGORIES = [
        '冷蔵庫', '洗濯機', '掃除機', 'テレビ', 'スマホ・タブレット',
        'イヤホン・ヘッドホン', 'パソコン', 'キッチン家電', '空気清浄機',
    ];

    public function index()
    {
        return view('kaden.index', ['categories' => self::CATEGORIES]);
    }

    public function search(Request $request)
    {
        $keyword = trim($request->input('keyword', ''));

        if ($keyword === '') {
            return redirect()->route('kaden.index');
        }

        $results = Cache::remember("kaden-search:{$keyword}", now()->addHour(), function () use ($keyword) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders([
                        'Referer' => config('app.url'),
                        'Origin' => config('app.url'),
                    ])
                    ->get('https://openapi.rakuten.co.jp/ichibams/api/IchibaItem/Search/20260701', [
                        'format' => 'json',
                        'formatVersion' => 2,
                        'applicationId' => env('RAKUTEN_APP_ID'),
                        'accessKey' => env('RAKUTEN_ACCESS_KEY'),
                        'affiliateId' => env('RAKUTEN_AFFILIATE_ID'),
                        'keyword' => $keyword,
                        'hits' => 30,
                        'sort' => 'standard',
                    ]);
            } catch (ConnectionException) {
                return [];
            }

            return $response->successful() ? ($response->json('Items') ?? []) : [];
        });

        $itemIds = collect($results)
            ->map(fn ($item) => $item['itemCode'] ?? null)
            ->filter()
            ->values();

        $reviews = Review::whereIn('item_id', $itemIds)
            ->latest()
            ->get()
            ->groupBy('item_id');

        $faq = $this->buildFaq($keyword, $reviews);

        return view('kaden.results', compact('results', 'keyword', 'reviews', 'faq'));
    }

    private function buildFaq(string $keyword, Collection $reviews): array
    {
        $topRated = $reviews->filter(fn ($group) => $group->count() > 0)
            ->sortByDesc(fn ($group) => $group->avg('rating'))
            ->first();
        $topRatedTitle = $topRated ? $topRated->first()->title : null;

        $faq = [
            [
                'question' => "「{$keyword}」の最安値はどこで確認できますか？",
                'answer' => '各商品ページの「楽天市場で見る」リンクから、楽天市場の商品ページで最新の価格を確認できます。',
            ],
            [
                'question' => "「{$keyword}」の口コミは見られますか？",
                'answer' => '各商品ページで、楽天市場のレビュー件数・評価に加えて、当サイト独自に投稿された口コミも確認できます。口コミはどなたでもログイン不要で投稿できます。',
            ],
        ];

        if ($topRatedTitle) {
            $faq[] = [
                'question' => "「{$keyword}」でおすすめの商品は？",
                'answer' => "口コミ評価をもとにすると、「{$topRatedTitle}」が現在最も高い評価を得ています。ただし用途や好みによって最適な商品は異なるため、他の商品の口コミもあわせてご確認ください。",
            ];
        }

        return $faq;
    }
}
