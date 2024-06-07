CREATE OR REPLACE FUNCTION public.get_order_summary(session character varying DEFAULT NULL::character varying,
                                                    number character varying DEFAULT NULL::character varying)
    RETURNS json
    LANGUAGE plpgsql
AS
$function$
DECLARE
    summary JSON;
BEGIN
    SELECT json_agg(
                   json_build_object(
                           'id', o.id,
                           'session', o.session,
                           'store', (SELECT json_build_object(
                                                    'id', s.id,
                                                    'currency', s.currency
                                            )
                                     FROM store s
                                     WHERE s.id = o.store_id
                                     LIMIT 1),
                           'status', o.status,
                           'total', o.total,
                           'products', (SELECT json_agg(json_build_object(
                           'id', sop.id,
                           'size', sop.size::json->'size',
                           'color', sop.color::json->'extra',
                           'quantity', sop.quantity,
                           'cost', sop.cost,
                           'coupon', (SELECT json_build_object(
                                                     'id', sc.id,
                                                     'discount', sc.discount,
                                                     'price', sc.price,
                                                     'started', sc.started_at,
                                                     'expired', sc.expired_at
                                             )
                                      FROM store_coupon_store_product scsp
                                               LEFT JOIN store_coupon sc ON sc.id = scsp.store_coupon_id AND sc.type = 'product'
                                      WHERE scsp.store_product_id = sop.product_id),
                           'product', (SELECT json_build_object(
                                                      'id', p.id,
                                                      'cost', p.cost,
                                                      'discount', p.discount::integer,
                                                      'sku', p.sku,
                                                      'quantity', p.quantity,
                                                      'attachment', (SELECT a.name
                                                                     FROM store_product_attach spa
                                                                              LEFT JOIN attach a ON a.id = spa.attach_id
                                                                     WHERE spa.product_id = p.id
                                                                     LIMIT 1)
                                              )
                                       FROM store_product p
                                       WHERE p.id = sop.product_id)
                                                        ))
                                        FROM store_orders_product sop
                                        WHERE sop.orders_id = o.id)
                   )
           )
    INTO summary
    FROM store_orders o
    WHERE o.session = get_order_summary.session;

    RETURN json_build_object(
            'summary', summary
           );
END;
$function$