<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use Illuminate\Http\Request;

class NavigationItemController extends Controller
{
    public function index(Request $request)
    {
        return NavigationItem::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get()
            ->filter(fn (NavigationItem $item) => $request->user()->can($item->readPermission()))
            ->values()
            ->map(fn (NavigationItem $item) => [
                'key' => $item->key,
                'label' => $item->label,
                'path' => $item->path,
                'icon' => $item->icon,
                'permission' => $item->readPermission(),
            ]);
    }
}
