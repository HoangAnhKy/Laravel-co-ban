create table if not exists courses
(
    id         int auto_increment constraint `PRIMARY` primary key,
    name       varchar(255)                        not null,
    created_at timestamp default CURRENT_TIMESTAMP null,
    updated_at timestamp                           null on update CURRENT_TIMESTAMP
    );

create table if not exists students
(
    id         int auto_increment constraint `PRIMARY` primary key,
    course_id  int                                 not null,
    name       varchar(255)                        not null,
    birthday   timestamp                           not null,
    created_at timestamp default CURRENT_TIMESTAMP not null,
    updated_at timestamp                           null on update CURRENT_TIMESTAMP
    );

create table if not exists users
(
    id        int auto_incrementconstraint `PRIMARY` primary key,
    full_name varchar(50) charset utf8mb3 not null,
    email     varchar(255)                not null,
    password  varchar(255)                not null,
    token     varchar(255)                null
    );

