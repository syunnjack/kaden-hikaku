<?php

namespace App\Console\Commands;

use App\Models\ItemWatch;
use App\Support\LineMessaging;
use App\Support\RakutenKadenSearch;
use Illuminate\Console\Command;

class CheckKadenWatches extends Command
{
    protected $signature = 'kaden:check-watches';

    protected $description = 'ウォッチ登録された商品を再検索し、値下がりをLINEで通知する';

    public function handle(): int
    {
        $watches = ItemWatch::with('lineUser')->get();

        foreach ($watches as $watch) {
            if (! $watch->lineUser) {
                continue;
            }

            // 商品名で再検索し、同じitemCodeの商品を探す(単品取得APIが無いため代替手段として使用)
            $results = RakutenKadenSearch::search($watch->item_name);
            $current = collect($results)->firstWhere('itemCode', $watch->item_code);

            if (! $current || empty($current['itemPrice'])) {
                $watch->update(['last_checked_at' => now()]);
                continue;
            }

            $currentPrice = (int) $current['itemPrice'];

            if ($watch->last_known_price !== null && $currentPrice < $watch->last_known_price) {
                LineMessaging::push(
                    $watch->lineUser->line_user_id,
                    "「{$watch->item_name}」が値下がりしました！ {$watch->last_known_price}円 → {$currentPrice}円"
                );
            }

            $watch->update([
                'last_known_price' => $currentPrice,
                'last_checked_at' => now(),
            ]);
        }

        $this->info("チェック完了: {$watches->count()}件の価格ウォッチを確認しました。");

        return self::SUCCESS;
    }
}
