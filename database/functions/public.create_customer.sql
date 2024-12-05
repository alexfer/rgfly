CREATE OR REPLACE FUNCTION public.create_customer(user_id integer, "values" json)
    RETURNS integer
    LANGUAGE plpgsql
AS
$function$
DECLARE
    last_inserted_id INTEGER;
BEGIN
    INSERT INTO "store_customer" (member_id,
                                   first_name,
                                   last_name,
                                   phone,
                                   country,
                                   email,
                                   social_id,
                                   created_at)
    VALUES (user_id,
            values ->> 'first_name',
            values ->> 'last_name',
            values ->> 'phone',
            values ->> 'country',
            values ->> 'email',
            values ->> 'social_id',
            CURRENT_TIMESTAMP)
    RETURNING id INTO last_inserted_id;

    RETURN last_inserted_id;
END;
$function$