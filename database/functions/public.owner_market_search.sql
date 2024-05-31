CREATE OR REPLACE FUNCTION public.owner_store_search(query text, oid integer DEFAULT 0)
 RETURNS json
 LANGUAGE plpgsql
AS $function$DECLARE
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
END;$function$