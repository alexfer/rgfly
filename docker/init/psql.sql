-- Change "<database>" name to your database
DROP ROLE IF EXISTS rgbfly;
CREATE USER rgbfly WITH password 'rgbfly';
ALTER USER rgbfly WITH SUPERUSER;
ALTER USER rgbfly CREATEDB;
CREATE DATABASE rgbfly OWNER rgbfly;
GRANT ALL PRIVILEGES ON DATABASE rgbfly TO rgbfly;