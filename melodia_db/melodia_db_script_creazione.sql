CREATE DATABASE `melodia_db`;
USE `melodia_db`;

CREATE TABLE accounts (
    username VARCHAR(256) NOT NULL,
    password CHAR(128) NOT NULL, -- se usiamo sha-512 per hashing, altrimenti impostare il tipo giusto
    active TINYINT(1) NOT NULL, -- 1 se l'account è attivo, 0 altrimenti
	PRIMARY KEY (username)
);

CREATE TABLE messages (
	message_id INT NOT NULL,
    name VARCHAR(256) NOT NULL,
    email VARCHAR(256) NOT NULL,
	message VARCHAR(2048) NOT NULL,
	creation_date DATETIME NOT NULL, -- data inserimento, visibile solo dall'admin che visualizza un messaggio
	first_viewed_by VARCHAR(256), -- la prima volta che il messaggio viene visualizzato inserisco lo username, NULL altrimenti
	PRIMARY KEY (message_id),
	FOREIGN KEY (first_viewed_by) REFERENCES accounts(username)
);

CREATE TABLE categories (
	category_id INT NOT NULL,
    name VARCHAR(256) NOT NULL, -- popolare con chitarre, batterie, pianoforti
	PRIMARY KEY (category_id)
);

CREATE TABLE images (
	image_id INT NOT NULL,
    image blob NOT NULL,
	PRIMARY KEY (image_id)
);

CREATE TABLE products (
	products_id INT NOT NULL,
	name VARCHAR(256) NOT NULL,
	category_id INT NOT NULL,
	description VARCHAR(2048) NOT NULL,
	price DECIMAL NOT NULL,
	discounted_price DECIMAL, -- se NULL, allora non c'è sconto
	image_id INT NOT NULL,
	created_by VARCHAR(256) NOT NULL, -- riporta l'username dell'account creatore
	creation_date DATETIME NOT NULL,
	last_edited_by VARCHAR(256) NOT NULL, -- riporta l'username dell'account che ha fatto la modifica più recente (uguale a created_by per il prodotto appena creato)
	last_edited_date DATETIME NOT NULL,
	-- questi ultimi 4 campi sono visibili solo dall'area riservata, quando si modifica un prodotto già inserito in precedenza
	PRIMARY KEY (products_id),
	FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (image_id)  REFERENCES images(image_id),
	FOREIGN KEY (created_by) REFERENCES accounts(username),
	FOREIGN KEY (last_edited_by) REFERENCES accounts(username)
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