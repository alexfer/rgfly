CREATE OR REPLACE FUNCTION public.get_messages(market_id integer, priority text, start integer DEFAULT 0,
                                               row_count integer DEFAULT 25)
    RETURNS json
    LANGUAGE plpgsql
AS
$function$
DECLARE
    get_messages JSON;
    rows_count   INT;
BEGIN
    SELECT json_agg(json_build_object(
            'id', mm.id,
            'created', mm.created_at,
            'priority', INITCAP(mm.priority),
            'customer', json_build_object(
                    'id', mc.id,
                    'full_name', CONCAT_WS(' ', mc.first_name, mc.last_name)
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
    INTO get_messages
    FROM market_message mm
             LEFT JOIN market_product mp ON mp.id = mm.product_id
             LEFT JOIN market_orders mo ON mo.id = mm.orders_id
             LEFT JOIN market_customer mc ON mc.id = mm.customer_id
    WHERE mm.market_id = get_messages.market_id
      AND mm.priority = get_messages.priority
    ORDER BY MAX(mm.id) DESC
    OFFSET start LIMIT row_count;

    SELECT COUNT(*)
    INTO rows_count
    FROM market_message mm
    WHERE mm.market_id = get_messages.market_id
      AND mm.priority = get_messages.priority;

    RETURN json_build_object(
            'data', get_messages,
            'rows_count', rows_count
           );

END;
$function$