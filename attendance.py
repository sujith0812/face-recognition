import sys
import os
import cv2
import numpy as np
from datetime import datetime
from skimage.metrics import structural_similarity as ssim
import mysql.connector
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart


# Command line arguments

if len(sys.argv) != 4:
    print("Usage: python attendance.py <class> <section> <topic>")
    sys.exit(1)

class_name = sys.argv[1]
section    = sys.argv[2]
topic      = sys.argv[3]


# Database connection

db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="class_attendance_system"
)
cursor = db.cursor()


# Loading students from DB

cursor.execute("SELECT id, name, photo, email FROM students WHERE class=%s AND section=%s", (class_name, section))
students = cursor.fetchall()
print(f"Loaded {len(students)} students for {class_name}-{section}")


# Email configuration

sender_email = "gsujith116@gmail.com"
sender_password = "mlao makx cygq cpjh"  # google account loki poyi , security tab lo , app password ani untadhi...akada create cheyali
smtp_server = "smtp.gmail.com"
smtp_port = 587

def send_absent_email(student_email, student_name, topic):
    subject = f"Attendance Notification: {topic}"
    body = f"Dear {student_name},\n\nYou were absent in today’s class , Topic :  ({topic}). Please take necessary action.\n\nRegards,\nFaculty"
    msg = MIMEMultipart()
    msg['From'] = sender_email
    msg['To'] = student_email
    msg['Subject'] = subject
    msg.attach(MIMEText(body, 'plain'))

    try:
        server = smtplib.SMTP(smtp_server, smtp_port)
        server.starttls()
        server.login(sender_email, sender_password)
        server.send_message(msg)
        server.quit()
        print(f"Email sent to {student_name} ({student_email})")
    except Exception as e:
        print(f"Failed to send email to {student_name}: {e}")


# Loading Haar Cascade algo...

face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + "haarcascade_frontalface_default.xml")
if face_cascade.empty():
    print("Failed to load Haar cascade. Check OpenCV installation.")
    sys.exit(1)


# Prepare data structures

present_ids = set()

def compare_faces(img1, img2):
    img1 = cv2.resize(img1, (100, 100))
    img2 = cv2.resize(img2, (100, 100))
    gray1 = cv2.cvtColor(img1, cv2.COLOR_BGR2GRAY)
    gray2 = cv2.cvtColor(img2, cv2.COLOR_BGR2GRAY)
    score, _ = ssim(gray1, gray2, full=True)
    return score


# Start camera(open CV )

cap = cv2.VideoCapture(0)
print("Press 'q' to stop attendance marking...")

while True:
    ret, frame = cap.read()
    if not ret:
        print("Failed to capture frame from camera.")
        break

    gray_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
    faces = face_cascade.detectMultiScale(gray_frame, scaleFactor=1.1, minNeighbors=5, minSize=(60, 60))

    for (x, y, w, h) in faces:
        live_face = frame[y:y+h, x:x+w]

        rect_color = (255, 255, 255)
        label_text = ""

        for sid, name, photo, email in students:
            photo_path = os.path.join("uploads/students", photo)
            if os.path.exists(photo_path):
                stored_img = cv2.imread(photo_path)
                score = compare_faces(live_face, stored_img)
                if score > 0.5:
                    present_ids.add(sid)
                    rect_color = (0, 255, 0)
                    label_text = f"{name} (Present)"
                    break

        cv2.rectangle(frame, (x, y), (x+w, y+h), rect_color, 2)
        if label_text:
            cv2.putText(frame, label_text, (x, y-10),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.8, rect_color, 2)

    cv2.imshow("Attendance", frame)
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()


# Save attendance to DB

now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
for sid, name, photo, email in students:
    status = "present" if sid in present_ids else "absent"
    cursor.execute(
        "INSERT INTO attendance (student_id, class, section, topic, status, marked_at) VALUES (%s,%s,%s,%s,%s,%s)",
        (sid, class_name, section, topic, status, now)
    )
db.commit()


# Sending emails to absent students

for sid, name, photo, email in students:
    if sid not in present_ids:
        send_absent_email(email, name, topic)

cursor.close()
db.close()

print(f"✅ Attendance saved. Present: {len(present_ids)}/{len(students)}")
