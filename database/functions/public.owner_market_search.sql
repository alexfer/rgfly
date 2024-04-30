CREATE OR REPLACE FUNCTION public.owner_market_search(query text, oid integer DEFAULT 0)
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
FROM market m WHERE m.owner_id = oid AND LOWER(m.name) LIKE LOWER('%' || query::text || '%');
RETURN json_build_object(
        'data', market_search
       );
END;$function$