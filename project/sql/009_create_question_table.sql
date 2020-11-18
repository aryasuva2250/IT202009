CREATE TABLE Question
(
    id        int auto_increment,
    question  TEXT,
    survey_id int,
    primary key(id),
    FOREIGN KEY (survey_id) REFERENCES Surveys (id)
)
