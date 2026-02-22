<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function sync(ProductService $service)
    {
        $service->sync();

        return response()->json(['message' => 'Produtos sincronizados com sucesso!']);
    }

    public function syncPrices(ProductService $service)
    {
        $service->syncPrices();
        return response()->json(['message' => 'Preços sincronizados com sucesso!']);
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $search = $request->get('q');

        $query = DB::table('produto_insercao as p');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('p.name', 'like', "%{$search}%")
                ->orWhere('p.category', 'like', "%{$search}%")
                ->orWhere('p.subcategory', 'like', "%{$search}%")
                ->orWhere('p.description', 'like', "%{$search}%")
                ->orWhere('p.manufacturer', 'like', "%{$search}%")
                ->orWhere('p.model', 'like', "%{$search}%")
                ->orWhere('p.color', 'like', "%{$search}%")

                ->orWhereExists(function ($sub) use ($search) {
                    $sub->select(DB::raw(1))
                        ->from('preco_insercao as pr')
                        ->whereColumn('pr.produto_insercao_id', 'p.id')
                        ->where(function ($q2) use ($search) {
                            $q2->where('pr.seller_name', 'like', "%{$search}%")
                                ->orWhere('pr.origin', 'like', "%{$search}%")
                                ->orWhere('pr.client_type', 'like', "%{$search}%")
                                ->orWhere('pr.observation', 'like', "%{$search}%");
                        });
                });
            });
        }

        $produtos = $query->paginate($perPage);
        
        $produtos->getCollection()->transform(function ($produto) {
            $prices = DB::table('preco_insercao')
                ->where('produto_insercao_id', $produto->id)
                ->get();

            return [
                'id' => $produto->id,
                'name' => $produto->name,
                'category' => $produto->category,
                'subcategory' => $produto->subcategory,
                'description' => $produto->description,
                'manufacturer' => $produto->manufacturer,
                'model' => $produto->model,
                'color' => $produto->color,
                'weight_g' => $produto->weight_g,
                'width_cm' => $produto->width_cm,
                'height_cm' => $produto->height_cm,
                'depth_cm' => $produto->depth_cm,
                'created_at' => $produto->created_at,
                'updated_at' => $produto->updated_at,

                'prices' => $prices->map(function ($price) {
                    return [
                        'id' => $price->id,
                        'product_insercao_id' => $price->produto_insercao_id,
                        'price' => $price->price,
                        'currency' => $price->currency,
                        'discount_percentage' => $price->discount_percentage,
                        'increase_percentage' => $price->increase_percentage,
                        'promotional_price' => $price->promotional_price,
                        'promotion_start_date' => $price->promotion_start_date,
                        'promotion_end_date' => $price->promotion_end_date,
                        'origin' => $price->origin,
                        'client_type' => $price->client_type,
                        'seller_name' => $price->seller_name,
                        'observation' => $price->observation,
                        'created_at' => $price->created_at,
                        'updated_at' => $price->updated_at,
                    ];
                })->values()
            ];
        });

        return response()->json($produtos);
    }
}
