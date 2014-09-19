-- this deals with the idividual accounts (ex, a savings account at a bank)
CREATE TABLE saving_accounts (
	id INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id INT (11),
	NAME VARCHAR (255),
-- interest rate will be in units of hundreths of a cent, and balance in cents
	interest_rate INT (11),
	balance INT (11),
-- needed a clean way to track the yearly additions, and this makes it pretty easy
    is_yearly_addition INT (1)
);

-- deals with the transactions that happen for a given account
CREATE TABLE transactions (
	id INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id INT (11),
	account_id INT (11),
	is_withdrawal INT (1),
	is_deposit INT (1),
-- amount is in cents
	amount INT (11),
	transaction_date TIMESTAMP NOT NULL DEFAULT NOW(),
-- optional note
	note TEXT
);

-- track the yearly additions for every year.
CREATE TABLE yearly_additions (
	id INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	year INT (11),
-- as always, amount is in cents
	amount INT (11)
);

-- I know the values for each of the previous years, so mind as well just insert them now
INSERT INTO yearly_additions (year, amount) VALUES (2009, 500000);
INSERT INTO yearly_additions (year, amount) VALUES (2010, 500000);
INSERT INTO yearly_additions (year, amount) VALUES (2011, 500000);
INSERT INTO yearly_additions (year, amount) VALUES (2012, 500000);
INSERT INTO yearly_additions (year, amount) VALUES (2013, 550000);
INSERT INTO yearly_additions (year, amount) VALUES (2014, 550000);




