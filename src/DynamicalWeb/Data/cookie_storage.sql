create table if not exists cookie_storage
(
    name                   varchar(255)             null comment 'The name of the cookie',
    token                  varchar(255)             not null comment 'The value of the cookie (Token)',
    expiry_time            int          default 0   null comment 'The Unix Timestamp for when this cookie expires',
    path                   varchar(256) default '/' null comment 'the path on the server that the cookie will be valid for (including all sub-directories), e.g. an empty string for the current directory or `/` for the root directory',
    domain                 varchar(256)             null comment 'the domain that the cookie will be valid for (including subdomains) or `null` for the current host (excluding subdomains)',
    http_only              tinyint(1)   default 1   null comment 'indicates that the cookie should be accessible through the HTTP protocol only and not through scripting languages',
    secure_only            tinyint(1)   default 1   null comment 'indicates that the cookie should be sent back by the client over secure HTTPS connections only',
    same_site_restriction  varchar(255)             null comment 'indicates that the cookie should not be sent along with cross-site requests (either `null`, `None`, `Lax` or `Strict`)',
    data                   blob                     null comment 'The data stored for this cookie (ZiProto blob)',
    ip_address             varchar(255)             null comment 'The IP Address that is associated with this cookie',
    ip_tied                tinyint(1)   default 1   null comment 'Indicates if this cookie is tied to a single IP address only',
    last_updated_timestamp int                      null comment 'The Unix Timestamp for when this cookie record was last updated',
    created_timestamp      int                      null comment 'The Unix Timestamp for when this cookie was created',
    constraint cookie_storage_name_value_uindex
        unique (name, token),
    constraint cookie_storage_value_uindex
        unique (token)
)
    comment 'Stores cookie data on the server-side';

create index cookie_storage_ip_address_index
    on cookie_storage (ip_address);

create index cookie_storage_ip_tied_index
    on cookie_storage (ip_tied);

create index cookie_storage_name_index
    on cookie_storage (name);

alter table cookie_storage
    add primary key (token);

