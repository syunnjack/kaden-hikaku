<?php

namespace App\Http\Controllers;

use App\Models\ItemWatch;
use App\Models\LineUser;
use App\Support\LineMessaging;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LineLoginController extends Controller
{
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('line_login_state', $state);

        if ($request->filled('item_code') && $request->filled('item_name') && $request->filled('price')) {
            $request->session()->put('line_login_intended_item', [
                'item_code' => $request->input('item_code'),
                'item_name' => $request->input('item_name'),
                'price' => (int) $request->input('price'),
            ]);
        }

        return redirect()->away(LineMessaging::authorizeUrl($state));
    }

    public function callback(Request $request)
    {
        $state = $request->query('state');
        $expectedState = $request->session()->pull('line_login_state');

        if (! $state || $state !== $expectedState) {
            return redirect()->route('kaden.index')->withErrors(['line' => 'LINEログインの検証に失敗しました。もう一度お試しください。']);
        }

        if (! $request->filled('code')) {
            return redirect()->route('kaden.index')->withErrors(['line' => 'LINEログインがキャンセルされました。']);
        }

        $token = LineMessaging::exchangeToken($request->input('code'));
        $claims = LineMessaging::verifyIdToken($token['id_token']);

        $lineUser = LineUser::updateOrCreate(
            ['line_user_id' => $claims['sub']],
            ['display_name' => $claims['name'] ?? null]
        );

        $request->session()->put('line_user_local_id', $lineUser->id);

        $intendedItem = $request->session()->pull('line_login_intended_item');
        if ($intendedItem) {
            ItemWatch::firstOrCreate(
                ['line_user_id' => $lineUser->id, 'item_code' => $intendedItem['item_code']],
                ['item_name' => $intendedItem['item_name'], 'last_known_price' => $intendedItem['price']]
            );

            return redirect()->route('kaden.search', ['keyword' => $intendedItem['item_name']])
                ->with('success', '価格ウォッチ登録が完了しました。値下がりするとLINEでお知らせします。');
        }

        return redirect()->route('kaden.index')->with('success', 'LINEログインが完了しました。');
    }
}
