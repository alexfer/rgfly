CREATE OR REPLACE FUNCTION public.market_search(query text)
 RETURNS json
 LANGUAGE plpgsql
AS $function$DECLARE
    market_search JSON;
BEGIN
SELECT json_agg(json_build_object(
        'id', m.id,
        'name', m.name
                ))
INTO market_search
FROM market m WHERE LOWER(m.name) LIKE LOWER('%' || query::text || '%');

RETURN json_build_object(
        'data', market_search
       );
END;$function$