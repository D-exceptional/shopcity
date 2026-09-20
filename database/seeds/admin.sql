--
-- Dumping data for table users
-- Default admin password is `adminAccess1`
--
INSERT INTO users (user_id, avatar, firstname, lastname, email, contact, country, user_state, user_password, user_role, user_status, created_at, updated_at) 
VALUES (1, 'None', 'Admin', 'Account', 'admin@gmail.com', '+23491111111', 'Nigeria', 'N/A', '$2y$12$5THmG78bGy0.XbLzNimlpOJiCmx.wF3VOXgd6ciOWMrC79YT9X10C', 'Admin', 'Active', '2026-09-20 00:00:00', NULL);

-- --------------------------------------------------------