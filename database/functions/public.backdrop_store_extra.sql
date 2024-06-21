CREATE OR REPLACE FUNCTION public.backdrop_store_extra(store_id integer)
 RETURNS json
 LANGUAGE plpgsql
AS $function$
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
$function$