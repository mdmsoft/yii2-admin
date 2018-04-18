/**
 * Database schema required by yii2-admin.
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 2.5
 */

drop table if exists `menu`;
drop table if exists `user` cascade;

create table `menu`
(
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(128),
    `parent` int(11),
    `route` varchar(256),
    `order` int(11),
    `data`   blob,
    foreign key (`parent`) references `menu`(`id`)  ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `user`
(
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(32) NOT NULL,
    `auth_key` varchar(32) NOT NULL,
    `password_hash` varchar(256) NOT NULL,
    `password_reset_token` varchar(256),
    `email` varchar(256) NOT NULL,
    `status` integer not null default 10,
    `created_at` integer not null,
    `updated_at` integer not null
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
