CREATE OR REPLACE FUNCTION public.create_user_details(user_id integer, "values" json)
    RETURNS integer
    LANGUAGE plpgsql
AS
$function$
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
$function$