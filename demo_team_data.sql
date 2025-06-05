-- Demo MLM Team Data for user yovodi8736@baxima.com
-- Replace USER_ID below with your actual user id if different (from phpMyAdmin)

SET @root_user := 7;

-- Level 1 (Direct Referrals)
INSERT INTO users (email, created_at, referred_by, status) VALUES ('direct1@example.com', '2024-06-01', @root_user, 'active');
SET @direct1 := LAST_INSERT_ID();
INSERT INTO users (email, created_at, referred_by, status) VALUES ('direct2@example.com', '2024-07-15', @root_user, 'active');
SET @direct2 := LAST_INSERT_ID();

-- Level 2 (Indirect)
INSERT INTO users (email, created_at, referred_by, status) VALUES ('indirect1@example.com', '2024-08-01', @direct1, 'active');
SET @indirect1 := LAST_INSERT_ID();
INSERT INTO users (email, created_at, referred_by, status) VALUES ('indirect2@example.com', '2024-09-10', @direct2, 'active');
SET @indirect2 := LAST_INSERT_ID();

-- Level 3
INSERT INTO users (email, created_at, referred_by, status) VALUES ('third1@example.com', '2024-10-01', @indirect1, 'active');
SET @third1 := LAST_INSERT_ID();

-- Investment plans (update plan_id if needed)
INSERT INTO investments (user_id, plan_id, invested_amount, join_date, status) VALUES (@direct1, 1, 5000, '2024-06-02', 'active');
INSERT INTO investments (user_id, plan_id, invested_amount, join_date, status) VALUES (@direct1, 2, 3000, '2024-12-15', 'active');
INSERT INTO investments (user_id, plan_id, invested_amount, join_date, status) VALUES (@direct2, 2, 7000, '2024-07-16', 'active');
INSERT INTO investments (user_id, plan_id, invested_amount, join_date, status) VALUES (@indirect1, 3, 2500, '2024-08-02', 'active');
INSERT INTO investments (user_id, plan_id, invested_amount, join_date, status) VALUES (@indirect2, 1, 4000, '2024-09-11', 'active');
INSERT INTO investments (user_id, plan_id, invested_amount, join_date, status) VALUES (@indirect2, 3, 2000, '2025-01-05', 'active');
INSERT INTO investments (user_id, plan_id, invested_amount, join_date, status) VALUES (@third1, 2, 1500, '2024-10-02', 'active');
