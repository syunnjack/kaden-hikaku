<?php

namespace App\Http\Controllers;

use App\Models\ItemWatch;
use Illuminate\Http\Request;

class ItemWatchController extends Controller
{
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:60',
            'item_name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ]);

        $lineUserLocalId = $request->session()->get('line_user_local_id');

        if (! $lineUserLocalId) {
            return redirect()->route('line.login', $validated);
        }

        $watch = ItemWatch::where('line_user_id', $lineUserLocalId)
            ->where('item_code', $validated['item_code'])
            ->first();

        if ($watch) {
            $watch->delete();

            return back()->with('success', '価格ウォッチを解除しました。');
        }

        ItemWatch::create([
            'line_user_id' => $lineUserLocalId,
            'item_code' => $validated['item_code'],
            'item_name' => $validated['item_name'],
            'last_known_price' => $validated['price'],
        ]);

        return back()->with('success', '値下がりするとLINEでお知らせします。');
    }
}
