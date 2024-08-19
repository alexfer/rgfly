CREATE
    OR REPLACE FUNCTION public.backdrop_admin_stores(start integer DEFAULT 0, row_count integer DEFAULT 25)
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
                    ) ORDER BY s.id DESC)
    INTO results
    FROM store s
    OFFSET start LIMIT row_count;
    RETURN json_build_object(
            'result', results
           );
END;
$function$