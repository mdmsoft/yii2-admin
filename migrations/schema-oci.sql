/**
 * Database schema required by yii2-admin.
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 2.5
 */

drop table if exists "menu";
drop table if exists "user";

create table "menu"
(
    "id" NUMBER(10) NOT NULL PRIMARY KEY,
    "name" varchar(128),
    "parent" number(10),
    "route" varchar(256),
    "order" number(10),
    "data"   BYTEA,
    foreign key (parent) references "menu"("id")  ON DELETE SET NULL ON UPDATE CASCADE
);

create table "user"
(
    "id" NUMBER(10) NOT NULL PRIMARY KEY,
    "username" varchar(32) NOT NULL,
    "auth_key" varchar(32) NOT NULL,
    "password_hash" varchar(256) NOT NULL,
    "password_reset_token" varchar(256),
    "email" varchar(256) NOT NULL,
    "status" integer not null default 10,
    "created_at" number(10) not null,
    "updated_at" number(10) not null
);
