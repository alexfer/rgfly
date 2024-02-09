-- Change "<database>" name to your database
DROP ROLE IF EXISTS "<database>";
CREATE USER "<database>" WITH password '<password>';
ALTER USER "<database>" WITH SUPERUSER;
ALTER USER "<database>" CREATEDB;
CREATE DATABASE "<database>" OWNER "<database>";
GRANT ALL PRIVILEGES ON DATABASE "<database>" TO "<database>";