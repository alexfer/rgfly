CREATE OR REPLACE FUNCTION public.get_customer_messages(customer_id integer, "offset" integer DEFAULT 0,
                                                        "limit" integer DEFAULT 25)
    RETURNS json
    LANGUAGE plpgsql
AS
$function$
DECLARE
    get_customer_messages JSON;
    rows_count            INT;
BEGIN
    SELECT json_agg(json_build_object(
            'id', mm.id,
            'created', mm.created_at,
            'priority', INITCAP(mm.priority),
            'market', json_build_object(
                    'id', m.id,
                    'name', m.name,
                    'slug', m.slug
                      ),
            'product', (CASE
                            WHEN mp.id IS NULL THEN NULL
                            ELSE json_build_object(
                                    'id', mp.id,
                                    'slug', mp.slug,
                                    'short_name', mp.short_name
                                 ) END),
            'order', (CASE
                          WHEN mo.id IS NULL THEN NULL
                          ELSE json_build_object(
                                  'id', mo.id,
                                  'number', mo.number
                               ) END)
                    ))
    INTO get_customer_messages
    FROM market_message mm
             LEFT JOIN market_product mp ON mp.id = mm.product_id
             LEFT JOIN market_orders mo ON mo.id = mm.orders_id
             LEFT JOIN market m ON m.id = mm.market_id
    WHERE mm.customer_id = get_customer_messages.customer_id
    ORDER BY MAX(mm.id) DESC
    OFFSET get_customer_messages.offset LIMIT get_customer_messages.limit;

    SELECT COUNT(*)
    INTO rows_count
    FROM market_message mm
    WHERE mm.customer_id = get_customer_messages.customer_id;

    RETURN json_build_object(
            'data', get_customer_messages,
            'rows_count', rows_count
           );

END;
$function$