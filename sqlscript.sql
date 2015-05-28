DROP TABLE IF EXISTS Users;
CREATE TABLE Users(
    id		INTEGER PRIMARY KEY,
    name	TEXT NOT NULL,
    pw		TEXT NOT NULL,
    email	TEXT NOT NULL UNIQUE
);

DROP TABLE IF EXISTS Questions;
CREATE TABLE Questions(
    id		INTEGER PRIMARY KEY,
    header	TEXT NOT NULL,
    question	TEXT NOT NULL,
    userId	INTEGER NOT NULL,
    created	DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (userId) REFERENCES Users(id)
);

DROP TABLE IF EXISTS Answers;
CREATE TABLE Answers(
    id		INTEGER PRIMARY KEY,
    userId	INTEGER NOT NULL,
    questionId	INTEGER NOT NULL,
    answer	TEXT NOT NULL,
    accepted	BOOLEAN NOT NULL DEFAULT FALSE,
    created	DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (userId) REFERENCES Users(id),
    FOREIGN KEY (questionId) REFERENCES Questions(id)
);

DROP TABLE IF EXISTS Comments;
CREATE TABLE Comments(
    id		INTEGER PRIMARY KEY,
    answerId	INTEGER NOT NULL,
    userId	INTEGER NOT NULL,
    comment	TEXT NOT NULL,
    created	DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (answerId) REFERENCES Answers(id),
    FOREIGN KEY (userId) REFERENCES Users(id)
);

DROP TABLE IF EXISTS QuestionTags;
CREATE TABLE QuestionTags(
    id		INTEGER PRIMARY KEY,
    questionId	INTEGER NOT NULL,
    tag		varchar(30) NOT NULL,

    FOREIGN KEY (questionId) REFERENCES Questions(id)
);

DROP TABLE IF EXISTS QuestionVotes;
CREATE TABLE QuestionVotes(
    id		INTEGER PRIMARY KEY,
    questionId	INTEGER NOT NULL,
    voterId	INTEGER NOT NULL,
    isUpvote	BOOLEAN NOT NULL,

    FOREIGN KEY (questionId)	REFERENCES Questions(id),
    FOREIGN KEY (voterId) 	REFERENCES Users(id)
);

DROP TABLE IF EXISTS AnswerVotes;
CREATE TABLE AnswerVotes(
    id		INTEGER PRIMARY KEY,
    answerId	INTEGER NOT NULL,
    voterId	INTEGER NOT NULL,
    isUpvote	BOOLEAN NOT NULL,

    FOREIGN KEY (answerId)	REFERENCES Answers(id),
    FOREIGN KEY (voterId) 	REFERENCES Users(id)
);

DROP TABLE IF EXISTS CommentVotes;
CREATE TABLE CommentVotes(
    id		INTEGER PRIMARY KEY,
    commentId	INTEGER NOT NULL,
    voterId	INTEGER NOT NULL,
    isUpvote	BOOLEAN NOT NULL,

    FOREIGN KEY (commentId)	REFERENCES Comments(id),
    FOREIGN KEY (voterId) 	REFERENCES Users(id)
);

DROP VIEW IF EXISTS UsersWithScore;
CREATE VIEW UsersWithScore AS
SELECT
	U.id,
	U.name,
	U.email,
	(
	    SELECT
		(
		    SELECT count(*) FROM Users AS U2 
			INNER JOIN Questions AS Q 
			    ON U2.id = Q.userId
			INNER JOIN QuestionVotes as QV
			    ON QV.questionId = Q.id AND QV.isUpvote = 'TRUE'
		    WHERE U2.id = U.id
		)
		-
		(
		    SELECT count(*) FROM Users AS U2 
			INNER JOIN Questions AS Q 
			    ON U2.id = Q.userId
			INNER JOIN QuestionVotes as QV
			    ON QV.questionId = Q.id AND QV.isUpvote = 'FALSE'
		    WHERE U2.id = U.id
		)
		+
		(
		    SELECT count(*) FROM Users AS U2
			INNER JOIN Answers AS A 
			    ON U2.id = A.userId
			INNER JOIN AnswerVotes as AV
			    ON AV.answerId = A.id AND AV.isUpvote = 'TRUE'
		    WHERE U2.id = U.id
		)
		-
		(
		    SELECT count(*) FROM Users AS U2 
			INNER JOIN Answers AS A 
			    ON U2.id = A.userId
			INNER JOIN AnswerVotes as AV
			    ON AV.answerId = A.id AND AV.isUpvote = 'FALSE'
		    WHERE U2.id = U.id
		)
		+
		(
		    SELECT count(*) FROM Users AS U2 
			INNER JOIN Comments AS C 
			    ON U2.id = C.userId
			INNER JOIN CommentVotes as CV
			    ON CV.commentId = C.id AND CV.isUpvote = 'TRUE'
		    WHERE U2.id = U.id
		)
		-
		(
		    SELECT count(*) FROM Users AS U2 
			INNER JOIN Comments AS C 
			    ON U2.id = C.userId
			INNER JOIN CommentVotes as CV
			    ON CV.commentId = C.id AND CV.isUpvote = 'FALSE'
		    WHERE U2.id = U.id
		)
		+
		(
		    SELECT count(*) FROM Users AS U2
			INNER JOIN Questions AS Q
			    ON U2.id = Q.userId
		    WHERE U2.id = U.id
		)
		+
		(
		    SELECT count(*) FROM Users AS U2
			INNER JOIN Answers AS A
			    ON U2.id = A.userId
		    WHERE U2.id = U.id
		)
		+
		(
		    SELECT count(*) FROM Users AS U2
			INNER JOIN Comments AS C
			    ON U2.id = C.userId
		    WHERE U2.id = U.id
		)
	) AS reputation,
	(
	    SELECT 
		(
		    SELECT count(*) FROM QuestionVotes WHERE voterId = U.id
		)
		+
		(
		    SELECT count(*) FROM AnswerVotes WHERE voterId = U.id
		)
		+
		(
		    SELECT count(*) FROM CommentVotes WHERE voterId = U.id
		)
	) AS votes
	FROM Users AS U
;

/*
    SOME TEST ROWS
*/
/*
INSERT INTO Users ('name', 'pw', 'email') VALUES('Bobby', 'hemlit', 'abc@mail.com');
INSERT INTO Users ('name', 'pw', 'email') VALUES('Mos', 'ze teacher', 'mos@bth.se');
INSERT INTO Users ('name', 'pw', 'email') VALUES('Jeppe', 'brony', 'jesper.johnsson1995@hotmail.com');

INSERT INTO Questions ('header','question','userId') VALUES('BobbyFråga', 'Detta är bobbyfrågan', 1);
INSERT INTO Questions ('header','question','userId') VALUES('MosFråga', 'Detta är mosfrågan', 2);

INSERT INTO Answers ('answer','userId', 'questionId') VALUES ('Svaret är detta',1,2);

INSERT INTO Comments ('comment','userId', 'answerId') VALUES ('DAGS ATT KOMMENTERA LITE', 3, 1);

INSERT INTO QuestionTags('questionId', 'tag') VALUES(1, 'DATABASTAG');

INSERT INTO QuestionVotes ('questionId', 'voterId', 'isUpvote') VALUES (1, 1, 'TRUE');
INSERT INTO QuestionVotes ('questionId', 'voterId', 'isUpvote') VALUES (1, 1, 'FALSE');
INSERT INTO QuestionVotes ('questionId', 'voterId', 'isUpvote') VALUES (1, 1, 'FALSE');

INSERT INTO QuestionVotes ('questionId', 'voterId', 'isUpvote') VALUES (2, 1, 'TRUE');
INSERT INTO QuestionVotes ('questionId', 'voterId', 'isUpvote') VALUES (2, 1, 'TRUE');
INSERT INTO QuestionVotes ('questionId', 'voterId', 'isUpvote') VALUES (2, 1, 'TRUE');
*/
/*
    TESTS
*/
/*
SELECT '-----------Correlated?------';
SELECT * FROM UsersWithScore;

SELECT '------Users id--------';
SELECT id FROM Users;

SELECT '------Questions id--------';
SELECT id FROM Questions;
*/
/*
SELECT '--------------';
SELECT * FROM QuestionVotes;
*/