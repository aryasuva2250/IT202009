CREATE TABLE Answers 
(
    id                 int auto_increment,
    answer        varchar(120) not null,
    modified      TIMESTAMP  NOT NULL  default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    created       TIMESTAMP  NOT NULL  default CURRENT_TIMESTAMP,
    user_id        int,
    primary key (id),
    FOREIGN KEY (user_id) REFERENCES Users (id)
)

