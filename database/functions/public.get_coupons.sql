CREATE OR REPLACE FUNCTION public.get_coupons(store_id integer, type character varying DEFAULT NULL::character varying,
                                              start integer DEFAULT 0, row_count integer DEFAULT 10)
    RETURNS json
    LANGUAGE plpgsql
AS
$function$
DECLARE
    get_coupons JSON;
    rows_count  INT;
BEGIN
    SELECT json_agg(json_build_object(
            'id', sc.id,
            'name', sc.name,
            'codes', (SELECT json_agg(json_build_object('id', c.id, 'code', c.code))
                      FROM store_coupon_code c
                      WHERE c.coupon_id = sc.id),
            'product', json_build_object('id', mp.id),
            'market_id', sc.store_id,
            'discount', COALESCE(sc.discount, 0),
            'price', COALESCE(sc.price, 0),
            'duration', sc.expired_at::timestamp - sc.started_at::timestamp,
            'startedAt', sc.started_at,
            'expiredAt', sc.expired_at
                    ))
    INTO get_coupons
    FROM store_coupon sc
             LEFT JOIN store_coupon_store_product scsp ON scsp.store_coupon_id = sc.id
             LEFT JOIN store_product mp ON scsp.store_product_id = mp.id
    WHERE sc.store_id = get_coupons.store_id
      AND sc.type = get_coupons.type
    ORDER BY MIN(sc.expired_at) ASC
    OFFSET start LIMIT row_count;

    SELECT COUNT(*)
    INTO rows_count
    FROM store_coupon sc
    WHERE sc.store_id = get_coupons.store_id
      AND sc.type = get_coupons.type;

    RETURN json_build_object(
            'data', get_coupons,
            'rows_count', rows_count
           );
END;
$function$