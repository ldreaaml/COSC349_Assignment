CREATE TABLE times(
	name varchar(30),
	timeDifference varchar(30),
	PRIMARY KEY (name)
);

/*
PST time, calculate differences for all by UTC time
*/
INSERT INTO times VALUES ('New Zealand','+12:00');
INSERT INTO times VALUES ('Western Australia','+8:00');
INSERT INTO times VALUES ('Antarctica- McMurdo Station','+12:00');
INSERT INTO times VALUES ('America ET','-5:00');
INSERT INTO times VALUES ('Russia - Moscow','+3:00');
INSERT INTO times VALUES ('France','+1:00');
INSERT INTO times VALUES ('United Kingdom','0:00');
INSERT INTO times VALUES ('Canada','+7:00');
INSERT INTO times VALUES ('Denmark','+1:00');
INSERT INTO times VALUES ('South Africa','+2:00');
INSERT INTO times VALUES ('Spain','+1:00');
INSERT INTO times VALUES ('El Salvador','-6:00');
