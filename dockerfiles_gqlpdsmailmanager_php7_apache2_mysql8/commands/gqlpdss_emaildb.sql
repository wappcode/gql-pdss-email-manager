INSERT INTO gpd_email_sender_account
(id, title, email, server, auth, username, account_password, secure, port, max_deliveries_per_hour, created, updated)
VALUES('zeda4a372717e04ccec53f79fd87bef806a', 'Cuenata demo', 'demo@demo.local.lan', 'pop3.demo.local.lan', 1, 'demo@demo.local.lan', '12345', 'ssl', 645, 0, now(), now());

INSERT INTO gpd_email_message
(id, title, body, plain_text_body, chartset, created, updated)
VALUES('idje5d403c4d04228f19839cacc7d8717db', 'Mensaje demo', '<h1>Mi Mensaje demo<h1>', null, 'UTF-8', now(), now());

INSERT INTO gpd_email_queue
(id, message_id, sender_account_id, title, subject, reply_to, reply_to_name, sender_name, sender_email_address, created, updated)
VALUES('qdo5541528b2e4f88ea9697abf2316833fb', 'idje5d403c4d04228f19839cacc7d8717db', 'zeda4a372717e04ccec53f79fd87bef806a', 'Queue Demo', 'Queue Demo', null, null, 'Cuenta demo', 'demo@demo.local.lan', now(), now());