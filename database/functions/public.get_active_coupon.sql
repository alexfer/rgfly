CREATE OR REPLACE FUNCTION public.get_active_coupon(market_id integer, type text, event smallint DEFAULT 1)
    RETURNS json
    LANGUAGE plpgsql
AS
$function$
DECLARE
    coupon JSON;
BEGIN

    SELECT json_build_object('coupon', json_build_object(
            'id', mc.id,
            'price', mc.price,
            'discount', mc.discount,
            'start', to_char(mc.started_at::timestamp, 'YYYY-MM-DD HH24:MI:SS'),
            'emd', to_char(mc.expired_at::timestamp, 'YYYY-MM-DD HH24:MI:SS')
                                       )) AS single
    INTO coupon
    FROM market_coupon mc
    WHERE mc.market_id = get_active_coupon.market_id
      AND mc.type = get_active_coupon.type
      AND mc.event = get_active_coupon.event
      AND mc.started_at::timestamp < CURRENT_TIMESTAMP
      AND mc.expired_at::timestamp > CURRENT_TIMESTAMP
    LIMIT 1;
    IF coupon IS NULL THEN RETURN 0; ELSE RETURN coupon; END IF;
END;
$function$