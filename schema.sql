CREATE DATABASE IF NOT EXISTS gradstart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gradstart;

CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE graduate (
    user_id INT PRIMARY KEY,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
) ENGINE=InnoDB;

CREATE TABLE admin (
    user_id INT PRIMARY KEY,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
) ENGINE=InnoDB;

CREATE TABLE opportunity (
    opportunity_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    company VARCHAR(150) NOT NULL,
    specialization VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(100) NOT NULL,
    duration VARCHAR(50) NOT NULL,
    skills_to_be_acquired TEXT NOT NULL,
    deadline DATE NOT NULL,
    application_link VARCHAR(512) NOT NULL,
    admin_id INT NOT NULL,
    FOREIGN KEY (admin_id) REFERENCES admin(user_id)
) ENGINE=InnoDB;

CREATE TABLE bookmark (
    bookmark_id INT AUTO_INCREMENT PRIMARY KEY,
    graduate_id INT NOT NULL,
    opportunity_id INT NOT NULL,
    UNIQUE (graduate_id, opportunity_id),
    FOREIGN KEY (graduate_id) REFERENCES graduate(user_id),
    FOREIGN KEY (opportunity_id) REFERENCES opportunity(opportunity_id)
) ENGINE=InnoDB;

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL
) ENGINE=InnoDB;

INSERT INTO user (name, email, password_hash) VALUES
('Admin', 'admin@gradstart.com', '$2y$10$6BrFLrXuDH3Q8/MFPLOQt.SaVSjnhjTatIeaUE1eL8ux944Z3HInK');

INSERT INTO admin (user_id) VALUES (1);

INSERT INTO opportunity (title, company, specialization, location, duration, skills_to_be_acquired, description, deadline, application_link, admin_id) VALUES
('Co-op Training Program at SDAIA', 'SDAIA (Saudi Data & AI Authority)', 'Computer Science, Information Technology, Information Systems, Software Engineering', 'Saudi Arabia', 'Second semester of 1448 AH', 'Artificial Intelligence, Data Analysis', 'Registration is open for the SDAIA Academy co-op training program for university students for the second semester of 1448 AH. Registration runs from October 18 to October 24, 2026.', '2026-10-24', 'https://athkax.sdaia.gov.sa/coop', 1),
('Istakshif Co-op Training Program 2027', 'National Center for Government Resource Systems (NCGR)', 'Computer Science, Information Technology, Information Systems, Software Engineering', 'Saudi Arabia', '2 to 6 months', 'Digital Government, Teamwork, Communication', 'NCGR gives male and female students the chance to apply for its 2027 co-op training program, Istakshif, to support national talent and develop their skills.\n\nRequirements:\n- Saudi nationality\n- Training period between 2 and 6 months\n- Cumulative GPA of at least 2.5 out of 4 or 3.5 out of 5\n- Passing all courses and academic prerequisites, and being available for full-time training\n- Passing the personal interview\n\nApplication deadline: October 31, 2026. You can apply through the NCGR page or LinkedIn.', '2026-10-31', 'https://lnkd.in/eVA2chdv', 1),
('Co-op Training Program 2027', 'Elm', 'Computer Science, Information Technology, Information Systems, Software Engineering', 'Saudi Arabia', '4 to 7 months', 'English, Software Development, Project Management', 'Join the Elm co-op training program and launch towards a promising professional future.\n\nRequirements:\n- Training period of 4 to 7 months\n- Cumulative GPA of at least 4 out of 5 or 3.2 out of 4\n- Co-op training must be a graduation requirement at your university\n- Good command of English\n\nApplication stages for the January 2027 cohort:\n1. Application: October 18 to November 4, 2026\n2. Screening and interviews: November 8 to 22, 2026\n3. Results announced: November 24, 2026\n4. Start dates: January 10 or January 24, 2027', '2026-11-04', 'https://lnkd.in/dn3jT3uW', 1);
