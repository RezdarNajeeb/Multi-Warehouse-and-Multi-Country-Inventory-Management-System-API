<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $rows = Inventory::whereColumn('quantity', '=', 'min_quantity')
            ->with(['product:id,name,sku', 'warehouse:id,location,country_id', 'warehouse.country:id,name'])
            ->get()
            ->map(fn ($inv) => [
                'product_id'          => $inv->product_id,
                'product_name'        => $inv->product->name,
                'sku'                 => $inv->product->sku,
                'quantity'            => $inv->quantity,
                'min_quantity'        => $inv->min_quantity,
                'warehouse_location'  => $inv->warehouse->location,
                'country'             => $inv->warehouse->country->name ?? '—',
            ]);

        return response()->json($rows);
    }
}
