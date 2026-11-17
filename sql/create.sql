CREATE DATABASE comet_apparel;

USE comet_apparel;

CREATE TABLE Clothing_Line(
    CL_Name VARCHAR(30) PRIMARY KEY,
    Type VARCHAR(20),
    First_Available DATETIME NOT NULL,
    Last_Available DATETIME,
    On_Sale FLOAT NOT NULL,
    Price_Tier VARCHAR(10),
    Description VARCHAR(150)
);

CREATE TABLE Clothing_Item (
    Item_No INT PRIMARY KEY,
    Gender CHAR(1) NOT NULL DEFAULT 'U',
    Size VARCHAR(3) NOT NULL,
    Material VARCHAR(15) NOT NULL,
    Price FLOAT NOT NULL,
    Current_Stock INT NOT NULL
);

CREATE TABLE Customer (
    CustomerID INT AUTO_INCREMENT PRIMARY KEY,
    Phone_No CHAR(10),
    F_Name VARCHAR(20) NOT NULL,
    L_Name VARCHAR(20) NOT NULL,
    DOB DATETIME,
    Email VARCHAR(64)
);

CREATE TABLE Sale (
    TransactionID INT PRIMARY KEY,
    CustomerID INT,
    Date DATETIME NOT NULL,
    Cost FLOAT NOT NULL DEFAULT 0,
    CONSTRAINT CustomerFK FOREIGN KEY (CustomerID) REFERENCES Customer (CustomerID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE Sales_Line (
    TransactionID INT NOT NULL,
    Item_No INT NOT NULL,
    Sale_Quantity INT NOT NULL DEFAULT 0,
    CONSTRAINT Sales_LinePK PRIMARY KEY (TransactionID, Item_No),
    CONSTRAINT TransactionFK FOREIGN KEY (TransactionID) REFERENCES Sale (TransactionID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT ItemFK FOREIGN KEY (Item_No) REFERENCES Clothing_Item (Item_No)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE C_Line_Contains (
    CL_Name VARCHAR(30) NOT NULL,
    Item_No INT NOT NULL,
    CONSTRAINT CLinePK PRIMARY KEY (CL_Name, Item_No),
    CONSTRAINT CLNameFK FOREIGN KEY (CL_Name) REFERENCES Clothing_Line (CL_Name)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT ItemNoFK FOREIGN KEY (Item_No) REFERENCES Clothing_Item (Item_No)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);