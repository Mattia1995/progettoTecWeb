CREATE DATABASE `melodia_db`;
USE `melodia_db`;

CREATE TABLE accounts (
    username VARCHAR(256) NOT NULL,
    password CHAR(128) NOT NULL, -- se usiamo sha-512 per hashing, altrimenti impostare il tipo giusto
    active TINYINT(1) NOT NULL, -- 1 se l'account è attivo, 0 altrimenti
	PRIMARY KEY (username)
);

CREATE TABLE message_states (
	state_id INT NOT NULL,
    name VARCHAR(256) NOT NULL,
	PRIMARY KEY (state_id)
);

CREATE TABLE messages (
	message_id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(256) NOT NULL,
    email VARCHAR(256) NOT NULL,
	message VARCHAR(2048) NOT NULL,
	state_id INT NOT NULL,
	creation_date DATE NOT NULL,
	PRIMARY KEY (message_id),
	FOREIGN KEY (state_id) REFERENCES message_states(state_id)
);

CREATE TABLE categories (
	category_id INT NOT NULL,
    name VARCHAR(256) NOT NULL, -- popolare con chitarre, batterie, pianoforti
	PRIMARY KEY (category_id)
);

CREATE TABLE products (
	product_id INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(256) NOT NULL,
	category_id INT NOT NULL,
	description VARCHAR(2048) NOT NULL,
	price DECIMAL(10,2) NOT NULL,
	discounted_price DECIMAL(10,2), -- se NULL, allora non c'è sconto
	brand VARCHAR(256) NOT NULL,
	color VARCHAR(256) NOT NULL,
	material VARCHAR(256) NOT NULL,
	image_url VARCHAR(256) NOT NULL,
	PRIMARY KEY (product_id),
	FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

INSERT INTO `melodia_db`.`accounts`
(`username`,
`password`,
`active`)
VALUES
('admin',
'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec', -- sha-512 per password "admin"
1);

INSERT INTO `melodia_db`.`categories`
(`category_id`,
`name`)
VALUES
(1,
'chitarre');

INSERT INTO `melodia_db`.`categories`
(`category_id`,
`name`)
VALUES
(2,
'pianoforti');

INSERT INTO `melodia_db`.`categories`
(`category_id`,
`name`)
VALUES
(3,
'batterie');

INSERT INTO `melodia_db`.`message_states`
(`state_id`,
`name`)
VALUES
(1,
'Da leggere');

INSERT INTO `melodia_db`.`message_states`
(`state_id`,
`name`)
VALUES
(2,
'Evaso');