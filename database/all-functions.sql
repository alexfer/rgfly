create or replace function notify_messenger_messages() returns trigger
    language plpgsql
as
$$
            BEGIN
                PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$;

alter function notify_messenger_messages() owner to rgfly;

create or replace function get_coupons(store_id integer, type character varying DEFAULT NULL::character varying, start integer DEFAULT 0, row_count integer DEFAULT 10) returns json
    language plpgsql
as
$$
DECLARE
    get_coupons JSON;
    rows_count  INT;
BEGIN
    SELECT json_agg(json_build_object(
            'id', sc.id,
            'name', sc.name,
            'codes', (SELECT json_agg(json_build_object('id', c.id, 'code', c.code))
                      FROM store_coupon_code c
                      WHERE c.coupon_id = sc.id),
            'product', json_build_object('id', mp.id),
            'market_id', sc.store_id,
            'discount', COALESCE(sc.discount, 0),
            'price', COALESCE(sc.price, 0),
            'duration', sc.expired_at::timestamp - sc.started_at::timestamp,
            'startedAt', sc.started_at,
            'expiredAt', sc.expired_at
                    ))
    INTO get_coupons
    FROM store_coupon sc
             LEFT JOIN store_coupon_store_product scsp ON scsp.store_coupon_id = sc.id
             LEFT JOIN store_product mp ON scsp.store_product_id = mp.id
    WHERE sc.store_id = get_coupons.store_id
      AND sc.type = get_coupons.type
    ORDER BY MIN(sc.expired_at) ASC
    OFFSET start LIMIT row_count;

    SELECT COUNT(*)
    INTO rows_count
    FROM store_coupon sc
    WHERE sc.store_id = get_coupons.store_id
      AND sc.type = get_coupons.type;

    RETURN json_build_object(
            'data', get_coupons,
            'rows_count', rows_count
           );
END;
$$;

alter function get_coupons(integer, varchar, integer, integer) owner to rgfly;

create or replace function owner_store_search(query text, oid integer DEFAULT 0) returns json
    language plpgsql
as
$$DECLARE
    store_search JSON;
BEGIN
SELECT json_agg(json_build_object(
        'id', m.id,
        'name', m.name
                ))
INTO store_search
FROM store m WHERE m.owner_id = oid AND LOWER(m.name) LIKE LOWER('%' || query::text || '%');
RETURN json_build_object(
        'data', store_search
       );
END;$$;

alter function owner_store_search(text, integer) owner to rgfly;

create or replace function create_customer(user_id integer, "values" json) returns integer
    language plpgsql
as
$$
DECLARE
    last_inserted_id INTEGER;
BEGIN
    INSERT INTO "store_customer" (member_id,
                                   first_name,
                                   last_name,
                                   phone,
                                   country,
                                   email,
                                   created_at)
    VALUES (user_id,
            values ->> 'first_name',
            values ->> 'last_name',
            values ->> 'phone',
            values ->> 'country',
            values ->> 'email',
            CURRENT_TIMESTAMP)
    RETURNING id INTO last_inserted_id;

    RETURN last_inserted_id;
END;
$$;

alter function create_customer(integer, json) owner to rgfly;

create or replace function get_product(slug character varying) returns json
    language plpgsql
as
$$
DECLARE
    get_product JSON;
BEGIN
    WITH attachments AS (
        SELECT DISTINCT jsonb_build_object(
                                'id', a.id,
                                'name', a.name
                        ) AS attachment
        FROM store_product p
                 LEFT JOIN store_product_attach spa ON spa.product_id = p.id
                 LEFT JOIN attach a ON a.id = spa.attach_id
        WHERE p.slug = get_product.slug
    )
    SELECT json_build_object(
                   'id', p.id,
                   'slug', p.slug,
                   'name', p.name,
                   'code', UPPER(p.slug),
                   'short_name', p.short_name,
                   'description', p.description,
                   'cost', p.cost,
                   'fee', p.fee,
                   'discount', p.discount::integer,
                   'sku', p.sku,
                   'quantity', p.quantity,
                   'pckg', p.pckg_quantity,
                   'attributes', (
                       SELECT json_agg(json_build_object(
                               'id', pa.id,
                               'name', pa.name,
                               'in_front', pa.in_front
                                       ))
                       FROM store_product_attribute pa
                       WHERE pa.product_id = p.id
                   ),
                   'attribute_values', (
                       SELECT json_agg(json_build_object(
                               'id', pav.id,
                               'attribute_id', pav.attribute_id,
                               'value', pav.value,
                               'in_use', pav.in_use,
                               'extra', pav.extra
                                       ))
                       FROM store_product_attribute_value pav
                       WHERE pav.attribute_id IN (
                           SELECT pa.id
                           FROM store_product_attribute pa
                           WHERE pa.product_id = p.id
                       )
                   ),
                   'coupon', (CASE
                       WHEN sc.id IS NULL THEN NULL
                       ELSE json_build_object(
                              'id', sc.id,
                              'price', sc.price,
                              'expired', sc.expired_at
                           ) END),
                   'store', json_build_object(
                           'id', s.id,
                           'slug', s.slug,
                           'name', s.name,
                           'address', s.address,
                           'phone', s.phone,
                           'email', s.email,
                           'currency', s.currency,
                           'website', s.website,
                           'description', s.description
                            ),
                   'attachments_count', (SELECT COUNT(spa.id) FROM store_product_attach spa WHERE spa.product_id = p.id),
                   'attachments', (SELECT json_agg(attachment) FROM attachments),
                   'wishlist', json_build_object(
                           'id', w.id,
                           'product', w.product_id,
                           'store', w.store_id,
                           'customer', w.customer_id
                               ),
                   'brand', (
                       SELECT sb.name
                       FROM store_product_brand spb
                                LEFT JOIN store_brand sb ON sb.id = spb.brand_id
                       WHERE spb.product_id = p.id
                       LIMIT 1
                   ),
                   'supplier', (
                       SELECT ss.name
                       FROM store_product_supplier sps
                                LEFT JOIN store_supplier ss ON ss.id = sps.supplier_id
                       WHERE sps.product_id = p.id
                       LIMIT 1
                   ),
                   'manufacturer', (
                       SELECT sm.name
                       FROM store_product_manufacturer spm
                                LEFT JOIN store_manufacturer sm ON sm.id = spm.manufacturer_id
                       WHERE spm.product_id = p.id
                       LIMIT 1
                   )
           )
    INTO get_product
    FROM store_product p
             LEFT JOIN store_coupon_store_product scsp ON scsp.store_product_id = p.id
             LEFT JOIN store_coupon sc ON sc.id = scsp.store_coupon_id
             JOIN store s ON s.id = p.store_id
             LEFT JOIN store_wishlist w ON w.product_id = p.id AND w.store_id = s.id
    WHERE p.slug = get_product.slug
    GROUP BY p.id, s.id, sc.id, w.id;

    RETURN json_build_object(
            'product', get_product
           );
END;
$$;

alter function get_product(varchar) owner to rgfly;

create or replace function create_address(customer_id integer, "values" json) returns integer
    language plpgsql
as
$$
DECLARE
    last_inserted_id INTEGER;
    cid              INT;
BEGIN
    cid := customer_id;

    INSERT INTO "store_address" (customer_id,
                                  line1,
                                  line2,
                                  phone,
                                  country,
                                  city,
                                  region,
                                  postal,
                                  created_at)
    VALUES (cid,
            values ->> 'line1',
            values ->> 'line2',
            values ->> 'phone',
            values ->> 'country',
            values ->> 'city',
            values ->> 'region',
            values ->> 'postal',
            CURRENT_TIMESTAMP)
    RETURNING id INTO last_inserted_id;

    RETURN last_inserted_id;
END;
$$;

alter function create_address(integer, json) owner to rgfly;

create or replace function get_active_coupon(store_id integer, type text, event smallint DEFAULT 1) returns json
    language plpgsql
as
$$
DECLARE
    coupon JSON;
BEGIN

    SELECT json_build_object('coupon', json_build_object(
            'id', sc.id,
            'price', sc.price,
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
$$;

alter function get_active_coupon(integer, text, smallint) owner to rgfly;

create or replace function get_coupon_codes(store_id integer, coupon_id integer, type character varying) returns json
    language plpgsql
as
$$
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
$$;

alter function get_coupon_codes(integer, integer, varchar) owner to rgfly;

create or replace function get_order_summary(session character varying DEFAULT NULL::character varying, customer_id integer DEFAULT NULL::integer, number character varying DEFAULT NULL::character varying) returns json
    language plpgsql
as
$$
DECLARE
    summary JSON;
BEGIN
    SELECT json_agg(
                   json_build_object(
                           'id', o.id,
                           'session', o.session,
                           'number', o.number,
                           'store', (SELECT json_build_object(
                                                    'id', s.id,
                                                    'name', s.name,
                                                    'tax',s.tax,
                                                    'currency', s.currency,
                                                    'slug', s.slug,
                                                    'cc', s.cc::json
                                            )
                                     FROM store s
                                     WHERE s.id = o.store_id
                                     GROUP BY s.id
                                     LIMIT 1),
                           'status', o.status,
                           'total', o.total,
                           'tax', o.tax,
                           'products', (SELECT json_agg(json_build_object(
                           'id', sop.id,
                           'size', sop.size::json -> 'size',
                           'size_title', sop.size::json -> 'size',
                           'color', sop.color::json -> 'extra',
                           'color_title', sop.color::json -> 'color',
                           'quantity', sop.quantity,
                           'coupon', (SELECT json_build_object(
                                                     'id', sc.id,
                                                     'discount', sc.discount::integer,
                                                     'price', sc.price,
                                                     'started', sc.started_at,
                                                     'expired', sc.expired_at,
                                                     'valid', (sc.started_at::timestamp < CURRENT_TIMESTAMP AND sc.expired_at::timestamp > CURRENT_TIMESTAMP),
                                                     'hasUsed', (SELECT COUNT(scu.id)
                                                              FROM store_coupon_usage scu
                                                              WHERE scu.customer_id = get_order_summary.customer_id
                                                                AND scu.coupon_id = sc.id 
                                                                AND scu.relation = sop.product_id
                                                              LIMIT 1)
                                             )
                                      FROM store_coupon_store_product scsp
                                               LEFT JOIN store_coupon sc ON sc.id = scsp.store_coupon_id AND sc.type = 'product'
                                      WHERE scsp.store_product_id = sop.product_id),
                           'product', (SELECT json_build_object(
                                                      'id', p.id,
                                                      'name', p.name,
                                                      'short_name', p.short_name,
                                                      'cost', p.cost,
                                                      'discount', p.discount::integer,
                                                      'sku', p.sku,
                                                      'fee', p.fee,
                                                      'slug', p.slug,
                                                      'quantity', p.quantity,
                                                      'attachment', (SELECT a.name
                                                                     FROM store_product_attach spa
                                                                              LEFT JOIN attach a ON a.id = spa.attach_id
                                                                     WHERE spa.product_id = p.id
                                                                     LIMIT 1)
                                              )
                                       FROM store_product p
                                       WHERE p.id = sop.product_id)
                                                        ))
                                        FROM store_orders_product sop
                                        WHERE sop.orders_id = o.id)
                   )
           )
    INTO summary
    FROM store_orders o
    WHERE o.session = get_order_summary.session;

    RETURN json_build_object(
            'summary', summary
           );
END;
$$;

alter function get_order_summary(varchar, integer, varchar) owner to rgfly;

create or replace function store_search(query text) returns json
    language plpgsql
as
$$DECLARE
    store_search JSON;
BEGIN
SELECT json_agg(json_build_object(
        'id', m.id,
        'name', m.name
                ))
INTO store_search
FROM store m WHERE LOWER(m.name) LIKE LOWER('%' || query::text || '%');

RETURN json_build_object(
        'data', store_search
       );
END;$$;

alter function store_search(text) owner to rgfly;

create or replace function get_customer_messages(customer_id integer, "offset" integer DEFAULT 0, "limit" integer DEFAULT 25) returns json
    language plpgsql
as
$$
DECLARE
    get_customer_messages JSON;
    rows_count            INT;
BEGIN
    SELECT json_agg(json_build_object(
            'id', sm.id,
            'created', sm.created_at,
            'priority', INITCAP(sm.priority),
            'answers', (SELECT COUNT(*) FROM store_message sm WHERE sm.parent_id = sm.id),
            'store', json_build_object(
                    'id', s.id,
                    'name', s.name,
                    'slug', s.slug
                      ),
            'product', (CASE
                            WHEN sp.id IS NULL THEN NULL
                            ELSE json_build_object(
                                    'id', sp.id,
                                    'slug', sp.slug,
                                    'short_name', sp.short_name
                                 ) END),
            'order', (CASE
                          WHEN so.id IS NULL THEN NULL
                          ELSE json_build_object(
                                  'id', so.id,
                                  'number', so.number
                               ) END)
                    ))
    INTO get_customer_messages
    FROM store_message sm
             LEFT JOIN store_product sp ON sp.id = sm.product_id
             LEFT JOIN store_orders so ON so.id = sm.orders_id
             LEFT JOIN store s ON s.id = sm.store_id
    WHERE sm.customer_id = get_customer_messages.customer_id
      AND sm.parent_id IS NULL
    ORDER BY MAX(sm.id) DESC
    OFFSET get_customer_messages.offset LIMIT get_customer_messages.limit;

    SELECT COUNT(*)
    INTO rows_count
    FROM store_message sm
    WHERE sm.customer_id = get_customer_messages.customer_id;

    RETURN json_build_object(
            'data', get_customer_messages,
            'rows_count', rows_count
           );

END;
$$;

alter function get_customer_messages(integer, integer, integer) owner to rgfly;

create or replace function create_user_details(user_id integer, "values" json) returns integer
    language plpgsql
as
$$
DECLARE
    last_details_id INTEGER;
    social_id       INT;
    uid             INT;
    date_birth      DATE;
BEGIN
    uid := user_id;
    date_birth := TO_DATE(values ->> 'date_birth', 'YYYY-MM-DD');

    INSERT INTO "user_details" (user_id,
                                first_name,
                                last_name,
                                phone,
                                country,
                                city,
                                about,
                                date_birth,
                                updated_at)
    VALUES (uid,
            values ->> 'first_name',
            values ->> 'last_name',
            values ->> 'phone',
            values ->> 'country',
            values ->> 'city',
            values ->> 'about',
            date_birth,
            CURRENT_TIMESTAMP)
    RETURNING id INTO last_details_id;

    INSERT INTO "user_social" (details_id) VALUES (last_details_id) RETURNING id INTO social_id;

    RETURN social_id;
END;
$$;

alter function create_user_details(integer, json) owner to rgfly;

create or replace function get_products(start integer DEFAULT 0, row_count integer DEFAULT 10) returns json
    language plpgsql
as
$$
DECLARE
    get_products JSON;
    rows_count   INT;
BEGIN
    WITH products AS (SELECT DISTINCT jsonb_build_object(
                                     'id', p.id,
                                     'slug', p.slug,
                                     'cost', p.cost,
                                     'discount', p.discount,
                                     'name', p.name,
                                     'fee', p.fee,
                                     'short_name', p.short_name,
                                     'quantity', p.quantity,
                                     'attach_name', a.name,
                                     'category_name', c.name,
                                     'category_slug', c.slug,
                                     'parent_category_name', cc.name,
                                     'parent_category_slug', cc.slug,
                                     'store', m.name,
                                     'store_phone', m.phone,
                                     'store_id', m.id,
                                     'currency', m.currency,
                                     'store_slug', m.slug
                             ) AS product
                      FROM store_product p
                               JOIN store_category_product cp ON p.id = cp.product_id
                               JOIN store_category c ON c.id = cp.category_id
                               JOIN store_category cc ON c.parent_id = cc.id
                               LEFT JOIN (SELECT DISTINCT ON (pa.product_id) pa.product_id, a.name
                                          FROM store_product_attach pa
                                                   LEFT JOIN attach a ON pa.attach_id = a.id
                                          ORDER BY pa.product_id) a ON a.product_id = p.id
                               LEFT JOIN store_wishlist w ON w.product_id = p.id
                               JOIN store m ON m.id = p.store_id
                      WHERE p.deleted_at IS NULL OFFSET start LIMIT row_count)
    SELECT json_build_object('products', json_agg(products ORDER BY RANDOM()))
    INTO get_products FROM products;

    SELECT COUNT(*)
    INTO rows_count
    FROM store_product p
             JOIN store_category_product cp ON p.id = cp.product_id
             JOIN store_category c ON c.id = cp.category_id
    WHERE p.deleted_at IS NULL;

    RETURN json_build_object(
            'data', get_products,
            'rows_count', rows_count
           );
END;
$$;

alter function get_products(integer, integer) owner to rgfly;

create or replace function create_user("values" json) returns integer
    language plpgsql
as
$$
DECLARE
    last_inserted_id INTEGER;
    roles            json;
BEGIN
    roles := values ->> 'roles';

    INSERT INTO "user" (email, password, roles, ip, created_at)
    VALUES (values ->> 'email', values ->> 'password', roles, values ->> 'ip', CURRENT_TIMESTAMP)
    RETURNING id INTO last_inserted_id;

    RETURN last_inserted_id;
EXCEPTION
    WHEN unique_violation THEN
        RAISE NOTICE 'Unique constraint violation occurred';
        -- Perform additional actions as needed
        RETURN -1;
END;
$$;

alter function create_user(json) owner to rgfly;

create or replace function get_messages(store_id integer, priority text, start integer DEFAULT 0, row_count integer DEFAULT 25) returns json
    language plpgsql
as
$$
DECLARE
    get_messages JSON;
    rows_count   INT;
BEGIN
    SELECT json_agg(json_build_object(
            'id', sm.id,
            'created', sm.created_at,
            'priority', INITCAP(sm.priority),
            'answers', (SELECT COUNT(*) FROM store_message mc WHERE mc.parent_id = sc.id),
            'customer', json_build_object(
                    'id', sc.id,
                    'full_name', CONCAT_WS(' ', sc.first_name, sc.last_name)
                        ),
            'product', (CASE
                            WHEN sp.id IS NULL THEN NULL
                            ELSE json_build_object(
                                    'id', sp.id,
                                    'slug', sp.slug,
                                    'short_name', sp.short_name
                                 ) END),
            'order', (CASE
                          WHEN mo.id IS NULL THEN NULL
                          ELSE json_build_object(
                                  'id', mo.id,
                                  'number', mo.number
                               ) END)
                    ))
    INTO get_messages
    FROM store_message sm
             LEFT JOIN store_product sp ON sp.id = sm.product_id
             LEFT JOIN store_orders mo ON mo.id = sm.orders_id
             LEFT JOIN store_customer sc ON sc.id = sm.customer_id
    WHERE sm.store_id = get_messages.store_id
      AND sm.priority = get_messages.priority
      AND sm.parent_id IS NULL
    ORDER BY MAX(sm.id) DESC
    OFFSET start LIMIT row_count;

    SELECT COUNT(*)
    INTO rows_count
    FROM store_message sm
    WHERE sm.store_id = get_messages.store_id
      AND sm.priority = get_messages.priority;

    RETURN json_build_object(
            'data', get_messages,
            'rows_count', rows_count
           );

END;
$$;

alter function get_messages(integer, text, integer, integer) owner to rgfly;

create or replace function get_customer_orders(customer_id integer, start integer DEFAULT 0, row_count integer DEFAULT 25) returns json
    language plpgsql
as
$$
DECLARE
    orders     JSON;
    rows_count INTEGER;
BEGIN
    SELECT COUNT(*)
    FROM store_customer_orders sco
    WHERE sco.customer_id = get_customer_orders.customer_id
    INTO rows_count;

    SELECT json_agg(json_build_object(
                            'id', o.id,
                            'store', (SELECT json_build_object(
                                                     'id', s.id,
                                                     'name', s.name,
                                                     'currency', s.currency,
                                                     'slug', s.slug
                                             )
                                      FROM store s
                                      WHERE s.id = o.store_id
                                      LIMIT 1),
                            'number', o.number,
                            'created', o.created_at,
                            'completed', o.completed_at,
                            'tax', o.tax,
                            'total_quantity',
                            (SELECT SUM(op.quantity) FROM store_orders_product op WHERE op.orders_id = o.id LIMIT 1),
                            'coupon', (SELECT json_build_object(
                                                      'id', scu.id,
                                                      'price', sc.price,
                                                      'discount', sc.discount::integer
                                              )
                                       FROM store_coupon_usage scu
                                                LEFT JOIN public.store_coupon sc on sc.id = scu.coupon_id
                                       WHERE scu.relation = co.orders_id
                                       LIMIT 1),
                            'invoice', (json_build_object(
                    'id', si.id,
                    'number', si.number,
                    'tax', si.tax,
                    'amount', si.amount,
                    'created', si.created_at,
                    'paid', si.paid_at,
                    'payment_gateway', (SELECT json_build_object(
                                                       'id', spg.id,
                                                       'name', spg.name,
                                                       'icon', spg.icon
                                               )
                                        FROM store_payment_gateway spg
                                        WHERE spg.id = si.payment_gateway_id
                                        LIMIT 1)
                                        )),
                            'status', o.status,
                            'total', o.total,
                            'products', (SELECT json_agg(json_build_object(
                    'id', sop.id,
                    'quantity', sop.quantity,
                    'size', sop.size::json -> 'size',
                    'size_title', sop.size::json -> 'size',
                    'color', sop.color::json -> 'extra',
                    'color_title', sop.color::json -> 'color',
                    'product', (SELECT json_build_object(
                                               'id', p.id,
                                               'fee', p.fee,
                                               'cost', p.cost,
                                               'slug', p.slug,
                                               'amount', SUM(p.cost + p.fee) * sop.quantity,
                                               'discount', p.discount::integer,
                                               'short_name', p.short_name,
                                               'name', p.name,
                                               'coupon', (SELECT json_build_object(
                                                                         'id', c.id,
                                                                         'price', c.price,
                                                                         'discount', c.discount::integer
                                                                 )
                                                          FROM store_coupon_store_product scsp
                                                                   LEFT JOIN public.store_coupon c on c.id = scsp.store_coupon_id
                                                          WHERE scsp.store_product_id = p.id
                                                          LIMIT 1)
                                       )
                                FROM store_product p
                                WHERE p.id = sop.product_id
                                GROUP BY p.id
                                LIMIT 1)
                                                         ))
                                         FROM store_orders_product sop
                                         WHERE sop.orders_id = co.orders_id
                                         LIMIT 1)
                    ) ORDER BY co.id DESC)
    INTO orders
    FROM store_customer_orders co
             JOIN store_orders o ON o.id = co.orders_id
             LEFT JOIN store_invoice si on co.orders_id = si.orders_id
    WHERE co.customer_id = get_customer_orders.customer_id
    OFFSET get_customer_orders.start LIMIT get_customer_orders.row_count;

    RETURN json_build_object(
            'orders', orders,
            'rows_count', rows_count
           );
END ;
$$;

alter function get_customer_orders(integer, integer, integer) owner to rgfly;

create or replace function get_store(slug character varying, customer_id integer DEFAULT 0, start integer DEFAULT 0, row_count integer DEFAULT 25) returns jsonb
    language plpgsql
as
$$
DECLARE
    results JSON;
BEGIN
    WITH products AS (SELECT DISTINCT jsonb_build_object(
                                              'id', p.id,
                                              'slug', p.slug,
                                              'cost', p.cost,
                                              'discount', p.discount::integer,
                                              'fee', p.fee,
                                              'quantity', p.quantity,
                                              'short_name', p.short_name,
                                              'name', p.name,
                                              'category_name', c.name,
                                              'category_slug', c.slug,
                                              'parent_category_name', cc.name,
                                              'parent_category_slug', cc.slug,
                                              'attachment', (SELECT a.name
                                                             FROM store_product_attach spa
                                                                      LEFT JOIN attach a on a.id = spa.attach_id
                                                             WHERE spa.product_id = p.id
                                                             ORDER BY spa.id DESC
                                                             LIMIT 1)
                                      ) AS product
                      FROM store s
                               LEFT JOIN store_product p ON s.id = p.store_id
                               JOIN store_category_product cp ON p.id = cp.product_id
                               JOIN store_category c ON c.id = cp.category_id
                               JOIN store_category cc ON c.parent_id = cc.id
                      WHERE s.slug = get_store.slug
                      OFFSET get_store.start LIMIT get_store.row_count),
         coupon AS (SELECT sc2.started_at AS started, sc2.expired_at AS expired
                    FROM store s
                             LEFT JOIN store_coupon sc2 ON s.id = sc2.store_id
                    WHERE s.slug = get_store.slug
                    GROUP BY sc2.id
                    LIMIT 1)
    SELECT json_build_object(
                   'id', s.id,
                   'name', s.name,
                   'cc', s.cc::json,
                   'slug', s.slug,
                   'description', s.description,
                   'currency', s.currency,
                   'phone', s.phone,
                   'email', s.email,
                   'website', s.website,
                   'address', s.address,
                   'promo', json_build_object(' expired ', coupon.expired, ' started ', coupon.started),
                   'products_count', (SELECT COUNT(p.id)
                                        FROM store_product p
                                        WHERE p.store_id = s.id),
                   'socials', (SELECT json_agg(ss.*) as name
                                 FROM store_social ss
                                 WHERE ss.store_id = s.id
                                   AND ss.is_active = true),
                   'products', json_agg(product ORDER BY products DESC)
           )
    INTO results
    FROM store s
             CROSS JOIN products,
         coupon
    WHERE s.deleted_at IS NULL AND s.slug = get_store.slug
    GROUP BY s.id, coupon.started, coupon.expired;

    RETURN json_build_object(
            'result', results
           );
END;

$$;

alter function get_store(varchar, integer, integer, integer) owner to rgfly;

create or replace function get_random_store() returns jsonb
    language plpgsql
as
$$
DECLARE
    results  JSON;
    products JSON;
BEGIN
    SELECT json_build_object(
                   'id', s.id,
                   'currency', s.currency,
                   'name', s.name,
                   'cc', s.cc::json,
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
             LEFT JOIN store_payment_gateway_store spgs on s.id = spgs.store_id
             LEFT JOIN store_payment_gateway spg on spg.id = spgs.gateway_id
             JOIN store_product sp2 on s.id = sp2.store_id
    WHERE s.deleted_at IS NULL
    GROUP BY s.id, a.id
    HAVING COUNT(sp2.id) > 0
    ORDER BY RANDOM()
    LIMIT 1;

    SELECT json_agg(json_build_object(
            'id', p.id,
            'slug', p.slug,
            'name', p.name,
            'store_id', p.store_id,
            'quantity', p.quantity,
            'short_name', p.short_name,
            'cost', p.cost,
            'fee', p.fee,
            'discount', p.discount,
            'payments', (SELECT json_agg(json_build_object(
                                         'name', g.name,
                                         'icon', g.icon
                                         )) 
                         FROM store_payment_gateway_store spg 
                             LEFT JOIN store_payment_gateway g ON g.id = spg.gateway_id 
                         WHERE spg.store_id = p.store_id),
            'currency', (SELECT s.currency FROM store s WHERE s.id = p.store_id LIMIT 1),
            'attachment', (SELECT a.name
                           FROM store_product_attach spa
                                    LEFT JOIN attach a on a.id = spa.attach_id
                           WHERE spa.product_id = p.id
                           LIMIT 1)
                    ))
    FROM (SELECT sp.id,
                 sp.store_id,
                 sp.slug,
                 sp.quantity,
                 sp.name,
                 sp.short_name,
                 sp.cost,
                 sp.fee,
                 sp.discount
          FROM store_product sp
          WHERE sp.deleted_at IS NULL
          ORDER BY RANDOM()
          LIMIT 3) AS p
    INTO products;

    RETURN
        json_build_object(
                'store', results,
                'products', products
        );
END;
$$;

alter function get_random_store() owner to rgfly;

create or replace function backdrop_store_extra(store_id integer) returns json
    language plpgsql
as
$$
DECLARE
    suppliers     JSON;
    brands        JSON;
    manufacturers JSON;
BEGIN
    SELECT json_agg(json_build_object(
                            'id', b.id,
                            'name', b.name
                    ) ORDER BY b.name ASC)
    INTO brands
    FROM store_brand b
    WHERE b.store_id = backdrop_store_extra.store_id;

    SELECT json_agg(json_build_object(
                            'id', s.id,
                            'name', s.name
                    ) ORDER BY s.name ASC)
    INTO suppliers
    FROM store_supplier s
    WHERE s.store_id = backdrop_store_extra.store_id;

    SELECT json_agg(json_build_object(
                            'id', m.id,
                            'name', m.name
                    ) ORDER BY m.name ASC)
    INTO manufacturers
    FROM store_manufacturer m
    WHERE m.store_id = backdrop_store_extra.store_id;

    RETURN json_build_object(
            'suppliers', suppliers,
            'brands', brands,
            'manufacturers', manufacturers
           );
END;
$$;

alter function backdrop_store_extra(integer) owner to rgfly;

create or replace function backdrop_products(store_id integer, query text DEFAULT NULL::text, start integer DEFAULT 0, row_count integer DEFAULT 25) returns json
    language plpgsql
as
$$
DECLARE
    results    JSON;
    rows_count INTEGER;
BEGIN
    SELECT json_agg(json_build_object(
                            'id', p.id,
                            'store', (SELECT json_build_object(
                                                     'id', s.id,
                                                     'deleted', s.deleted_at
                                             )
                                      FROM store s
                                      WHERE s.id = p.store_id
                                      LIMIT 1),
                            'name', p.name,
                            'short_name', p.short_name,
                            'cost', p.cost,
                            'quantity', p.quantity,
                            'discount', p.discount::integer,
                            'fee', p.fee,
                            'created', p.created_at,
                            'deleted', p.deleted_at,
                            'coupons', json_build_object(
                                    'coupon', sc.id,
                                    'product', scsp.store_product_id
                                       )
                    ) ORDER BY p.id DESC)
    INTO results
    FROM store_product p
             LEFT JOIN store_coupon sc ON sc.store_id = p.store_id AND sc.type = 'product'
             LEFT JOIN store_coupon_store_product scsp on sc.id = scsp.store_coupon_id
    WHERE LOWER(p.short_name) LIKE LOWER('%' || query::text || '%')
      AND p.store_id = backdrop_products.store_id
    OFFSET backdrop_products.start LIMIT backdrop_products.row_count;

    SELECT COUNT(*)
    INTO rows_count
    FROM store_product p
    WHERE LOWER(p.short_name) LIKE LOWER('%' || query::text || '%')
      AND p.store_id = backdrop_products.store_id;

    RETURN json_build_object(
            'result', results,
            'query', query::text,
            'store', backdrop_products.store_id,
            'rows', rows_count
           );
END ;
$$;

alter function backdrop_products(integer, text, integer, integer) owner to rgfly;

create or replace function backdrop_stores(owner_id integer) returns json
    language plpgsql
as
$$
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
$$;

alter function backdrop_stores(integer) owner to rgfly;

create or replace function backdrop_admin_stores(start integer DEFAULT 0, row_count integer DEFAULT 25) returns json
    language plpgsql
as
$$DECLARE
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
END;$$;

alter function backdrop_admin_stores(integer, integer) owner to rgfly;

create or replace function get_products_by_parent_category(category_slug character varying, start integer DEFAULT 0, row_count integer DEFAULT 10, search text DEFAULT NULL::text) returns json
    language plpgsql
as
$$
DECLARE
    get_products JSON;
    rows_count   INT;
BEGIN
    WITH products AS (SELECT DISTINCT jsonb_build_object(
                                              'id', p.id,
                                              'slug', p.slug,
                                              'cost', p.cost,
                                              'discount', p.discount,
                                              'name', p.name,
                                              'fee', p.fee,
                                              'short_name', p.short_name,
                                              'quantity', p.quantity,
                                              'attach_name', a.name,
                                              'category_name', c.name,
                                              'category_slug', c.slug,
                                              'parent_category_name', cc.name,
                                              'parent_category_slug', cc.slug,
                                              'store', m.name,
                                              'store_phone', m.phone,
                                              'store_id', m.id,
                                              'currency', m.currency,
                                              'store_slug', m.slug
                                      ) AS product
                      FROM store_product p
                               JOIN store_category_product cp ON p.id = cp.product_id
                               JOIN store_category c ON c.id = cp.category_id
                               JOIN store_category cc ON c.parent_id = cc.id
                               LEFT JOIN (SELECT DISTINCT ON (pa.product_id) pa.product_id, a.name
                                          FROM store_product_attach pa
                                                   LEFT JOIN attach a ON pa.attach_id = a.id
                                          ORDER BY pa.product_id) a ON a.product_id = p.id
                               LEFT JOIN store_wishlist w ON w.product_id = p.id
                               JOIN store m ON m.id = p.store_id
                      WHERE p.deleted_at IS NULL
                        AND c.parent_id in (SELECT id FROM store_category WHERE slug = category_slug)
                      OFFSET start LIMIT row_count)

    SELECT json_build_object('products', json_agg(products ORDER BY product DESC))
    INTO get_products
    FROM products;

    SELECT COUNT(*)
    INTO rows_count
    FROM store_product p
             JOIN store_category_product cp ON p.id = cp.product_id
             JOIN store_category c ON c.id = cp.category_id
    WHERE p.deleted_at IS NULL
      AND c.parent_id IN (SELECT id FROM store_category WHERE slug = category_slug);

    RETURN json_build_object(
            'data', get_products,
            'rows_count', rows_count
           );
END;
$$;

alter function get_products_by_parent_category(varchar, integer, integer, text) owner to rgfly;

create or replace function get_products_by_child_category(child_id integer, start integer DEFAULT 0, row_count integer DEFAULT 10, search text DEFAULT NULL::text) returns json
    language plpgsql
as
$$
DECLARE
    get_products JSON;
    rows_count   INT;
BEGIN
    WITH products AS (SELECT DISTINCT jsonb_build_object(
                                              'id', p.id,
                                              'slug', p.slug,
                                              'cost', p.cost,
                                              'discount', p.discount,
                                              'name', p.name,
                                              'fee', p.fee,
                                              'short_name', p.short_name,
                                              'quantity', p.quantity,
                                              'attach_name', a.name,
                                              'category_name', c.name,
                                              'category_slug', c.slug,
                                              'parent_category_name', cc.name,
                                              'parent_category_slug', cc.slug,
                                              'store', m.name,
                                              'store_phone', m.phone,
                                              'store_id', m.id,
                                              'currency', m.currency,
                                              'store_slug', m.slug
                                      ) AS product
                      FROM store_product p
                               JOIN store_category_product cp ON p.id = cp.product_id
                               JOIN store_category c ON c.id = cp.category_id
                               JOIN store_category cc ON c.parent_id = cc.id
                               LEFT JOIN (SELECT DISTINCT ON (pa.product_id) pa.product_id, a.name
                                          FROM store_product_attach pa
                                                   LEFT JOIN attach a ON pa.attach_id = a.id
                                          ORDER BY pa.product_id) a ON a.product_id = p.id
                               LEFT JOIN store_wishlist w ON w.product_id = p.id
                               JOIN store m ON m.id = p.store_id
                      WHERE p.deleted_at IS NULL
                        AND cp.category_id = child_id
                      OFFSET start LIMIT row_count)
    SELECT json_build_object('products', json_agg(products ORDER BY product DESC))
    INTO get_products
    FROM products;
    
    SELECT COUNT(*)
    INTO rows_count
    FROM store_product p
             JOIN store_category_product cp ON p.id = cp.product_id
             JOIN store_category c ON c.id = cp.category_id
    WHERE p.deleted_at IS NULL
      AND cp.category_id = child_id;
    RETURN json_build_object(
            'data', get_products,
            'rows_count', rows_count
           );
END;
$$;

alter function get_products_by_child_category(integer, integer, integer, text) owner to rgfly;


