from fastapi import FastAPI, File, UploadFile
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
import cv2
import numpy as np
from ultralytics import YOLO
import pyttsx3
from datetime import datetime
from collections import defaultdict

app = FastAPI()

# Allow frontend access (adjust allow_origins for production)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# Load the YOLO model
model = YOLO("best.pt")

# Optional: TTS engine
engine = pyttsx3.init()
engine.setProperty('rate', 150)

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

# -----------------------------
# Analytics tracking variables
# -----------------------------
sign_counts = defaultdict(int)
sign_last_seen = {}
hourly_distribution = [0] * 24
location_data = []  # You can inject GPS later

# -----------------------------
# Detection Endpoint
# -----------------------------
@app.post("/detect/")
async def detect(file: UploadFile = File(...)):
    content = await file.read()
    npimg = np.frombuffer(content, np.uint8)
    frame = cv2.imdecode(npimg, cv2.IMREAD_COLOR)
    results = model(frame)[0]

    class_names = results.names
    detections = []
    now = datetime.utcnow()

    for box in results.boxes:
        class_id = int(box.cls)
        class_name = class_names[class_id]

        xyxy = box.xyxy[0].cpu().numpy().tolist()
        confidence = float(box.conf.cpu().numpy())

        # Update analytics
        sign_counts[class_name] += 1
        sign_last_seen[class_name] = now.isoformat()
        hourly_distribution[now.hour] += 1

        # Fake location (you can replace with GPS)
        location_data.append([-2.6068, 29.7354, 0.5])

        detections.append({
            "class_name": class_name,
            "bbox": xyxy,
            "confidence": confidence
        })

    detected_classes = list(set([d['class_name'] for d in detections]))
    warnings = [class_warnings.get(c, c) for c in detected_classes]

    return {"detections": detections, "warnings": warnings}

# -----------------------------
# Real-Time Analytics Endpoint
# -----------------------------
@app.get("/analytics")
async def get_analytics():
    total = sum(sign_counts.values())

    # Sign statistics
    stats = []
    for sign, count in sign_counts.items():
        percentage = round((count / total) * 100, 1) if total else 0
        stats.append({
            "sign": sign,
            "count": count,
            "percentage": percentage,
            "lastDetected": sign_last_seen.get(sign, "-")
        })

    # Rare signs
    rare = [{"sign": k, "count": v} for k, v in sign_counts.items() if v <= 3]

    return JSONResponse(content={
        "signFrequency": [{"sign": k, "count": v} for k, v in sign_counts.items()],
        "locations": location_data[-100:],  # Limit to latest 100
        "timeDistribution": {
            "hours": list(range(24)),
            "counts": hourly_distribution
        },
        "signStats": stats,
        "rareSigns": rare
    })