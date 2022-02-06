CREATE TABLE IF NOT EXISTS `user`(
    id                  INTEGER(10) PRIMARY KEY AUTO_INCREMENT,
    email               VARCHAR(150) NOT NULL,
    username            VARCHAR(50) NOT NULL,
    `password`          VARCHAR(50) NOT NULL,
    `admin`             BOOLEAN DEFAULT FALSE,
    `delete`            BOOLEAN DEFAULT FALSE,
    delete_at           INTEGER(10) DEFAULT 0,
    update_at           INTEGER(10) DEFAULT 0,
    insert_at           INTEGER(15) DEFAULT 0
);

CREATE TABLE IF NOT EXISTS activity(
    id                  INTEGER(10) PRIMARY KEY AUTO_INCREMENT,
    title               VARCHAR(100) NOT NULL,
    `description`       VARCHAR(250) NOT NULL,
    `date`              TIMESTAMP DEFAULT NOW(),
    `delete`            BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS player(
    id                  VARCHAR(10) NOT NULL PRIMARY KEY,
    `name`              VARCHAR(50) NOT NULL,
    `role`              VARCHAR(10) NOT NULL,
    `image`             VARCHAR(200) NOT NULL,
    cant                INTEGER(10) NOT NULL DEFAULT 1,
    donations           INTEGER(10) NOT NULL,
    donationsReceived   INTEGER(10) NOT NULL,
    inClan              BOOLEAN DEFAULT TRUE,
    `date`              TIMESTAMP DEFAULT NOW(),
    `status`            VARCHAR(10) DEFAULT 'active' CHECK(`status` IN ('wait', 'active', 'break'))
);

CREATE TABLE IF NOT EXISTS donations(
    id                  VARCHAR(10) NOT NULL PRIMARY KEY,
    donations           INTEGER(10) NOT NULL,
    donationsReceived   INTEGER(10) NOT NULL,
    date_at             INTEGER(15) DEFAULT 0,
    update_at           INTEGER(15) DEFAULT 0,
    `delete`            BOOLEAN DEFAULT FALSE              
);

CREATE TABLE IF NOT EXISTS listwar(
    id                  INTEGER(10) PRIMARY KEY AUTO_INCREMENT,
    list                VARCHAR(1000) NOT NULL,
    members             INTEGER(10) NOT NULL,
    `description`       VARCHAR(250),
    `delete`            BOOLEAN DEFAULT FALSE,
    `date`              TIMESTAMP DEFAULT NOW(),
    update_at           INTEGER(15) DEFAULT 0,
    delete_at           INTEGER(15) DEFAULT 0
);

drop table listwar;

INSERT INTO `user` (`id`, `email`, `username`, `password`, `admin`) VALUES
(1, 'lomasduro17@hotmail.com', 'Rafaga21', '4e46088ec803ef3a0ee9bf53f518cd42', 1),
(2, 'esmeiribaezperez@gmail.com', 'Memeiry', '4775c8560be8158f2457d9d3d44bf9f3', 0),
(3, 'israelperezmasle2@gmail.com', 'BlackBullTxT', '88a9c649af36b82512cc5f5bf2026c19', 0),
(4, 'ffefito13@hotmail.com', 'anacleto24', '1b29a86178d95841a5ce33d8db824992', 0),
(5, 'leandroesuero21@gmail.com', 'Fussione', 'dbb1f8b2cabb843c5680047535ff845d', 0),
(6, 'matigumuciod@gmail.com', 'Yack', '767d39519e14d89043567afad2cd7da8', 0),
(7, 'tomcledrs@gmail.com', 'Manu', 'dca07b8dd3dea5cc46148ede55001164', 0),
(8, 'clanhechiceros@gmail.com', 'Angel', 'a0ea2bee8aeed42cd2e06384cb79a253', 0),
(9, 'pepe8alzojeda@gmail.com', 'pepe', '015217250d27ff258b12bb9f0d61d473', 0),
(10, 'eltetemonaguillo@gmail.com', 'GRIEFEER 721', '4ec3748dfecf9732dac3eac7ac7bff03', 0),
(11, 'eltetemonaguillo@gmail.com', 'JUAN', '4ec3748dfecf9732dac3eac7ac7bff03', 0),
(12, 'benja16echeverria@gmail.com', 'Benja10692', 'cfd909a7e48a63555e2da485665282d1', 0),
(13, 'jacruzbihan2006@gmail.com', 'Coto 2024 ', 'd795d4e5192dc695f1f31bb306fb3af1', 0),
(14, 'll3202118@gmail.com', 'Angel GT', '310b51dbf89733607e6dc6f7ec196b61', 0);