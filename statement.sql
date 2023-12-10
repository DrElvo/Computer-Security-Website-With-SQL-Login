CREATE TABLE IF NOT EXISTS accounts (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `encryptedEmail` varbinary(2048) NOT NULL,
    `encryptedNumber` varbinary(2048) NOT NULL,
    `verifyCode` varchar(6) NOT NULL,
    `encryptedQuestion` varbinary(2048) NOT NULL,
    `encryptedAnswer` varbinary(2048) NOT NULL,
    `iv` varbinary(16) NOT NULL,
    `verified` TINYINT(1) NOT NULL DEFAULT 0,
    `passwordLockoutCount` TINYINT(1) NOT NULL DEFAULT 0,
    `passwordToken` varchar(6) NULL,
    `passwordResetExpiry` DATETIME NULL,
    `verifiedExpiry` DATETIME NULL,
    `lockout` DATETIME NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO accounts (`id`, `username`, `password`, `encryptedEmail`, `encryptedNumber`) VALUES (1, 'admin', '$2y$10$JEHiJW9RMmqFlWJb4kg0Wu/CpBaCas2Vj1Ob/apa/E6g5Cv14oXs2', 'admin@admin.com', '+44 4444 444444');

CREATE TABLE IF NOT EXISTS comments (
    `commentID` int(11) NOT NULL AUTO_INCREMENT,
    `id` int(11) NOT NULL,
    `comment` varchar(255) NOT NULL,
    `contact` varchar(100) NOT NULL,
    `contactType` varchar(8) NOT NULL,
    `postedOn` DATETIME NOT NULL,
    `nameOfFile` varchar(255) NOT NULL,
    PRIMARY KEY (commentID)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
