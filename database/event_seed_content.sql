INSERT INTO `events` (
  `title`, `slug`, `description`, `full_description`, `cover_image`, `event_date`, `event_time`, `location`, `event_type`, `status`, `registration_link`
) VALUES
('Project Showcase BitFest 2025', 'project-showcase-bitfest-2025', 'KUET CSE BitFest 2025 Project Showcase Competition invited university and college teams to present impactful hardware, software, and AI-supported solutions.', 'KUET CSE BitFest 2025 | Project Showcase Competition is Here!
Organized by the Department of Computer Science and Engineering, KUET, BitFest 2025 presents an exciting Project Showcase Competition designed to inspire tech enthusiasts through problem-solving, collaboration, and skill development.

Event Details
Competition Date: January 03, 2025
Competition Venue: New Academic Building, Block-B, KUET
Registration Deadline: December 26, 2024
Note: Dates are subject to change based on the decision of the organizing committee.

Project Showcase Competition Details
In this project showcase, participants are encouraged to present innovative solutions that combine hardware, software, or a blend of both. Projects leveraging AI are highly appreciated, and initiatives aligning with the United Nations Sustainable Development Goal 17 (SDG 17) are particularly encouraged. The focus is on delivering impactful, creative solutions that demonstrate technical expertise and a commitment to driving meaningful change.

Participation Guidelines
- Participants must be enrolled in a university or college.
- Teams can have 1-3 participants. Participants from different universities or colleges are allowed.
- Each participant can only register in one team.
- Both hardware and software, or a combination of both, are allowed.

Registration & Fees
- Registration Fee:
- BDT 1500 for a team participation with 3 members.
- BDT 1000 for a team participation with 2 members.
- BDT 500 for individual participation.
- Bkash send money: 01305638483

Registration Link: https://forms.gle/jwYaY6sLet97ENj6A
Rulebook Link: https://tinyurl.com/bitFestProjectRulebook
Exciting prizes await. Details on rewards will be announced soon.
For queries or further details, reach us at the BitFest Facebook page.

Prepare to build, innovate, and compete in this one-of-a-kind competition. See you at BitFest 2025!', 'Project Showcase Bitfest2025.jpg', '2025-01-03', NULL, 'New Academic Building, Block-B, KUET', 'competition', 'completed', 'https://forms.gle/jwYaY6sLet97ENj6A'),
('HACKERS Ascent 2024', 'hackers-ascent-2024', 'HACKERS Ascent 2024 welcomed students into practical hardware design with mentor-led learning, discussion, and hands-on guidance.', 'If you are excited by the world of hardware and eager to unlock its vast potential, then the Hardware Acceleration Club of KUET (HACK) is the place for you. We are thrilled to announce our first workshop of 2024, and it promises to be a fantastic event. Whether you are a student looking to expand your knowledge or an enthusiast wanting to deepen your expertise, our team of experienced mentors will guide you through the essentials of hardware design and demonstrate how to create and implement efficient systems.

During the HACK workshop, you will learn to harness the power of hardware to solve complex challenges and improve performance. You will get hands-on experience with the latest tools and technologies as our mentors take you step-by-step from the fundamentals to more advanced concepts in hardware design. You will also have plenty of chances to ask questions, receive feedback, and collaborate with other participants.

Do not miss this exciting opportunity to join the HACK community and elevate your hardware acceleration skills. Sign up for our first 2024 workshop and get ready to unlock your full potential.

Class time: 10:30 am
Venue: Academic Building B Block, Room No: 101, 102

Please complete this form to confirm your attendance:
https://forms.gle/FPemuosyLhQbqPkL6

For any query:
Abdullah Al Noman (CSE 2k21)
Contact No: 01306990354

Mustafizur Rahman (CSE 2k21)
Contact No: 01630893042

Amit Kairy (CSE 2k20)
Contact No: 01998684891', 'Hackers Ascent 2024.jpg', '2024-11-09', '10:30:00', 'Academic Building B Block, Room No: 101, 102', 'workshop', 'completed', 'https://forms.gle/FPemuosyLhQbqPkL6'),
('Workshop 2023 on Advanced Arduino and LFR', 'workshop-2023-advanced-arduino-lfr', 'A two-day workshop on advanced Arduino programming, Arduino modules, and introductory line-following robot development.', 'We are very excited to announce that Hardware Acceleration Club of KUET (HACK) is going to launch a workshop on Advanced Arduino, Arduino programming, and introduction to Line Following Robot.

Necessary Information for the Event
Time: 10.00 AM
Venue: New Academic Building Block B (101, 102, 105)

The schedule of the workshop:
DAY-1: Advanced Arduino Programming and Different Arduino Modules
DAY-2: Introduction to Line Following Robot and Advanced Line Following Programming

Registration now!
Registration form link: https://forms.gle/QdmDKqip7fntgfYE6

Registration Fee: 500 BDT
Payment Method:
1. Bkash (+8801307232605)
2. Rocket (+88013072326054)', 'Workshop 2023 on Advanced Adruino and LFR.jpg', '2023-03-10', '10:00:00', 'New Academic Building Block B (101, 102, 105)', 'workshop', 'completed', 'https://forms.gle/QdmDKqip7fntgfYE6'),
('Introductory Class for 2k21', 'introductory-class-for-2k21', 'An introductory HACK workshop for the 2k21 batch focused on hardware fundamentals, practical systems, and club community learning.', 'If you are fascinated by the world of hardware and eager to explore its limitless potential, then you will want to be a part of the Hardware Acceleration Club of KUET (HACK). We are excited to announce our first workshop of the year 2023, and it is shaping up to be a fantastic event. Whether you are a student looking to expand your knowledge or an enthusiast seeking to deepen your understanding, our team of experienced mentors will guide you through the fundamentals of hardware and demonstrate how to design and implement efficient hardware systems.

At the HACK workshop, you will learn how to leverage the power of hardware to tackle complex problems and boost performance. You will get hands-on experience with cutting-edge tools and technologies. Our mentors will take you step-by-step through the process of designing and implementing a hardware system, starting with the basics and gradually building up to more advanced concepts. Along the way, you will have plenty of opportunities to ask questions, get feedback, and collaborate with other participants.

So do not miss out on this exciting opportunity to join the HACK community and take your hardware acceleration skills to the next level. Sign up for our first workshop of the year 2023 today and get ready to unlock your full potential.

Class time: 9:30 am
Venue: Academic Building B Block, 102

For any query:
Md. Rifat Murshed Rahat (CSE, 2k19)
Contact No: 01633241000

Amit Kairy (CSE, 2k20)
Contact No: 01998684891', 'Introductory Class for 2k21.jpg', '2023-03-04', '09:30:00', 'Academic Building B Block, 102', 'workshop', 'completed', NULL)
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `description` = VALUES(`description`),
  `full_description` = VALUES(`full_description`),
  `cover_image` = VALUES(`cover_image`),
  `event_date` = VALUES(`event_date`),
  `event_time` = VALUES(`event_time`),
  `location` = VALUES(`location`),
  `event_type` = VALUES(`event_type`),
  `status` = VALUES(`status`),
  `registration_link` = VALUES(`registration_link`);
