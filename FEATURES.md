# Luminus LMS — Complete Platform Features

## What is this platform?

Luminus is a **Learning Management System (LMS)** — a website where schools or training centers can manage courses, students, teachers, grades, and everything related to education. It was built for creative programs like **Film Production, Digital Media, Game Design, and Audio Engineering**.

---

## 1. User Management (Who Can Use It?)

The platform has **3 types of users**:

| Role | What they do |
|------|-------------|
| **Student** | Enrolls in courses, watches lessons, submits homework, takes quizzes, sees grades |
| **Instructor** | Creates courses, teaches, grades assignments, takes attendance |
| **Admin** | Manages everything — users, courses, programs, reports |

**Key features:**
- **Login** with email/password or **Google account**
- **Registration** — students and instructors sign up separately
- **Forgot password** — reset via email
- **Instructor verification** — admin must approve instructors before they can teach
- **User profiles** — upload photo, write bio, edit personal info
- **Bilingual** — supports English and Arabic (switchable)
- **Dark/Light mode** — users can change the theme

---

## 2. Course Management (The Heart of the Platform)

### Creating Courses

Instructors and admins can create courses with:
- Title, description, cover image
- Program (Film, Digital Media, etc.)
- Course type (program course, core course, university course)
- Publish/unpublish (hide course from students while building it)
- **Duplicate a course** — copies everything (modules, lessons, quizzes, etc.)

### Course Structure

Each course is organized like this:

```
Course
  └── Module (a chapter/section)
       └── Lesson (a class/topic)
            └── Topic (a file, link, video, audio, or text)
       └── Quiz
       └── Live Session (video call)
       └── Assignment (homework)
       └── Files (PDFs, documents)
```

### Course Catalog

- Students can **browse all published courses**
- **Search** by name, description, instructor
- **Filter** by program
- See how many students are enrolled

---

## 3. Lessons & Content

Lessons are where the actual teaching happens. Each lesson can contain:

| Content Type | What it is |
|-------------|-----------|
| **Rich Text** | Written content (like a blog post) |
| **Video URL** | YouTube/Vimeo link |
| **Video Upload** | Upload video file (MP4, WebM — up to 200MB) |
| **Audio URL** | SoundCloud/podcast link |
| **File URL** | Link to any file |

**Special features:**
- **Prerequisite locking** — students must finish Lesson 1 before accessing Lesson 2
- **Mark as complete** — students track their progress
- **Ordering** — lessons appear in a specific sequence

---

## 4. Assignments & Homework

### For Instructors:
- Create assignments with title, description, **due date**, **max score**
- Attach reference files
- Link to a **rubric** (grading checklist)
- Organize by module

### For Students:
- Submit homework by uploading files or providing links (video/audio/file)
- Add notes to submission
- **Can't submit after deadline** (enforced by the system)
- Can update submission before due date

### Grading:
- Instructor grades with score + written feedback
- Can **directly grade** even if student didn't submit
- **Release grades** — sends email + notification to student
- **Gradebook view** — see all students' grades for an assignment

---

## 5. Quizzes & Assessments

### Creating Quizzes:
- Title, description, time limit, max attempts
- **Question types:** Multiple choice, True/False, Short answer, Long answer
- **Randomize questions** — different order for each student
- **Grading method** for multiple attempts: Best score, Worst, First, Last, or Average

### Taking Quizzes:
- Timer counts down
- **Auto-graded** for multiple choice and True/False
- Short/Long answers graded manually by instructor
- Students can see results (if instructor allows)

### Question Banks:
- Create **reusable question banks** (like a question library)
- Import questions from **CSV files**
- Pull random questions from banks into quizzes
- Share banks across multiple courses

---

## 6. Grading System

### Grade Rules (Weighted Grading):
Each course can set weights:
- Quizzes = 40%
- Assignments = 30%
- Attendance = 30%

### Rubrics:
- Create detailed grading rubrics with criteria and levels
- **Import rubrics from Moodle XML format**
- Use rubric while grading (click-by-click scoring)

### Grade Management:
- View grades per student, per assignment, per course
- Export grades to **CSV** (Excel-compatible)
- Import grades from CSV
- Admin can manage all grades platform-wide

---

## 7. Attendance

### Recording:
- Mark students as **Present, Absent, Late, or Excused**
- **Bulk record** — mark all students at once
- Monthly calendar view

### Reports & Warnings:
- **Attendance rate** calculated per student
- **Auto-generated warnings:**
  - Level 1: Missing 20%+ of classes
  - Level 2: Missing 35%+ of classes
- Export attendance reports to CSV

---

## 8. Communication Features

### Announcements:
- Instructors/Admins post announcements (with priority: low/normal/high/urgent)
- Students can **dismiss** announcements they've read
- Appear on the dashboard

### Discussion Forums:
- Each course has a discussion board
- Students create topics, others reply
- Instructors can **pin** important topics
- Instructors can **lock** topics (no more replies)
- Everyone gets notified of new posts/replies

### Notifications:
- In-app notification bell with unread count
- Types: enrollment, assignment created, quiz graded, submission received, grade released, new discussion, live session
- Mark as read individually or all at once

### Email Notifications:
- Grade released → email sent to student
- Quiz grade released → email sent to student

---

## 9. Live Sessions (Video Calls)

### Built-in Video (LiveKit):
- Instructors create live sessions with scheduled time
- Students join directly in the browser
- **No external software needed**
- Supports recording

### External Providers:
- Whereby or custom video links
- Good for integration with existing tools

### Features:
- Upcoming/past sessions view
- Notifications when sessions are created
- Duration tracking

---

## 10. Plagiarism Detection

When a student submits homework, the system checks:

| Check | What it does |
|-------|-------------|
| **Text Fingerprinting** | Compares writing style across all submissions to find copied work |
| **AI Detection** | Detects if content was likely written by AI (ChatGPT, etc.) |

- Shows **similarity percentage** between submissions
- Shows **AI probability score**
- Badge indicator on submissions (green = human, yellow = mixed, red = AI)

---

## 11. Student Portfolio

Students can build a portfolio:
- Add projects with title, description, and media
- Upload videos, audio, images, or files
- Set items as **public** or **private**
- Other students/instructors can view public portfolios

---

## 12. Program Management

Admins manage **academic programs** (Film Production, Digital Media, Game Design, Audio Engineering):
- Create/edit/delete programs
- Assign courses to programs
- See student counts per program
- Remove students from programs

---

## 13. Reports & Analytics

### Admin Dashboard shows:
- Total users, courses, enrollments
- Average grades across platform
- Students by program (chart)
- Top 10 courses by enrollment
- Monthly signups and submissions (chart)
- Recent activity feed

### Export Reports (CSV):

| Report | What it contains |
|--------|-----------------|
| Users | All user data |
| Enrollments | Who's enrolled in what |
| Grades | All grades |
| Submissions | All homework submissions |
| Attendance | Attendance records |
| Announcements | All announcements |
| Quiz Attempts | Quiz results |
| Grade Rules | Grading weights |

### Import Data (CSV):
- Bulk import users, courses, grades, attendance, questions
- Each import has a **downloadable example** showing the correct format

---

## 14. External System Integration (SIS/CRM)

The LMS can connect to external school management systems:

### Single Sign-On (SSO):
- Students can log in from the school's main system
- Secure token-based authentication

### Data Sync:
- Push/pull user data between systems
- Push grades and attendance to external system
- Protected by API keys

---

## 15. User Interface Features

- **Sidebar navigation** — main menu
- **Top bar** — user info, notifications, settings
- **Course tabs** — navigate course sections easily
- **Mini calendar** — upcoming events widget
- **Student view mode** — instructors/admins can see the platform as a student sees it
- **Responsive design** — works on desktop and mobile

---

## 16. Security & Middleware

| Feature | Purpose |
|---------|---------|
| Role-based access | Students can't access instructor pages |
| Rate limiting | Blocks brute-force login attempts |
| Session management | Secure login sessions |
| CSRF protection | Prevents form tampering |
| API authentication | Secure external integrations |
| Prevent back-button | Can't access restricted pages via browser back button |

---

## Summary Statistics

| Component | Count |
|-----------|-------|
| Database tables | 54 |
| Pages/Views | 130+ |
| User roles | 3 |
| Features | 16 major categories |
| Export formats | 8 CSV types |
| Import formats | 6 CSV types |
| API endpoints | 10 |
| Supported languages | 2 (English, Arabic) |

---

## Conclusion

This platform covers **everything a school needs**: managing students, creating courses, teaching content, giving quizzes, grading homework, tracking attendance, detecting plagiarism, running video classes, generating reports, and integrating with external systems — all in one place.
