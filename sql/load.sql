SET FOREIGN_KEY_CHECKS=0; -- Disable foreign key constraints during upload

SET GLOBAL LOCAL_INFILE=1;

-- LOAD DATA FROM CSV INTO CLOTHING_LINE TABLE
LOAD DATA INFILE 'C:\\ProgramData\\MySQL\\MySQL Server 26.10\\Uploads\\Clothing_Line.csv'
INTO TABLE Clothing_Line
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS; -- Skips header row

-- LOAD DATA FROM CSV INTO CLOTHING_ITEM TABLE
LOAD DATA INFILE 'C:\\ProgramData\\MySQL\\MySQL Server 26.10\\Uploads\\Clothing_Item.csv'
INTO TABLE Clothing_Item
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS; -- Skips header row

-- LOAD DATA FROM CSV INTO CUSTOMER TABLE
LOAD DATA INFILE 'C:\\ProgramData\\MySQL\\MySQL Server 26.10\\Uploads\\Customer.csv'
INTO TABLE Customer
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS; -- Skips header row

-- LOAD DATA FROM CSV INTO SALE TABLE
LOAD DATA INFILE 'C:\\ProgramData\\MySQL\\MySQL Server 26.10\\Uploads\\Sale.csv'
INTO TABLE Sale
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS; -- Skips header row

-- LOAD DATA FROM CSV INTO SALES_LINE TABLE
LOAD DATA INFILE 'C:\\ProgramData\\MySQL\\MySQL Server 26.10\\Uploads\\Sales_Line.csv'
INTO TABLE Sales_Line
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS; -- Skips header row

-- LOAD DATA FROM CSV INTO C_LINE_CONTAINS TABLE
LOAD DATA INFILE 'C:\\ProgramData\\MySQL\\MySQL Server 26.10\\Uploads\\C_Line_Contains.csv'
INTO TABLE C_Line_Contains
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS; -- Skips header row

SET FOREIGN_KEY_CHECKS=1; -- Enable foreign key constraints now that uploading data is done!