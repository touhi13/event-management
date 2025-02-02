-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create events table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    event_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    max_capacity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create attendees table
CREATE TABLE attendees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendee (event_id, email)
);

-- Optional: Add some initial data
INSERT INTO users (username, email, password, is_admin) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);
-- Note: The password hash above is for 'password' 

-- Generate test data
-- First, create admin user
INSERT INTO users (username, email, password, is_admin) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

-- Generate 99 more users
DELIMITER //
CREATE PROCEDURE generate_test_data()
BEGIN
    DECLARE i INT DEFAULT 1;
    
    -- Generate Users
    WHILE i <= 99 DO
        INSERT INTO users (username, email, password, is_admin) VALUES
        (CONCAT('user', i), 
         CONCAT('user', i, '@example.com'),
         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
         IF(i <= 5, TRUE, FALSE));
        SET i = i + 1;
    END WHILE;

    -- Generate Events
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO events (user_id, name, description, event_date, location, max_capacity) VALUES
        (FLOOR(1 + RAND() * 100),
         CONCAT('Event ', i),
         CONCAT('Description for event ', i, '. This is a test event with detailed information.'),
         DATE_ADD(CURRENT_DATE, INTERVAL FLOOR(RAND() * 365) DAY),
         CASE FLOOR(1 + RAND() * 5)
            WHEN 1 THEN 'New York Convention Center'
            WHEN 2 THEN 'Los Angeles Exhibition Hall'
            WHEN 3 THEN 'Chicago Conference Center'
            WHEN 4 THEN 'Houston Event Space'
            ELSE 'Miami Convention Center'
         END,
         FLOOR(50 + RAND() * 450));
        SET i = i + 1;
    END WHILE;

    -- Generate Attendees
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO attendees (event_id, name, email, phone) VALUES
        (FLOOR(1 + RAND() * 100),
         CONCAT('Attendee ', i),
         CONCAT('attendee', i, '@example.com'),
         CONCAT('555-', LPAD(FLOOR(RAND() * 9999), 4, '0')));
        SET i = i + 1;
    END WHILE;
END //
DELIMITER ;

-- Execute the procedure
CALL generate_test_data();

-- Clean up
DROP PROCEDURE IF EXISTS generate_test_data; 
