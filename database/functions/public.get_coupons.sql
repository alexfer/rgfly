CREATE OR REPLACE FUNCTION public.get_coupons(market_id integer, start integer DEFAULT 0, row_count integer DEFAULT 25)
    RETURNS json
    LANGUAGE plpgsql
AS $function$DECLARE
    get_coupons JSON;
    rows_count   INT;
BEGIN
    SELECT json_agg(json_build_object(
            'id', mc.id,
            'name', mc.name,
            'code', mc.code,
            'prroduct', json_build_object('id', mp.id),
            'market_id', mc.market_id,
            'discount', COALESCE(mc.discount, 0),
            'price', COALESCE(mc.price, 0),
            'date', mc.expired_at::timestamp - mc.started_at::timestamp,
            'startedAt', mc.started_at,
            'expiredAt', mc.expired_at
                    ))
    INTO get_coupons
    FROM market_coupon mc
             LEFT JOIN market_coupon_market_product mcmp ON mcmp.market_coupon_id = mc.id
             LEFT JOIN market_product mp ON mcmp.market_product_id = mp.id
    WHERE mc.market_id = get_coupons.market_id
    GROUP BY mc.id, mc.expired_at
    ORDER BY mc.expired_at ASC
    OFFSET start LIMIT row_count;

    SELECT COUNT(*)
    INTO rows_count
    FROM market_coupon mc
    WHERE mc.market_id = get_coupons.market_id;

    RETURN json_build_object(
            'data', get_coupons,
            'rows_count', rows_count
           );
END;$function$