-- raw password is admin
INSERT INTO user
    (id, username, password, fullname)
    VALUES
    (1, 'admin', '$2y$10$Q8Zs5kwL6bjB02hgtKu5GOB.s0CCAysIf2jcskii.jox8Eyn5v8yu', 'Eko Kurniawan');

INSERT INTO category
    (id, category)
    VALUES
    (1, 'Hello'),
    (2, 'Uncategorized'),
    (3, 'Good');

INSERT INTO post
    (id, title, slug, content, created_at, user_id)
    VALUES
    (1, 'Just Good', 'just-good', '<p>When the tool is used properly.</p>', now(), 1),
    (2, 'But not that Good', 'but-not-that-good', '<p>Simplification can take more complex process.</p>', now(), 1),
    (3, 'Simple is Good', 'simple-is-good', '<p>Reduce the complexicity.</p>', now(), 1),
    (4, 'Hello World', 'hello-world', '<p>Hello World.</p>', now(), 1),
    (5, 'Look the Ipsum', 'lorem-ipsum', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', now(), 1);

INSERT INTO post_category
    (post_id, category_id)
    VALUES
    (5, 2),
    (4, 1),
    (4, 3),
    (3, 1),
    (2, 1),
    (1, 1);
