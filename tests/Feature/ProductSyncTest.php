<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class productSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // cria no banco de teste a tabela de produtos_base
        Schema::create('produtos_base', function (Blueprint $table) {
            $table->string('prod_cod');
            $table->string('prod_nome');
            $table->string('prod_cat');
            $table->string('prod_subcat')->nullable();
            $table->text('prod_desc')->nullable();
            $table->string('prod_fab')->nullable();
            $table->string('prod_mod')->nullable();
            $table->string('prod_cor')->nullable();
            $table->string('prod_peso')->nullable();
            $table->string('prod_larg')->nullable();
            $table->string('prod_alt')->nullable();
            $table->string('prod_prof')->nullable();
            $table->string('prod_dt_cad')->nullable();
            $table->string('prod_atv')->default('1');
        });

        // cria no banco de teste a tabela de precos_base
        Schema::create('precos_base', function (Blueprint $table) {
            $table->string('prc_cod_prod');
            $table->string('prc_valor')->nullable();
            $table->string('prc_moeda')->nullable();
            $table->string('prc_desc')->nullable();
            $table->string('prc_acres')->nullable();
            $table->string('prc_promo')->nullable();
            $table->string('prc_dt_ini_promo')->nullable();
            $table->string('prc_dt_fim_promo')->nullable();
            $table->string('prc_dt_atual')->nullable();
            $table->string('prc_origem')->nullable();
            $table->string('prc_tipo_cli')->nullable();
            $table->string('prc_vend_resp')->nullable();
            $table->string('prc_obs')->nullable();
            $table->string('prc_status')->nullable();
        });
    }

    protected function createBaseProduct(): void
    {
        DB::table('produtos_base')->insert([
            [
                'prod_cod' => 'PRD1',
                'prod_nome' => '   product TESTE',
                'prod_cat' => '   caTêgoria',
                'prod_subcat' => ' Súb',
                'prod_desc' => '   descricao teste   ',
                'prod_fab' => '  marca  ',
                'prod_mod' => '  modelo  ',
                'prod_cor' => '  preTo  ',
                'prod_peso' => ' 1kg',
                'prod_larg' => ' 10cm',
                'prod_alt' => ' 5cm',
                'prod_prof' => ' 2cm',
                'prod_dt_cad' => now()->format('d/m/Y'),
                'prod_atv' => '1',
            ]
        ]);
    }

    public function test_product_sync_correctly(): void
    {
        $this->createBaseProduct();

        $response = $this->postJson('/api/sincronizar/produtos');
        $response->assertStatus(200);

        $product = DB::table('produto_insercao')->where('id', 1)->first();

        $this->assertNotNull($product);

        $this->assertEquals('Product teste', $product->name);
        $this->assertEquals('categoria', $product->category);
        $this->assertEquals('sub', $product->subcategory);
        $this->assertEquals('descricao teste', $product->description);
        $this->assertEquals('marca', $product->manufacturer);
        $this->assertEquals('modelo', $product->model);
        $this->assertEquals('preto', $product->color);

        $this->assertEquals(1000, $product->weight_g);
        $this->assertEquals(10, $product->width_cm);
        $this->assertEquals(5, $product->height_cm);
        $this->assertEquals(2, $product->depth_cm);

        $this->assertNotNull($product->created_at);
        $this->assertNotNull($product->updated_at);
    }

    public function test_price_sync_correctly(): void
    {
        $this->createBaseProduct();
        $response = $this->postJson('/api/sincronizar/produtos');
        $response->assertStatus(200);

        $product = DB::table('produto_insercao')->where('id', 1)->first();
        $this->assertNotNull($product);

        DB::table('precos_base')->insert([
            [
                'prc_cod_prod' => ' PRD1 ',
                'prc_valor' => ' 499,90 ',
                'prc_moeda' => 'brl',
                'prc_desc' => '5%',
                'prc_acres' => '0',
                'prc_promo' => '474,90',
                'prc_dt_ini_promo' => '2025/10/10',
                'prc_dt_fim_promo' => '2025-10-20',
                'prc_dt_atual' => '2025-10-15',
                'prc_origem' => 'SISTEMA ERP',
                'prc_tipo_cli' => 'VAREJO',
                'prc_vend_resp' => 'Marcos Silva',
                'prc_obs' => 'Produto em destaque',
                'prc_status' => 'ativo',
            ]
        ]);

        $response = $this->postJson('/api/sincronizar/precos');
        $response->assertStatus(200);

        $price = DB::table('preco_insercao')->where('id', 1)->first();

        $this->assertNotNull($price);

        $this->assertEquals(1, $price->id);
        $this->assertEquals(1, $price->produto_insercao_id);

        $this->assertEquals(499.90, $price->price);
        $this->assertEquals('BRL', $price->currency);

        $this->assertEquals(5, $price->discount_percentage);
        $this->assertEquals(0, $price->increase_percentage);

        $this->assertEquals(474.90, $price->promotional_price);

        $this->assertEquals('2025-10-10', $price->promotion_start_date);
        $this->assertEquals('2025-10-20', $price->promotion_end_date);
        $this->assertEquals('2025-10-15', $price->created_at);
        $this->assertNotNull($price->updated_at);

        $this->assertEquals('sistema erp', $price->origin);
        $this->assertEquals('varejo', $price->client_type);
    }
}
