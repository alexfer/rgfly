CREATE TABLE market_session
(
    sess_id       VARCHAR(128) NOT NULL PRIMARY KEY,
    sess_data     BYTEA        NOT NULL,
    sess_lifetime INTEGER      NOT NULL,
    sess_time     INTEGER      NOT NULL
);
CREATE INDEX sessions_sess_lifetime_idx ON market_session (sess_lifetime);