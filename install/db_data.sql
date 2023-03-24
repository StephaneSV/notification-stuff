INSERT INTO artists(id, name)
VALUES (130, 'Epica');
INSERT INTO albums(id, name, id_artist)
VALUES (1234, 'The quantum enigma', 130);
INSERT INTO content_types(id, name)
VALUES (1, 'album'),
       (2, 'playlist'),
       (3, 'track'),
       (4, 'podcast');
INSERT INTO notification_types(id, name)
VALUES (1, 'recommendation'),
       (2, 'new content'),
       (3, 'sharing'),
       (4, 'update');
INSERT INTO playlists (id, name)
VALUES (1000, 'Epica album start');
INSERT INTO podcasts (id, name)
VALUES (2000, 'Stuff');
INSERT INTO tracks (id, number, title, id_album)
VALUES (100, 1, 'Originem', 1234),
       (101, 2, 'The second stone', 1234),
       (102, 3, 'The essence of silence', 1234);
INSERT INTO playlist_track (id_playlist, id_track, position)
VALUES (1000, 100, 1),
       (1000, 101, 2);
INSERT INTO users(id, email)
VALUES (2345, 'stephanepro@mecontacter.net'),
       (4567, 'someone@yopmail.com');

INSERT INTO notifications (id, id_notification_type, id_user, id_content_type, id_content, expires, description, new,
                           date_creation)
VALUES (1, 1, 2345, null, null, '2023-03-20 20:52:29', null, 1, '2023-03-20 20:42:29'),
       (2, 1, 2345, null, null, null, null, 1, '2023-03-23 20:00:00'),
       (3, 2, 2345, 1, 1234, '2023-05-23 20:00:00', null, 1, '2023-03-23 20:00:00'),
       (4, 1, 2345, 3, 101, '2023-05-24 20:00:00', null, 0, '2022-03-23 20:00:00'),
       (5, 1, 4567, null, null, '2023-05-23 20:00:00', null, 1, '2023-03-23 20:00:00');