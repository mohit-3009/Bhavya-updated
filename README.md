CREATE TABLE onepayment (
    id INT(11) NOT NULL AUTO_INCREMENT,
    payment_reason VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
    payment_date DATE DEFAULT NULL,
    one_time_amount DECIMAL(10, 2) DEFAULT NULL,
    PRIMARY KEY (id)
);
