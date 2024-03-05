-- Change "<database>" name to your database
DROP ROLE IF EXISTS rgfly;
CREATE USER rgfly WITH password 'rgfly';
ALTER USER rgfly WITH SUPERUSER;
ALTER USER rgfly CREATEDB;
CREATE DATABASE rgfly OWNER rgfly;
GRANT ALL PRIVILEGES ON DATABASE rgfly TO rgfly;