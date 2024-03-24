CREATE OR REPLACE FUNCTION public.create_user("values" json)
    RETURNS integer
    LANGUAGE plpgsql
AS
$function$
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
$function$