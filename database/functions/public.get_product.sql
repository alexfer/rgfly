CREATE OR REPLACE FUNCTION public.get_product(slug character varying)
    RETURNS json
    LANGUAGE plpgsql
AS
$function$
DECLARE
    get_product JSON;
BEGIN
    SELECT json_build_object(
                   'id', p.id,
                   'slug', p.slug,
                   'name', p.name,
                   'code', UPPER(p.slug),
                   'short_name', p.short_name,
                   'description', p.description,
                   'cost', p.cost,
                   'fee', p.fee,
                   'discount', p.discount::integer,
                   'sku', p.sku,
                   'quantity', p.quantity,
                   'pckg', p.pckg_quantity,
                   'attributes', (SELECT json_agg(json_build_object(
                    'id', pa.id,
                    'name', pa.name,
                    'in_front', pa.in_front
                                                  ))
                                  FROM store_product_attribute pa
                                  WHERE pa.product_id = p.id),
                   'attribute_values', (SELECT json_agg(json_build_object(
                    'id', pav.id,
                    'value', pav.value,
                    'in_use', pav.in_use,
                    'extra', pav.extra
                                                        ))
                                        FROM store_product_attribute pa
                                                 JOIN store_product_attribute_value pav ON pav.attribute_id = pa.id
                                        WHERE pa.product_id = p.id),
                   'attachments', (SELECT json_agg(json_build_object(
                    'id', a.id,
                    'name', a.name
                                                   ))
                                   FROM store_product_attach spa
                                            LEFT JOIN attach a ON a.id = spa.attach_id
                                   WHERE spa.product_id = p.id),
                   'coupon', json_build_object(
                           'id', sc.id,
                           'price', sc.price,
                           'expired', sc.expired_at
                             ),
                   'store', json_build_object(
                           'id', s.id,
                           'slug', s.slug,
                           'name', s.name,
                           'address', s.address,
                           'phone', s.phone,
                           'email', s.email,
                           'currency', s.currency,
                           'website', s.website,
                           'description', s.description
                            ),
                   'wishlist', json_build_object(
                           'id', w.id,
                           'product', w.product_id,
                           'store', w.store_id,
                           'customer', w.customer_id
                               )
           )
    INTO get_product
    FROM store_product p
             JOIN store_coupon_store_product scsp ON scsp.store_product_id = p.id
             JOIN store_coupon sc ON sc.id = scsp.store_coupon_id
             JOIN store s ON s.id = p.store_id
             LEFT JOIN store_wishlist w ON w.product_id = p.id AND w.store_id = s.id
    WHERE p.slug = get_product.slug
    GROUP BY p.id, s.id, sc.id, w.id;

    RETURN json_build_object(
            'product', get_product
           );
END;
$function$