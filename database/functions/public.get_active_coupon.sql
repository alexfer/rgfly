CREATE OR REPLACE FUNCTION public.get_active_coupon(store_id integer, type text, event smallint DEFAULT 1)
    RETURNS json
    LANGUAGE plpgsql
AS
$function$
DECLARE
    coupon JSON;
BEGIN

    SELECT json_build_object('coupon', json_build_object(
            'id', sc.id,
            'price', sc.price,
            'available', sc.available,
            'name', sc.name,
            'promotion', sc.promotion_text,
            'discount', sc.discount,
            'start', to_char(sc.started_at::timestamp, 'YYYY-MM-DD HH24:MI:SS'),
            'emd', to_char(sc.expired_at::timestamp, 'YYYY-MM-DD HH24:MI:SS')
                                       )) AS single
    INTO coupon
    FROM store_coupon sc
    WHERE sc.store_id = get_active_coupon.store_id
      AND sc.type = get_active_coupon.type
      AND sc.event = get_active_coupon.event
      AND sc.started_at::timestamp < CURRENT_TIMESTAMP
      AND sc.expired_at::timestamp > CURRENT_TIMESTAMP
    LIMIT 1;
    IF coupon IS NULL THEN RETURN 0; ELSE RETURN coupon; END IF;
END;
$function$