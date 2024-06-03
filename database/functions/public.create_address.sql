CREATE OR REPLACE FUNCTION public.create_address(customer_id integer, "values" json)
    RETURNS integer
    LANGUAGE plpgsql
AS
$function$
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
$function$