<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ProductService
{
    public function sync(): void
    {
        $products = DB::table('products_view')->get();

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'subcategory' => $product->subcategory,
                'description' => $product->description,
                'manufacturer' => $product->manufacturer,
                'model' => $product->model,
                'color' => $product->color,
                'weight_g' => $product->weight_g,
                'width_cm' => $product->width_cm,
                'height_cm' => $product->height_cm,
                'depth_cm' => $product->depth_cm,
                'created_at' => $product->created_at,
                'updated_at' => now()->format('Y-m-d'),
            ];
        }

        DB::table('produto_insercao')->upsert(
            $data,
            ['id']
        );
    }

    public function syncPrices(): void
    {
        $prices = DB::table('prices_view')->get();

        $data = [];

        foreach ($prices as $price) {
            $isProductExists = DB::table('produto_insercao')
                ->where('id', $price->product_id)
                ->first();

            if (!$isProductExists) {
                continue;
            }

            $data[] = [
                'id' => $price->id,
                'produto_insercao_id' => $price->product_id,
                'price' => $price->price,
                'currency' => $price->currency,
                'discount_percentage' => $price->discount_perc,
                'increase_percentage' => $price->increase_perc,
                'promotional_price' => $price->promotional_price,
                'promotion_start_date' => $price->promotion_start_date,
                'promotion_end_date' => $price->promotion_end_date,
                'origin' => $price->origin,
                'client_type' => $price->client_type,
                'seller_name' => $price->seller_name,
                'observation' => $price->observation,
                'created_at' => $price->created_at,
                'updated_at' => now()->format('Y-m-d'),
            ];
        }

        DB::table('preco_insercao')->upsert(
            $data,
            ['id']
        );
    }
}