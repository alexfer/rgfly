CREATE OR REPLACE FUNCTION public.get_random_store()
    RETURNS jsonb
    LANGUAGE plpgsql
AS $function$
DECLARE
    results JSON;
BEGIN
    SELECT json_build_object(
                   'id', s.id,
                   'currency', s.currency,
                   'name', s.name,
                   'slug', s.slug,
                   'description', s.description,
                   'picture', a.name,
                   'coupon', (SELECT json_build_object(
                                             'id', sc.id,
                                             'type', sc.type,
                                             'price', sc.price,
                                             'discount', sc.discount
                                     )
                              FROM store_coupon sc
                              WHERE sc.store_id = s.id
                                AND sc.event = 1
                                AND extract(epoch from current_timestamp)::integer > extract(epoch from sc.created_at)::integer
                                AND extract(epoch from current_timestamp)::integer < extract(epoch from sc.expired_at)::integer
                              LIMIT 1),
                   'payments', json_agg(json_build_object(
                    'id', spg.id,
                    'icon', spg.icon,
                    'text', spg.handler_text,
                    'name', spg.name,
                    'summary', spg.summary
                                        ))
           )
    INTO results
    FROM store s
             LEFT JOIN attach a on a.id = s.attach_id
             LEFT JOIN public.store_payment_gateway_store spgs on s.id = spgs.store_id
             LEFT JOIN public.store_payment_gateway spg on spg.id = spgs.gateway_id
    WHERE s.deleted_at IS NULL
    GROUP BY s.id, a.id
    ORDER BY RANDOM()
    LIMIT 1;

    RETURN
        json_build_object(
                'result', results
        );
END;
$function$