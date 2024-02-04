DROP ROLE IF EXISTS techspace;
CREATE USER techspace WITH password '<password>';
ALTER USER techspace WITH SUPERUSER;
ALTER USER techspace CREATEDB;
CREATE DATABASE techspace OWNER techspace;
GRANT ALL PRIVILEGES ON DATABASE techspace TO techspace;