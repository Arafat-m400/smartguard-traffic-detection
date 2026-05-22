from fastapi import FastAPI, File, UploadFile
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
import cv2
import numpy as np
from ultralytics import YOLO
import pyttsx3
from datetime import datetime
from collections import defaultdict
from zoneinfo import ZoneInfo
from fastapi import Query
import csv
import os
import threading

app = FastAPI()

# Allow frontend access
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# Load the YOLO model
model = YOLO("best.pt")

# Define your custom messages
class_warnings = {
    "Green Light": "Green light detected. Be ready to go.",
    "Red Light": "Red light detected. Stop.",
    "Stop": "Stop sign detected. Be ready to stop.",
    "Speed Limit 10": "Speed limit 10 detected. Reduce your speed to 10 kilometers per hour.",
    "Speed Limit 20": "Speed limit 20 detected. Reduce your speed to 20 kilometers per hour.",
    "Speed Limit 30": "Speed limit 30 detected. Reduce your speed to 30 kilometers per hour.",
    "Speed Limit 40": "Speed limit 40 detected. Reduce your speed to 40 kilometers per hour.",
    "Speed Limit 50": "Speed limit 50 detected. Reduce your speed to 50 kilometers per hour.",
    "Speed Limit 60": "Speed limit 60 detected. Reduce your speed to 60 kilometers per hour.",
    "Speed Limit 70": "Speed limit 70 detected. Reduce your speed to 70 kilometers per hour.",
    "Speed Limit 80": "Speed limit 80 detected. Reduce your speed to 80 kilometers per hour.",
    "Speed Limit 90": "Speed limit 90 detected. Reduce your speed to 90 kilometers per hour.",
    "Speed Limit 100": "Speed limit 100 detected. Reduce your speed to 100 kilometers per hour.",
    "Speed Limit 110": "Speed limit 110 detected. Reduce your speed to 110 kilometers per hour.",
    "Speed Limit 120": "Speed limit 120 detected. Reduce your speed to 120 kilometers per hour."
}

# Analytics tracking variables
sign_counts = defaultdict(int)
sign_last_seen = {}
hourly_distribution = [0] * 24
location_data = []

# -----------------------------
# Detection Endpoint
# -----------------------------
@app.post("/detect/")
async def detect(file: UploadFile = File(...), latitude: float = -2.6068, longitude: float = 29.7354):
    content = await file.read()
    npimg = np.frombuffer(content, np.uint8)
    frame = cv2.imdecode(npimg, cv2.IMREAD_COLOR)
    results = model(frame)[0]

    class_names = results.names
    detections = []
    now = datetime.now(ZoneInfo("Africa/Kigali"))

    for box in results.boxes:
        class_id = int(box.cls)
        class_name = class_names[class_id]
        xyxy = box.xyxy[0].cpu().numpy().tolist()
        confidence = box.conf.item()
        
        # Update analytics
        sign_counts[class_name] += 1
        sign_last_seen[class_name] = now.isoformat()
        hourly_distribution[now.hour] += 1
        location_data.append([latitude, longitude, confidence])

        # Brick 2 - Offline SD Card Logging (CSV)
        log_file = "detection_log.csv"
        file_exists = os.path.isfile(log_file)
        with open(log_file, mode='a', newline='') as f:
            writer = csv.writer(f)
            if not file_exists:
                writer.writerow(["Timestamp", "Sign", "Confidence", "Latitude", "Longitude"])
            writer.writerow([
                now.strftime("%Y-%m-%d %H:%M:%S"),
                class_name,
                round(confidence, 2),
                latitude,
                longitude
            ])

        detections.append({
            "class_name": class_name,
            "bbox": xyxy,
            "confidence": confidence
        })

    detected_classes = list(set([d['class_name'] for d in detections]))
    warnings = [class_warnings.get(c, c) for c in detected_classes]

    # Speak warning in separate thread
    if warnings:
        def speak(text):
            try:
                speaker = pyttsx3.init()
                speaker.setProperty('rate', 150)
                speaker.say(text)
                speaker.runAndWait()
                speaker.stop()
            except Exception:
                pass
        threading.Thread(target=speak, args=(warnings[0],), daemon=True).start()

    return {"detections": detections, "warnings": warnings}

# -----------------------------
# Real-Time Analytics Endpoint
# -----------------------------
@app.get("/analytics")
async def get_analytics(reset: bool = Query(False)):
    global sign_counts, sign_last_seen, hourly_distribution, location_data
    
    if reset:
        sign_counts.clear()
        sign_last_seen.clear()
        hourly_distribution[:] = [0] * 24
        location_data.clear()
        print("Analytics data has been reset!")

    total = sum(sign_counts.values())
    stats = []
    for sign, count in sign_counts.items():
        percentage = round((count / total) * 100, 1) if total else 0
        stats.append({
            "sign": sign,
            "count": count,
            "percentage": percentage,
            "lastDetected": sign_last_seen.get(sign, "-")
        })

    rare = [{"sign": k, "count": v} for k, v in sign_counts.items() if v <= 3]

    return JSONResponse(content={
        "signFrequency": [{"sign": k, "count": v} for k, v in sign_counts.items()],
        "locations": location_data[-100:],
        "timeDistribution": {
            "hours": list(range(24)),
            "counts": hourly_distribution
        },
        "signStats": stats,
        "rareSigns": rare
    })