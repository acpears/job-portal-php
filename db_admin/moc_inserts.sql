-- ADMIN
DELETE FROM Admin;
INSERT INTO Admin (email, password)
VALUES (
        'adam@jobportal.ca',
        '$2y$10$dspgDGYJjSlhJogbc5AQVu5cCwqaghx2c2RZgb.VvM4Z7o7CaXkDm'
    );
-- SEEKER
DELETE FROM Seeker;
INSERT INTO Seeker (
        first_name,
        last_name,
        email,
        password,
        plan_name,
        security_question_id,
        security_answer
    )
VALUES (
        'Adam',
        'Pearson',
        'adam@gmail.ca',
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi',
        'basic',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    ),
    (
        'John',
        'Doe',
        'john@gmail.ca',
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi',
        'prime',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    ),
    (
        'Mark',
        'Robson',
        'mark@gmail.ca',
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi',
        'gold',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    ),
    (
        'James',
        'Blake',
        'james@gmail.ca',
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi',
        'basic',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    ),
    (
        'Jenny',
        'Macdonal',
        'jenny@gmail.ca',
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi',
        'prime',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    ),
    (
        'Adam',
        'Pearson',
        'adamcpearson@yahoo.ca',
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi',
        'prime',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    );
-- EMPLOYER
DELETE FROM Employer;
INSERT INTO Employer (
        name,
        email,
        password,
        plan_name,
        security_question_id,
        security_answer
    )
VALUES (
        'Company1',
        'company1@gmail.ca',
        '$2y$10$/Fxq3PgXmwwdvRoeF59KYegGAhoIy0qPeaCRVSUO4YvVhn4jobVou',
        'prime',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    ),
    (
        'Company2',
        'company2@gmail.ca',
        '$2y$10$/Fxq3PgXmwwdvRoeF59KYegGAhoIy0qPeaCRVSUO4YvVhn4jobVou',
        'gold',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    ),
    (
        'Company3',
        'company3@gmail.ca',
        '$2y$10$/Fxq3PgXmwwdvRoeF59KYegGAhoIy0qPeaCRVSUO4YvVhn4jobVou',
        'prime',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    ),
    (
        'Company4',
        'company4@gmail.ca',
        '$2y$10$/Fxq3PgXmwwdvRoeF59KYegGAhoIy0qPeaCRVSUO4YvVhn4jobVou',
        'gold',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    ),
    (
        'Company5',
        'company5@gmail.ca',
        '$2y$10$/Fxq3PgXmwwdvRoeF59KYegGAhoIy0qPeaCRVSUO4YvVhn4jobVou',
        'prime',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    ),
    (
        'Digicast',
        'adamcpearson@yahoo.ca',
        '$2y$10$/Fxq3PgXmwwdvRoeF59KYegGAhoIy0qPeaCRVSUO4YvVhn4jobVou',
        'gold',
        1,
        '$2y$10$kg8vewi0Na/cSjGhvEonyuC3jL86HdbG3cnNpaTaa6X.R2ld5DROi'
    );
DELETE FROM Payment_Method;
-- CREDIT CARDS
INSERT INTO Credit_Card (card_number, expiry_date, owner_name)
VALUES (
        '7462349752198894',
        '2024-07-01',
        'Adam Pearson'
    ),
    ('4757335130820581', '2023-02-01', 'John Doe'),
    ('3954840160603404', '2029-09-01', 'Mark Robson'),
    ('3251797293227656', '2025-06-01', 'James Blake'),
    (
        '4551423440079243',
        '2022-04-01',
        'Jacque Wardle'
    ),
    (
        '8881930738841442',
        '2023-03-01',
        'Owain Waller'
    ),
    (
        '9293400943999181',
        '2024-09-01',
        'Eleanor Mclaughlin'
    ),
    (
        '0286430660783407',
        '2025-01-01',
        'Caine Barber'
    ),
    (
        '0872895098465729',
        '2029-12-01',
        'Eleanor Mclaughlin'
    ),
    (
        '1176380426386032',
        '2022-11-01',
        'Caine Barber'
    );
-- BANK ACCOUNT
INSERT INTO Bank_Account (account_number, owner_name)
VALUES ("671969849492", "Kato Meyers"),
    ("586603549707", "Mechelle Cantu"),
    ("344141230088", "Rahim Morse"),
    ("709754005390", "Burke Alston"),
    ("636636564309", "Hilel Cummings"),
    ("535912785681", "Bruce Flynn"),
    ("167237884678", "Channing Roberson"),
    ("506709171597", "Sopoline Melendez"),
    ("148266068485", "Pearl Newman"),
    ("314431436214", "Joel Salas");
INSERT INTO Registered_Payment_Method (payment_method_id, wallet_id)
VALUES (1, 1),
    (2, 4),
    (3, 7),
    (4, 3),
    (5, 4),
    (6, 9),
    (7, 10),
    (8, 3),
    (9, 2),
    (10, 7),
    (11, 5),
    (12, 9),
    (13, 2),
    (14, 5),
    (15, 3),
    (16, 8),
    (17, 6),
    (18, 9),
    (19, 6),
    (20, 3);
DELETE FROM Job_Category;
INSERT INTO Job_Category (name)
VALUES ('Engineering'),
    ('Marketing'),
    ('Customer Services'),
    ('Finance'),
    ('Legal');
--  Auto-generated SQL script #202108090100
DELETE FROM Job_Posting;
INSERT INTO Job_Posting (
        title,
        description,
        max_fill_qty,
        date_posted,
        category_name,
        employer_id
    )
VALUES (
        "Electrical Engineer",
        "pharetra sed, hendrerit a, arcu. Sed et libero. Proin mi. Aliquam gravida mauris ut mi. Duis risus odio, auctor vitae, aliquet nec, imperdiet nec, leo. Morbi neque tellus, imperdiet non, vestibulum nec, euismod in,",
        4,
        "2021-08-04",
        "Engineering",
        4
    ),
    (
        "Social Media Manager",
        "fringilla cursus purus. Nullam scelerisque neque sed sem egestas blandit. Nam nulla magna, malesuada vel, convallis in, cursus et, eros. Proin ultrices. Duis volutpat nunc sit amet metus. Aliquam erat volutpat. Nulla facilisis.",
        3,
        "2021-07-09",
        "Finance",
        4
    ),
    (
        "Brand Manager",
        "nulla. In tincidunt congue turpis. In condimentum. Donec at arcu. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec tincidunt. Donec vitae erat vel pede blandit congue.",
        3,
        "2021-06-24",
        "Engineering",
        1
    ),
    (
        "Public Relations Manager",
        "bibendum sed, est. Nunc laoreet lectus quis massa. Mauris vestibulum, neque sed dictum eleifend, nunc risus varius orci, in consequat enim diam vel arcu. Curabitur ut odio vel est tempor bibendum. Donec felis orci, adipiscing non, luctus",
        2,
        "2021-05-07",
        "Finance",
        3
    ),
    (
        "Human Resources Manager",
        "eget mollis lectus pede et risus. Quisque libero lacus, varius et, euismod et, commodo at, libero. Morbi accumsan laoreet ipsum. Curabitur consequat, lectus sit amet luctus vulputate, nisi sem semper",
        5,
        "2021-07-01",
        "Legal",
        1
    ),
    (
        "Budget Analyst",
        "sit amet risus. Donec egestas. Aliquam nec enim. Nunc ut erat. Sed nunc est, mollis non, cursus non, egestas a, dui. Cras pellentesque. Sed dictum. Proin eget odio. Aliquam vulputate ullamcorper magna. Sed eu eros.",
        5,
        "2021-06-11",
        "Legal",
        5
    ),
    (
        "Financial Advisor",
        "imperdiet dictum magna. Ut tincidunt orci quis lectus. Nullam suscipit, est ac facilisis facilisis, magna tellus faucibus leo, in lobortis tellus justo sit amet nulla. Donec non justo. Proin non massa non ante bibendum ullamcorper.",
        2,
        "2021-08-06",
        "Customer Services",
        5
    ),
    (
        "Patent Agent",
        "Duis volutpat nunc sit amet metus. Aliquam erat volutpat. Nulla facilisis. Suspendisse commodo tincidunt nibh. Phasellus nulla. Integer vulputate, risus a ultricies adipiscing, enim mi tempor lorem, eget mollis lectus pede et risus. Quisque libero lacus, varius et, euismod",
        5,
        "2021-06-22",
        "Customer Services",
        2
    ),
    (
        "Legal Secretary",
        "porttitor scelerisque neque. Nullam nisl. Maecenas malesuada fringilla est. Mauris eu turpis. Nulla aliquet. Proin velit. Sed malesuada augue ut lacus. Nulla tincidunt, neque vitae semper egestas, urna justo faucibus lectus, a sollicitudin",
        2,
        "2021-06-18",
        "Customer Services",
        4
    ),
    (
        "Web Developer",
        "mi. Aliquam gravida mauris ut mi. Duis risus odio, auctor vitae, aliquet nec, imperdiet nec, leo. Morbi neque tellus, imperdiet non, vestibulum nec, euismod in, dolor. Fusce feugiat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam auctor,",
        2,
        "2021-05-07",
        "Marketing",
        4
    );
DELETE FROM Job_Application;
INSERT INTO Job_Application (seeker_id, job_posting_id, date_applied)
VALUES (1, 3, '2021-08-01'),
    (2, 3, '2021-08-01'),
    (4, 9, '2021-08-01'),
    (5, 10, '2021-08-01'),
    (3, 6, '2021-08-01'),
    (4, 2, '2021-08-01'),
    (2, 2, '2021-08-01'),
    (2, 7, '2021-08-01'),
    (1, 8, '2021-08-01'),
    (5, 8, '2021-08-01'),
    (4, 4, '2021-08-01'),
    (1, 7, '2021-08-01');