CREATE TABLE Surveys
(
    id	        int auto_increment,
    description TEXT,
    category    varchar(100) not null unique,
    visibility  TEXT,  
    date 	varchar(10) not null,
    created  TIMESTAMP default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    modified TIMESTAMP default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    user_id int,
    survey_id int,
    primary key (id),
    Foreign Key (user_id) REFERENCES Users (id)
)
