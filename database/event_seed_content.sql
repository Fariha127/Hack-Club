INSERT INTO `events` (
  `title`, `slug`, `description`, `cover_image`, `event_date`, `event_time`, `location`, `event_type`, `status`
) VALUES
('Project Showcase BitFest 2025', 'project-showcase-bitfest-2025', 'KUET CSE BitFest 2025 Project Showcase Competition invited university and college teams to present impactful hardware, software, and AI-supported solutions.', 'Project Showcase Bitfest2025.jpg', '2025-01-03', NULL, 'New Academic Building, Block-B, KUET', 'competition', 'completed'),
('HACKERS Ascent 2024', 'hackers-ascent-2024', 'HACKERS Ascent 2024 welcomed students into practical hardware design with mentor-led learning, discussion, and hands-on guidance.', 'Hackers Ascent 2024.jpg', '2024-11-09', '10:30:00', 'Academic Building B Block, Room No: 101, 102', 'workshop', 'completed'),
('Workshop 2023 on Advanced Arduino and LFR', 'workshop-2023-advanced-arduino-lfr', 'A two-day workshop on advanced Arduino programming, Arduino modules, and introductory line-following robot development.', 'Workshop 2023 on Advanced Adruino and LFR.jpg', '2023-03-10', '10:00:00', 'New Academic Building Block B (101, 102, 105)', 'workshop', 'completed'),
('Introductory Class for 2k21', 'introductory-class-for-2k21', 'An introductory HACK workshop for the 2k21 batch focused on hardware fundamentals, practical systems, and club community learning.', 'Introductory Class for 2k21.jpg', '2023-03-04', '09:30:00', 'Academic Building B Block, 102', 'workshop', 'completed')
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `description` = VALUES(`description`),
  `cover_image` = VALUES(`cover_image`),
  `event_date` = VALUES(`event_date`),
  `event_time` = VALUES(`event_time`),
  `location` = VALUES(`location`),
  `event_type` = VALUES(`event_type`),
  `status` = VALUES(`status`);
