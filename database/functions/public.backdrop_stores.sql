CREATE OR REPLACE FUNCTION public.backdrop_stores(owner_id integer)
    RETURNS json
    LANGUAGE plpgsql
AS
$function$
DECLARE
    results JSON;
BEGIN
    SELECT json_agg(json_build_object(
            'id', s.id,
            'name', s.name,
            'created', s.created_at,
            'deleted', s.deleted_at,
            'locked', s.locked_to,
            'orders', (SELECT COUNT(*)
                       FROM store_orders o
                       WHERE o.store_id = s.id
                       LIMIT 1),
            'exports', (SELECT COUNT(*)
                        FROM store_operation o
                        WHERE o.store_id = s.id
                        LIMIT 1),
            'messages', (SELECT COUNT(*)
                         FROM store_message m
                         WHERE m.store_id = s.id
                         LIMIT 1),
            'products', (SELECT COUNT(*)
                         FROM store_product p
                         WHERE p.store_id = s.id
                         LIMIT 1)
                    ))
    INTO results
    FROM store s
    WHERE s.owner_id = backdrop_stores.owner_id;
    RETURN json_build_object(
            'result', results
           );
END;
$function$