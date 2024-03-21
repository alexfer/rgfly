CREATE OR REPLACE FUNCTION public.get_products(offset_rows integer DEFAULT 0, limit_rows integer DEFAULT 10)
 RETURNS json
 LANGUAGE plpgsql
AS $function$
DECLARE
get_products JSON;
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
         INNER JOIN market_category_product cp ON cp.product_id = p.id
         INNER JOIN market_category c ON cp.category_id = c.id
         LEFT JOIN market_category cc ON c.parent_id = cc.id
         INNER JOIN market_orders_product op ON op.product_id = p.id
         LEFT JOIN (
    SELECT DISTINCT ON (pa.product_id)
        pa.product_id,
        a.name
    FROM market_product_attach pa
        LEFT JOIN attach a ON pa.attach_id = a.id
    ORDER BY pa.product_id
) a ON a.product_id = p.id
         LEFT JOIN market_wishlist w ON w.product_id = p.id
         INNER JOIN market m ON m.id = p.market_id
WHERE p.deleted_at IS NULL
GROUP BY p.id, a.name, c.id, cc.id, m.id
OFFSET offset_rows LIMIT limit_rows;

RETURN get_products;
END;
$function$