INSERT INTO `club_activities` (
  `title`, `slug`, `activity_date`, `description`, `content`, `cover_image`
) VALUES
('Communication Protocols: UART and HTTP', 'communication-protocols-uart-and-http', '2026-04-17', 'Successfully completed the final workshop of Module-1 for the 2k23 batch.', 'Successfully completed the final workshop of Module-1 for the 2k23 batch.

The workshop was on communication protocols, more specifically on UART and HTTP.

It was inspiring to see everyone move from understanding basic logic to understanding how devices talk with each other. Great energy from everyone involved!', 'Communication Protocols UART and HTTP-1.jpg'),
('Workshop on HACK Club & Robotics', 'workshop-on-hack-club-robotics', '2025-11-08', 'A session was held for the 2k24 batch to introduce HACK Club and robotics.', 'A session was held for the 2k24 batch to introduce HACK Club and robotics.

Basic topics like microcontroller, Arduino, LED blinking, and Arduino IDE were taught with a hands-on demo.

Their performance was great. Not only were we able to show them the blinky, but they also managed to complete a small task given after.

It was the beginning of an exciting learning journey in embedded systems.', 'Workshop on HACK Club Robotics-1.jpg'),
('First Workshop on Line Following Robot (LFR)', 'first-workshop-on-line-following-robot-lfr', '2025-10-24', 'Amit Kairy, President of HACK, conducted an insightful session on LFR and PID tuning.', 'Conducted by Amit Kairy Bhai, President of HACK - Hardware Acceleration Club of KUET.

It was an amazing and insightful session on PID tuning.

Excited for what is coming next.', 'LFR-workshop-1.jpg'),
('Hands-on Session: MPU6050 with Arduino and Python', 'hands-on-session-mpu6050-with-arduino-and-python', '2025-10-11', 'Participants learned how to connect the MPU6050 sensor with Arduino and read the data using Python.', 'The session on MPU6050 Sensor and Python was successfully conducted by HACK - Hardware Acceleration Club of KUET.

Participants learned how to connect the sensor with Arduino and use Python to serial read and display the data.

Thanks to everyone who joined and made the session successful.', 'MPU6050 with Arduino and Python-1.jpg'),
('Session on Servo Motor and LDR', 'session-on-servo-motor-and-ldr', '2025-09-24', 'A workshop for the 2k23 batch on Servo Motor, LDR sensor, Arduino interfacing, and a laser security system project.', 'HACK - Hardware Acceleration Club of KUET successfully conducted a workshop for the 2k23 batch on Servo Motor and LDR (Light Dependent Resistor) Sensor.

Topics Covered:
- Basics of Servo Motor and LDR
- Interfacing with Arduino
- Working principle
- Hands-on project: Security System using Laser Light and LDR

A heartfelt thanks to everyone who joined the session. We are truly glad to have such impressive and dedicated juniors. Your enthusiasm made it a success!', 'Session on Servo Motor and LDR-1.jpg'),
('Basic Arduino, Sensor Interfacing, and Serial Communication', 'basic-arduino-sensor-interfacing-and-serial-communication', '2025-08-28', 'A lively beginner workshop on Arduino basics, sensor interfacing, and serial communication.', 'We just wrapped up our HACK - Hardware Acceleration Club of KUET workshop on Basic Arduino, sensor interfacing, and serial communication, and it was an absolute blast.

Big shoutout to everyone who joined in, especially our awesome 2k23 juniors. Your energy, questions, and teamwork made the session lively and super engaging.

A special thanks to Amit Kairy Bhai and Faysal Bhai from Batch 2k20 for joining us and sharing their insights. It meant a lot.

We will be back with more sessions soon, so stay tuned. Until then, keep learning and keep building.', 'Basic Arduino, sensor interfacing, and serial communication-1.jpg'),
('Workshop on Servo Motor', 'workshop-on-servo-motor', '2023-12-05', 'HACK conducted another workshop for 2k22 on servo motors and a smart dustbin project.', 'HACK conducted another workshop for 2k22.

The workshop was about servo motor, its working mechanism, and finally building a project with servo and sonar: Smart Dustbin.

Here are some glimpses of the moments.', 'Workshop on Servo motor-1.jpg'),
('Spark Fusion 2k21', 'spark-fusion-2k21', '2023-09-20', 'An event that brought together organizers, mentors, judges, and the 2k21 batch for project ideas and innovation.', 'An electrifying convergence of talent and wisdom.

The Hardware Acceleration Club of KUET (HACK) orchestrated a remarkable event, bringing together the past, present, and future of innovation.

The 2k21 batch showcased their brilliant ideas and projects, reflecting the promising future of technology.

The 2k20 mentors provided invaluable guidance, sharing their knowledge and experience.

The 2k19 organizers orchestrated a seamless event, ensuring everything ran like clockwork.

The 2k18 judge panel critically assessed and recognized the best, setting the standard for excellence.

Together, we spark innovation, empower future tech leaders, and drive progress.', 'Spark Fusion-1.jpg')
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `activity_date` = VALUES(`activity_date`),
  `description` = VALUES(`description`),
  `content` = VALUES(`content`),
  `cover_image` = VALUES(`cover_image`);
