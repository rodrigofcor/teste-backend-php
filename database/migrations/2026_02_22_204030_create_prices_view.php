<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW prices_view AS

            SELECT

                -- id
                CAST(
                    REPLACE(REPLACE(LOWER(TRIM(p.prc_cod_prod)), 'prd', ''), ' ', '')
                AS INTEGER) AS id,
                
                -- product_id
                CAST(
                    REPLACE(REPLACE(LOWER(TRIM(p.prc_cod_prod)), 'prd', ''), ' ', '')
                AS INTEGER) AS product_id,
                
                -- price
                CASE
                    WHEN p.prc_valor IS NULL OR TRIM(p.prc_valor) = '' THEN 0
                    WHEN LOWER(TRIM(p.prc_valor)) = 'sem preço' THEN 0

                    WHEN TRIM(p.prc_valor) LIKE '%,%' THEN
                        CAST(
                            REPLACE(
                                REPLACE(TRIM(p.prc_valor), '.', ''),
                            ',', '.')
                        AS REAL)

                    ELSE
                        CAST(TRIM(p.prc_valor) AS REAL)
                END AS price,
                
                -- currency
                UPPER(TRIM(p.prc_moeda)) AS currency,
                
                -- discount_perc
                CASE
                    WHEN p.prc_desc IS NULL OR TRIM(p.prc_desc) = '' THEN 0

                    ELSE
                        CAST(REPLACE(TRIM(p.prc_desc), '%', '') AS REAL)
                END AS discount_perc,
                
                -- increase_perc
                CASE
                    WHEN p.prc_acres IS NULL OR TRIM(p.prc_acres) = '' THEN 0

                    ELSE
                        CAST(REPLACE(TRIM(p.prc_acres), '%', '') AS REAL)
                END AS increase_perc,
                
                -- promotional_price
                CASE
                    WHEN p.prc_desc IS NULL 
                        OR TRIM(p.prc_desc) = '' 
                        OR CAST(REPLACE(TRIM(p.prc_desc), '%', '') AS REAL) = 0
                    THEN NULL

                    WHEN p.prc_promo IS NOT NULL AND TRIM(p.prc_promo) <> '' THEN
                        CASE
                            WHEN TRIM(p.prc_promo) LIKE '%,%' THEN
                                CAST(
                                    REPLACE(
                                        REPLACE(TRIM(p.prc_promo), '.', ''),
                                    ',', '.')
                                AS REAL)
                            ELSE
                                CAST(TRIM(p.prc_promo) AS REAL)
                        END

                    ELSE
                        (
                            CASE
                                WHEN TRIM(p.prc_valor) LIKE '%,%' THEN
                                    CAST(
                                        REPLACE(
                                            REPLACE(TRIM(p.prc_valor), '.', ''),
                                        ',', '.')
                                    AS REAL)
                                ELSE
                                    CAST(TRIM(p.prc_valor) AS REAL)
                            END
                        )
                        *
                        (1 - (
                            CAST(REPLACE(TRIM(p.prc_desc), '%', '') AS REAL) / 100.0
                        ))
                END AS promotional_price,
                
                -- promotion_start_date
                CASE
                    WHEN p.prc_desc IS NULL 
                        OR TRIM(p.prc_desc) = '' 
                        OR CAST(REPLACE(TRIM(p.prc_desc), '%', '') AS REAL) = 0
                    THEN NULL
                
                    -- dd/mm/yyyy
                    WHEN TRIM(p.prc_dt_ini_promo) LIKE '__/__/____' THEN
                        SUBSTR(TRIM(p.prc_dt_ini_promo), 7, 4) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_ini_promo), 4, 2) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_ini_promo), 1, 2)
                
                    -- dd-mm-yyyy
                    WHEN TRIM(p.prc_dt_ini_promo) LIKE '__-__-____' THEN
                        SUBSTR(TRIM(p.prc_dt_ini_promo), 7, 4) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_ini_promo), 4, 2) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_ini_promo), 1, 2)
                
                    -- yyyy/mm/dd
                    WHEN TRIM(p.prc_dt_ini_promo) LIKE '____/__/__' THEN
                        REPLACE(TRIM(p.prc_dt_ini_promo), '/', '-')
                
                    -- yyyy.mm.dd
                    WHEN TRIM(p.prc_dt_ini_promo) LIKE '____.__.__' THEN
                        REPLACE(TRIM(p.prc_dt_ini_promo), '.', '-')
                
                    -- fallback
                    ELSE
                        REPLACE(REPLACE(TRIM(p.prc_dt_ini_promo), '/', '-'), '.', '-')
                END AS promotion_start_date,
                
                -- promotion_end_date
                CASE
                    WHEN p.prc_desc IS NULL 
                        OR TRIM(p.prc_desc) = '' 
                        OR CAST(REPLACE(TRIM(p.prc_desc), '%', '') AS REAL) = 0
                    THEN NULL
                
                    -- dd/mm/yyyy
                    WHEN TRIM(p.prc_dt_fim_promo) LIKE '__/__/____' THEN
                        SUBSTR(TRIM(p.prc_dt_fim_promo), 7, 4) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_fim_promo), 4, 2) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_fim_promo), 1, 2)
                
                    -- dd-mm-yyyy
                    WHEN TRIM(p.prc_dt_fim_promo) LIKE '__-__-____' THEN
                        SUBSTR(TRIM(p.prc_dt_fim_promo), 7, 4) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_fim_promo), 4, 2) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_fim_promo), 1, 2)
                
                    -- yyyy/mm/dd
                    WHEN TRIM(p.prc_dt_fim_promo) LIKE '____/__/__' THEN
                        REPLACE(TRIM(p.prc_dt_fim_promo), '/', '-')
                
                    -- yyyy.mm.dd
                    WHEN TRIM(p.prc_dt_fim_promo) LIKE '____.__.__' THEN
                        REPLACE(TRIM(p.prc_dt_fim_promo), '.', '-')
                
                    ELSE
                        REPLACE(REPLACE(TRIM(p.prc_dt_fim_promo), '/', '-'), '.', '-')
                END AS promotion_end_date,
                
                -- created_at
                CASE
                    -- dd/mm/yyyy
                    WHEN TRIM(p.prc_dt_atual) LIKE '__/__/____' THEN
                        SUBSTR(TRIM(p.prc_dt_atual), 7, 4) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_atual), 4, 2) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_atual), 1, 2)
                
                    -- dd-mm-yyyy
                    WHEN TRIM(p.prc_dt_atual) LIKE '__-__-____' THEN
                        SUBSTR(TRIM(p.prc_dt_atual), 7, 4) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_atual), 4, 2) || '-' ||
                        SUBSTR(TRIM(p.prc_dt_atual), 1, 2)
                
                    -- yyyy/mm/dd
                    WHEN TRIM(p.prc_dt_atual) LIKE '____/__/__' THEN
                        REPLACE(TRIM(p.prc_dt_atual), '/', '-')
                
                    -- yyyy.mm.dd
                    WHEN TRIM(p.prc_dt_atual) LIKE '____.__.__' THEN
                        REPLACE(TRIM(p.prc_dt_atual), '.', '-')
                
                    ELSE
                        REPLACE(REPLACE(TRIM(p.prc_dt_atual), '/', '-'), '.', '-')
                END AS created_at,
                
                -- origin
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                    LOWER(TRIM(p.prc_origem)), 
                    'á', 'a'),
                    'à', 'a'),
                    'ã', 'a'),
                    'â', 'a'),
                    'ä', 'a'),
                    'é', 'e'),
                    'è', 'e'),
                    'ê', 'e'),
                    'ë', 'e'),
                    'í', 'i'),
                    'ì', 'i'),
                    'î', 'i'),
                    'ï', 'i'),
                    'ó', 'o'),
                    'ò', 'o'),
                    'õ', 'o'),
                    'ô', 'o'),
                    'ö', 'o'),
                    'ú', 'u'),
                    'ù', 'u'),
                    'û', 'u'),
                    'ü', 'u'),
                    'ç', 'c'
                ) AS origin,
                
                -- client_type
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                REPLACE(
                    LOWER(TRIM(p.prc_tipo_cli)), 
                    'á', 'a'),
                    'à', 'a'),
                    'ã', 'a'),
                    'â', 'a'),
                    'ä', 'a'),
                    'é', 'e'),
                    'è', 'e'),
                    'ê', 'e'),
                    'ë', 'e'),
                    'í', 'i'),
                    'ì', 'i'),
                    'î', 'i'),
                    'ï', 'i'),
                    'ó', 'o'),
                    'ò', 'o'),
                    'õ', 'o'),
                    'ô', 'o'),
                    'ö', 'o'),
                    'ú', 'u'),
                    'ù', 'u'),
                    'û', 'u'),
                    'ü', 'u'),
                    'ç', 'c'
                ) AS client_type,
                
                TRIM(p.prc_vend_resp) AS seller_name,
                TRIM(p.prc_obs) AS observation

            FROM precos_base p
            INNER JOIN products_view pv
                ON CAST(
                    REPLACE(REPLACE(LOWER(TRIM(p.prc_cod_prod)), 'prd', ''), ' ', '')
                AS INTEGER) = pv.id

            WHERE LOWER(TRIM(p.prc_status)) = 'ativo';
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS prices_view");
    }
};