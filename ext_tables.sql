CREATE TABLE pages (
    tx_abtest2_b_id int(11) DEFAULT '0' NOT NULL,
    tx_abtest2_cookie_time int(11) DEFAULT '86400' NOT NULL,
    tx_abtest2_header text,
    tx_abtest2_counter int(11) DEFAULT '0' NOT NULL
);