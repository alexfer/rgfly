CREATE OR REPLACE FUNCTION public.get_coupon_codes(store_id integer, coupon_id integer, type character varying)
    RETURNS json
    LANGUAGE plpgsql
AS
$function$
DECLARE
    codes JSON;
BEGIN
    SELECT json_agg(json_build_object(
            'id', cc.id,
            'code', cc.code
                    ))
    INTO codes
    FROM store_coupon_code cc
             LEFT OUTER JOIN store_coupon sc on sc.store_id = get_coupon_codes.store_id
             LEFT JOIN store_coupon_usage scu on cc.id != scu.coupon_code_id
    WHERE sc.type = get_coupon_codes.type
      AND cc.coupon_id = get_coupon_codes.coupon_id;

    RETURN json_build_object(
            'result', codes
           );
END;
$function$