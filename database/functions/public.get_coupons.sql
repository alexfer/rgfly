CREATE OR REPLACE FUNCTION public.get_coupons(market_id integer, type character varying DEFAULT NULL::character varying,
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
            'id', mc.id,
            'name', mc.name,
            'codes', (SELECT json_agg(json_build_object('id', c.id, 'code', c.code))
                      FROM market_coupon_code c
                      WHERE c.coupon_id = mc.id),
            'product', json_build_object('id', mp.id),
            'market_id', mc.market_id,
            'discount', COALESCE(mc.discount, 0),
            'price', COALESCE(mc.price, 0),
            'duration', mc.expired_at::timestamp - mc.started_at::timestamp,
            'startedAt', mc.started_at,
            'expiredAt', mc.expired_at
                    ))
    INTO get_coupons
    FROM market_coupon mc
             LEFT JOIN market_coupon_market_product mcmp ON mcmp.market_coupon_id = mc.id
             LEFT JOIN market_product mp ON mcmp.market_product_id = mp.id
    WHERE mc.market_id = get_coupons.market_id
      AND mc.type = get_coupons.type
    ORDER BY MIN(mc.expired_at) ASC
    OFFSET start LIMIT row_count;

    SELECT COUNT(*)
    INTO rows_count
    FROM market_coupon mc
    WHERE mc.market_id = get_coupons.market_id
      AND mc.type = get_coupons.type;

    RETURN json_build_object(
            'data', get_coupons,
            'rows_count', rows_count
           );
END;
$function$