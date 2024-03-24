CREATE OR REPLACE FUNCTION public.get_products_by_child_category(child_id integer, start integer DEFAULT 0, row_count integer DEFAULT 10)
    RETURNS json
    LANGUAGE plpgsql
AS
$function$
DECLARE
    get_products JSON;
    rows_count   INT;
BEGIN
    SELECT json_agg(json_build_object(
            'id', p.id,
            'slug', p.slug,
            'cost', p.cost,
            'discount', p.discount,
            'name', p.name,
            'fee', p.fee,
            'short_name', p.short_name,
            'quantity', p.quantity,
            'attach_name', a.name,
            'category_name', c.name,
            'category_slug', c.slug,
            'parent_category_name', cc.name,
            'parent_category_slug', cc.slug,
            'market', m.name,
            'market_phone', m.phone,
            'market_id', m.id,
            'currency', m.currency,
            'market_slug', m.slug
                    ))
    INTO get_products
    FROM market_product p
             JOIN market_category_product cp ON p.id = cp.product_id
             JOIN market_category c ON c.id = cp.category_id
             JOIN market_category cc ON c.parent_id = cc.id
             LEFT JOIN (SELECT DISTINCT ON (pa.product_id) pa.product_id, a.name
                        FROM market_product_attach pa
                                 LEFT JOIN attach a ON pa.attach_id = a.id
                        ORDER BY pa.product_id) a ON a.product_id = p.id
             LEFT JOIN market_wishlist w ON w.product_id = p.id
             JOIN market m ON m.id = p.market_id
    WHERE p.deleted_at IS NULL
      AND cp.category_id = child_id
    OFFSET start LIMIT row_count;
    SELECT COUNT(*)
    INTO rows_count
    FROM market_product p
             JOIN market_category_product cp ON p.id = cp.product_id
             JOIN market_category c ON c.id = cp.category_id
    WHERE p.deleted_at IS NULL
      AND cp.category_id = child_id;
    RETURN json_build_object(
            'data', get_products,
            'rows_count', rows_count
           );
END;
$function$
