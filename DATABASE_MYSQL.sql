-- auto-generated definition
create table hr_users
(
    id int auto_increment,
    user_id    integer,
    username   varchar(255),
    step       varchar(255),
    created_at integer,
    constraint hr_users_pk
        primary key (id)
);

create table hr_resume
(
    id int auto_increment,
    user_id    int,
    full_name  varchar(255),
    region     varchar(255),
    phone      varchar(255),
    email      varchar(255),
    file       varchar(255),
    tehnology  varchar(255),
    add_info   text,
    status     smallint default 1 not null,
    created_at int,
    constraint hr_resume_pk
        primary key (id)
);

