-- auto-generated definition
create table hr_users
(
    id         serial not null
        constraint hr_users_pk
            primary key,
    user_id    integer,
    username   varchar,
    step   varchar,
    created_at integer
);

alter table hr_users
    owner to postgres;


create table hr_resume
(
    id serial
        constraint hr_resume_pk
            primary key,
    user_id int,
    full_name varchar,
    region varchar,
    phone varchar,
    email varchar,
    file varchar,
    tehnology varchar,
    add_info varchar(4096),
    status smallint default 1 not null,
    created_at int
);


