CREATE DATABASE if not exists lovegaming;

USE lovegaming;

CREATE TABLE if not exists role (
    roleId INT AUTO_INCREMENT PRIMARY KEY,
    roleName VARCHAR(255)
);

CREATE TABLE if not exists user (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    roleId INT default 1,
    dateOfBirth DATE NOT NULL,
    profilePic VARCHAR(255) default "/images/profilePics/default.jpg",
    telephone INT,
    FOREIGN KEY (roleId) REFERENCES role(roleId) ON DELETE CASCADE
);

CREATE TABLE if not exists productType (
    productTypeId INT AUTO_INCREMENT PRIMARY KEY,
    productType VARCHAR(255) not null default "Game"
);

CREATE TABLE if not exists product (
    productId INT AUTO_INCREMENT PRIMARY KEY,
    productTypeId INT,
    releaseDate DATE NOT NULL,
    price FLOAT,
    name VARCHAR(255) NOT NULL UNIQUE,
    description VARCHAR(255) NOT NULL,
    picture VARCHAR(255) default "/images/products/default.jpg",
    stock INT(255) default 0,
    FOREIGN KEY (productTypeId) REFERENCES productType(productTypeId) ON DELETE CASCADE
);

CREATE TABLE wishlist (
  userId INT,
  productId INT,
  PRIMARY KEY (userId, productId),
  FOREIGN KEY (userId) REFERENCES user(userId) ON DELETE CASCADE,
  FOREIGN KEY (productId) REFERENCES product(productId) ON DELETE CASCADE
);

CREATE TABLE if not exists productHistory (
    productHistoryId INT AUTO_INCREMENT PRIMARY KEY,
    userId INT,
    productId INT,
    saleDate DATE default CURDATE(),
    FOREIGN KEY (userId) REFERENCES user(userId) ON DELETE CASCADE,
    FOREIGN KEY (productId) REFERENCES product(productId) ON DELETE CASCADE
);

CREATE TABLE if not exists productReview (
    productReviewId INT AUTO_INCREMENT PRIMARY KEY,
    productHistoryId INT,
    productReview VARCHAR(255) NOT NULL,
    productReviewPic VARCHAR(255) default "/images/reviews/default.jpg",
    productScore INT(255) NOT NULL,
    UNIQUE (productReviewId, productHistoryId),
    FOREIGN KEY (productHistoryId) REFERENCES productHistory(productHistoryId) ON DELETE CASCADE
);

INSERT INTO role (roleName) VALUES ('usuario');
INSERT INTO role (roleName) VALUES ('admin');
INSERT INTO role (roleName) VALUES ('superadmin');

INSERT INTO productType (productType) VALUES ('Game');
INSERT INTO productType (productType) VALUES ('Decoration');
INSERT INTO productType (productType) VALUES ('Peripherals');

INSERT INTO `user`(`email`, `password`, `username`, `roleId`, `dateOfBirth`, `telephone`) VALUES ('aythamicm@gmail.com', '$2y$10$cisp2tNi9G4krWHZmpQ4ruRGjKQ00VlD6eZlAC./pAAjHMWbrdulm', 'aythamicm', '3', '1996-12-16', '123456789')