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
            CREATE VIEW products_view AS

            SELECT
                -- id
                CAST(
                    REPLACE(REPLACE(LOWER(TRIM(prod_cod)), 'prd', ''), ' ', '') AS INTEGER
                ) AS id,
                
                -- name
                UPPER(SUBSTR(TRIM(prod_nome), 1, 1)) || LOWER(SUBSTR(TRIM(prod_nome), 2)) AS name,
                
                -- category
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
                    LOWER(TRIM(prod_cat)), 
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
                ) AS category,
                
                -- subcategory
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
                    LOWER(TRIM(prod_subcat)), 
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
                ) AS subcategory,
                
                TRIM(prod_desc) AS description,
                TRIM(prod_fab) AS manufacturer,
                TRIM(prod_mod) AS model,
                
                -- color
                LOWER(TRIM(prod_cor)) AS color,
                
                -- weight_g (
                CASE
                    WHEN prod_peso IS NULL OR TRIM(prod_peso) = '' THEN NULL

                    WHEN LOWER(REPLACE(prod_peso, ' ', '')) LIKE '%kg' THEN
                        CAST(
                            REPLACE(
                                REPLACE(
                                    LOWER(REPLACE(prod_peso, ' ', '')),
                                    'kg',''
                                ),
                            ',', '.')
                        AS REAL
                        ) * 1000

                    ELSE
                        CAST(
                            REPLACE(
                                REPLACE(
                                    LOWER(REPLACE(prod_peso, ' ', '')),
                                    'g',''
                                ),
                            ',', '.')
                        AS REAL
                        )
                END AS weight_g,
                
                -- width_cm
                CASE
                    WHEN prod_larg IS NULL OR TRIM(prod_larg) = '' THEN NULL
                    ELSE CAST(
                        REPLACE(REPLACE(REPLACE(LOWER(TRIM(prod_larg)), ' ', ''), 'cm', ''), ',', '.')
                    AS REAL)
                END AS width_cm,

                -- height_cm
                CASE
                    WHEN prod_alt IS NULL OR TRIM(prod_alt) = '' THEN NULL
                    ELSE CAST(
                        REPLACE(REPLACE(REPLACE(LOWER(TRIM(prod_alt)), ' ', ''), 'cm', ''), ',', '.')
                    AS REAL)
                END AS height_cm,

                -- depth_cm
                CASE
                    WHEN prod_prof IS NULL OR TRIM(prod_prof) = '' THEN NULL
                    ELSE CAST(
                        REPLACE(REPLACE(REPLACE(LOWER(TRIM(prod_prof)), ' ', ''), 'cm', ''), ',', '.')
                    AS REAL)
                END AS depth_cm,
                
                -- created_at
                CASE
                    -- dd/mm/yyyy
                    WHEN TRIM(prod_dt_cad) LIKE '__/__/____' THEN
                        SUBSTR(TRIM(prod_dt_cad),7,4) || '-' ||
                        SUBSTR(TRIM(prod_dt_cad),4,2) || '-' ||
                        SUBSTR(TRIM(prod_dt_cad),1,2)

                    -- dd-mm-yyyy
                    WHEN TRIM(prod_dt_cad) LIKE '__-__-____' THEN
                        SUBSTR(TRIM(prod_dt_cad),7,4) || '-' ||
                        SUBSTR(TRIM(prod_dt_cad),4,2) || '-' ||
                        SUBSTR(TRIM(prod_dt_cad),1,2)

                    -- yyyy/mm/dd
                    WHEN TRIM(prod_dt_cad) LIKE '____/__/__' THEN
                        REPLACE(TRIM(prod_dt_cad), '/', '-')

                    -- yyyy.mm.dd
                    WHEN TRIM(prod_dt_cad) LIKE '____.__.__' THEN
                        REPLACE(TRIM(prod_dt_cad), '.', '-')

                    ELSE
                        REPLACE(REPLACE(TRIM(prod_dt_cad), '/', '-'), '.', '-')
                END AS created_at
                
            FROM produtos_base
            WHERE CAST(prod_atv AS INTEGER) = 1;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS products_view");
    }
};
