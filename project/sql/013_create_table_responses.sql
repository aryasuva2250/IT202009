CREATE TABLE Responses(
	id 		int auto_increment,
	survey_id 	int,
	question_id     int,
	answer_id 	int,
	created  TIMESTAMP default CURRENT_TIMESTAMP,
        modified TIMESTAMP default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	user_id	        int,
	primary key	(id),
	FOREIGN KEY (user_id) REFERENCES Users (id),
	FOREIGN KEY (question_id) REFERENCES Question (id),
	FOREIGN KEY (survey_id) REFERENCES Surveys (id),
	FOREIGN KEY (answer_id) REFERENCES Answers (id),
	UNIQUE KEY (user_id, question_id, answer_id, survey_id)
)
