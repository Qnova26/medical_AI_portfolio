from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from dotenv import load_dotenv
import cv2
import numpy as np
import os, json, re, base64, time, io, traceback
import pydicom
import fitz  # PyMuPDF -> untuk convert PDF hasil lab jadi gambar
from openai import OpenAI
from PIL import Image

load_dotenv(override=True)

# Gunakan model "chat gpt 5.5" sesuai permintaan, melalui proxy Aivene
client = OpenAI(
    api_key=os.getenv("OPENAI_API_KEY"),
    base_url=os.getenv("OPENAI_BASE_URL", "https://api.aivene.com/v1")
)

app = FastAPI()
app.add_middleware(CORSMiddleware, allow_origins=["*"], allow_methods=["*"], allow_headers=["*"])

# ── Pydantic Models (Disesuaikan dengan permintaan Laravel) ────────
class LabFileInput(BaseModel):
    filename: str
    mime_type: str | None = None
    data: str  # base64 encoded

class ClinicalRequest(BaseModel):
    system_prompt: str
    user_prompt: str
    patient_name: str
    template_level: str
    body_temperature: str | None = None
    heart_rate: str | None = None
    respiratory_rate: str | None = None
    blood_pressure: str | None = None
    medical_history: str | None = None
    allergies: str | None = None
    symptoms: str | None = None
    doctor_notes: str | None = None
    other_info: str | None = None
    lab_files: list[LabFileInput] | None = None

class ImageRequest(BaseModel):
    system_prompt: str
    user_prompt: str | None = None
    image_base64: str
    image_type: str
    body_part: str | None = None
    doctor_notes: str | None = None
    template_level: str | None = None
    confidence_threshold: float = 0.25

class CombinedRequest(BaseModel):
    # Output AI Klinis
    clinical_diagnosis: str | None = None
    clinical_risk_level: str | None = None
    clinical_confidence: float | None = None
    clinical_summary: str | None = None
    clinical_differential: str | None = None
    clinical_recommendation: str | None = None
    clinical_medication: str | None = None
    clinical_doctor_diagnosis: str | None = None
    clinical_validation_status: str | None = None

    # Data mentah klinis (input asli sebelum diringkas AI)
    clinical_template_level: str | None = None
    clinical_body_temperature: str | int | float | None = None
    clinical_heart_rate: str | int | float | None = None
    clinical_respiratory_rate: str | int | float | None = None
    clinical_blood_pressure: str | int | float | None = None
    clinical_medical_history: str | None = None
    clinical_allergies: str | None = None
    clinical_symptoms: str | None = None
    clinical_doctor_notes: str | None = None
    clinical_other_info: str | None = None

    # Output AI Citra
    image_diagnosis: str | None = None
    image_risk_level: str | None = None
    image_confidence: float | None = None
    image_summary: str | None = None
    image_recommendation: str | None = None
    image_medication: str | None = None
    image_doctor_diagnosis: str | None = None
    image_validation_status: str | None = None

    # Data mentah citra
    image_type: str | None = None
    body_part: str | None = None
    image_doctor_notes: str | None = None
    image_template_level: str | None = None

class ChatMessage(BaseModel):
    role: str
    message: str

class ChatRequest(BaseModel):
    context: dict
    history: list[ChatMessage] = []
    question: str

# ── Helper: Ekstrak JSON dari respons LLM ─────────────────────────
def extract_json(text: str) -> dict:
    try:
        return json.loads(text)
    except json.JSONDecodeError:
        pass
    cleaned = re.sub(r"```(?:json)?", "", text).strip().rstrip("`").strip()
    try:
        return json.loads(cleaned)
    except json.JSONDecodeError:
        pass
    match = re.search(r"\{.*\}", text, re.DOTALL)
    if match:
        return json.loads(match.group())
    raise ValueError("Tidak bisa mengekstrak JSON dari respons model.")

# ── Helper: Konversi DICOM ke JPEG ────────────────────────────────
def convert_dicom_to_jpg(image_bytes: bytes) -> bytes:
    try:
        ds = pydicom.dcmread(io.BytesIO(image_bytes))
        pixel_array = ds.pixel_array
        img_min, img_max = pixel_array.min(), pixel_array.max()
        if img_max > img_min:
            normalized_img = ((pixel_array - img_min) / (img_max - img_min) * 255).astype(np.uint8)
        else:
            normalized_img = np.zeros_like(pixel_array, dtype=np.uint8)

        if len(normalized_img.shape) == 2:
            normalized_img = cv2.cvtColor(normalized_img, cv2.COLOR_GRAY2BGR)

        is_success, buffer = cv2.imencode(".jpg", normalized_img)
        if is_success:
            return buffer.tobytes()
        return image_bytes
    except Exception as e:
        print("DICOM Warning:", e)
        return image_bytes

# ── Helper: Convert PDF (hasil lab) jadi list gambar JPG per halaman ──
def convert_pdf_to_images(pdf_bytes: bytes, max_pages: int = 3) -> list[bytes]:
    images = []
    try:
        doc = fitz.open(stream=pdf_bytes, filetype="pdf")
        for i, page in enumerate(doc):
            if i >= max_pages:
                break
            pix = page.get_pixmap(dpi=150)
            images.append(pix.tobytes("jpg"))
        doc.close()
    except Exception as e:
        print("PDF Lab Warning:", e)
    return images

# ── Helper: Denormalisasi Bounding Box & Kembalikan Base64 ────────────
def draw_normalized_boxes_base64(image_bytes: bytes, findings: list) -> str:
    nparr = np.frombuffer(image_bytes, np.uint8)
    img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
    if img is None:
        return None

    h, w, _ = img.shape
    colors = {'high': (0, 0, 255), 'medium': (0, 165, 255), 'low': (0, 255, 0)}

    for item in findings:
        bbox_str = item.get("bounding_box")
        if not bbox_str: continue
        try:
            clean_bbox = str(bbox_str).strip("[]")
            coords = [float(x.strip()) for x in clean_bbox.split(',')]
            if len(coords) == 4:
                x_min, y_min, x_max, y_max = coords
                if max(x_min, y_min, x_max, y_max) <= 1.0:
                    x1, y1 = int(x_min * w), int(y_min * h)
                    x2, y2 = int(x_max * w), int(y_max * h)
                else:
                    x1, y1, x2, y2 = int(x_min), int(y_min), int(x_max), int(y_max)

                label = item.get("label", "Deteksi")
                color = colors['high']
                
                font_scale = max(0.4, min(w, h) / 900.0)
                thickness = max(1, int(font_scale * 1.5))
                box_thickness = max(2, int(min(w, h) / 400.0))
                padding = int(5 * font_scale) + 3
                
                cv2.rectangle(img, (x1, y1), (x2, y2), color, box_thickness)
                (text_w, text_h), _ = cv2.getTextSize(label, cv2.FONT_HERSHEY_SIMPLEX, font_scale, thickness)
                
                # Prevent text from getting cut off at the top
                if y1 - text_h - (padding * 2) >= 0:
                    rect_y1 = y1 - text_h - (padding * 2)
                    rect_y2 = y1
                    text_y = y1 - padding
                else:
                    rect_y1 = y1
                    rect_y2 = y1 + text_h + (padding * 2)
                    text_y = y1 + text_h + padding
                    
                cv2.rectangle(img, (x1, rect_y1), (x1 + text_w + (padding * 2), rect_y2), color, -1)
                cv2.putText(img, label, (x1 + padding, text_y), cv2.FONT_HERSHEY_SIMPLEX, font_scale, (255, 255, 255), thickness)
        except Exception as e:
            import traceback
            with open("bbox_error.log", "a") as f:
                f.write("BBox Error:\n")
                traceback.print_exc(file=f)
            print("BBox Error:", e)
            continue

    is_success, buffer = cv2.imencode(".jpg", img)
    if is_success:
        return base64.b64encode(buffer.tobytes()).decode('utf-8')
    return None

# ── Instruksi bersama: kapan butuh korelasi lintas-modalitas ──────
CORRELATION_GUIDANCE = """
Contoh kondisi yang BIASANYA CUKUP JELAS berdiri sendiri (tidak wajib korelasi tambahan):
- Fraktur tulang yang tampak nyata dan jelas garis patahannya
- Ligamen robek yang jelas terlihat pada citra (mis. ACL/MCL robek total)
- Batu empedu (kolelitiasis) yang tampak jelas sebagai struktur hiperekoik dengan acoustic shadowing
- Massa/lesi dengan morfologi yang sangat khas dan tidak ambigu

Contoh kondisi yang BIASANYA butuh korelasi tambahan karena ambigu/nonspesifik:
- Opasitas paru yang bisa berarti pneumonia, edema paru, atau TB
- Massa dengan batas tidak jelas atau karakteristik campuran
- Gejala sistemik (demam, nyeri) tanpa temuan pencitraan definitif
- Kelainan laboratorium/tanda vital abnormal tanpa penyebab yang jelas dari riwayat/gejala saja
"""

# ── Endpoint root ──────────────────────────────────────────────────
@app.get("/")
def root():
    return {"status": "FastAPI Medical AI (Multimodal GPT 5.5 + OpenCV)"}

# ── Endpoint: Analisis Klinis ──────────────────────────────────────
@app.post("/analyze/clinical")
async def analyze_clinical(data: ClinicalRequest):
    system_message = data.system_prompt
    system_message += (
        "\n\nWAJIB tambahkan juga field 'needs_imaging_correlation' (boolean) dan "
        "'imaging_correlation_reason' (string singkat, dalam Bahasa Indonesia). "
        "Set true HANYA jika data klinis (gejala, tanda vital, riwayat) menunjukkan "
        "indikasi yang penyebabnya tidak bisa dipastikan tanpa citra medis (mis. X-Ray, "
        "CT Scan, MRI, USG, atau ECG) — misalnya nyeri dada yang bisa jantung atau paru, "
        "nyeri perut kanan atas yang bisa organ hati/empedu/usus. Set false jika data "
        "klinis sudah cukup untuk memberi kesimpulan tanpa perlu citra tambahan, atau "
        "jika tidak ada indikasi spesifik yang mengarah ke kebutuhan pencitraan."
        + CORRELATION_GUIDANCE
    )

    # ── Proses lampiran hasil lab (opsional) ──────────────────────
    has_lab_files = bool(data.lab_files)
    lab_images_content = []

    if has_lab_files:
        system_message += (
            "\n\nWAJIB tambahkan juga field 'lab_analysis' (string, dalam Bahasa Indonesia) "
            "berisi interpretasi Anda terhadap hasil laboratorium yang dilampirkan: sebutkan "
            "nilai-nilai yang abnormal, kemungkinan makna klinisnya, dan relevansinya dengan "
            "gejala/riwayat pasien di atas. Jika tulisan pada lampiran tidak dapat terbaca "
            "dengan jelas, sebutkan itu secara eksplisit di dalam 'lab_analysis'."
        )

        for lab_file in data.lab_files:
            try:
                file_bytes = base64.b64decode(lab_file.data)
                is_pdf = (lab_file.mime_type == "application/pdf") or lab_file.filename.lower().endswith(".pdf")

                if is_pdf:
                    for page_bytes in convert_pdf_to_images(file_bytes):
                        b64 = base64.b64encode(page_bytes).decode("utf-8")
                        lab_images_content.append({
                            "type": "image_url",
                            "image_url": {"url": f"data:image/jpeg;base64,{b64}"}
                        })
                else:
                    mime = lab_file.mime_type or "image/jpeg"
                    b64 = base64.b64encode(file_bytes).decode("utf-8")
                    lab_images_content.append({
                        "type": "image_url",
                        "image_url": {"url": f"data:{mime};base64,{b64}"}
                    })
            except Exception as e:
                print("Lab file processing warning:", lab_file.filename, e)
                continue

    lab_note = "\nTerdapat lampiran hasil laboratorium pasien, mohon dianalisis dan isi field 'lab_analysis'." if has_lab_files else ""
    lab_json_key = ", lab_analysis" if has_lab_files else ""

    user_message = f"""
{data.user_prompt}
---
INFORMASI PASIEN:
Nama: {data.patient_name}
Level: {data.template_level}
Tanda Vital: {data.body_temperature}, {data.heart_rate}, dll.
Gejala: {data.symptoms}
{lab_note}

Balas HANYA dengan JSON valid berisi: diagnosis, risk_level, confidence, confidence_reason, summary, differential, recommendation, treatment_information, medication, potential_complications, needs_imaging_correlation, imaging_correlation_reason{lab_json_key}
PENTING: Field 'risk_level' WAJIB diisi HANYA dengan salah satu dari: 'Rendah', 'Sedang', atau 'Tinggi'.
"""
    if "json" not in user_message.lower(): user_message += "\nHarus JSON."

    try:
        # Kalau ada lampiran lab, kirim sebagai pesan multimodal (teks + gambar).
        # Kalau tidak, tetap plain text seperti semula.
        if lab_images_content:
            user_content = [{"type": "text", "text": user_message}] + lab_images_content
        else:
            user_content = user_message

        response = client.chat.completions.create(
            model="gpt-5.5",
            messages=[
                {"role": "system", "content": system_message},
                {"role": "user", "content": user_content}
            ],
            response_format={"type": "json_object"}
        )
        result = extract_json(response.choices[0].message.content)
        return result
    except Exception as e:
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=str(e))


# ── Endpoint: Analisis Citra (Multimodal) ──────────────────────────
@app.post("/analyze/image")
async def analyze_image(data: ImageRequest):
    start = time.time()
    try:
        # Decode base64 image dari Laravel
        image_bytes = base64.b64decode(data.image_base64)

        was_dicom = False
        # Cek header DICOM. Jika ya, convert ke JPG bytes
        if image_bytes.startswith(b'\x00'*128 + b'DICM'):
            image_bytes = convert_dicom_to_jpg(image_bytes)
            was_dicom = True

        base64_image_for_llm = base64.b64encode(image_bytes).decode('utf-8')

        system_message = data.system_prompt
        if "json" not in system_message.lower(): system_message += "\nReturn JSON object with diagnosis, risk_level, confidence, confidence_reason, summary, differential, recommendation, medication, and Finding (array of objects with label and bounding_box [xmin, ymin, xmax, ymax] normalized 0-1)."
        system_message += "\n\nPENTING: Untuk field 'risk_level', Anda HANYA boleh mengembalikan salah satu dari nilai berikut: 'Rendah', 'Sedang', atau 'Tinggi'. Jangan gunakan nilai lain."
        system_message += (
            "\n\nWAJIB tambahkan juga field 'needs_clinical_correlation' (boolean) dan "
            "'clinical_correlation_reason' (string singkat, dalam Bahasa Indonesia). "
            "Set true HANYA jika temuan citra bersifat ambigu/nonspesifik dan diagnosis "
            "pasti memerlukan data klinis tambahan (gejala, hasil lab, riwayat) untuk "
            "dikonfirmasi. Set false jika temuan citra sudah cukup definitif berdiri "
            "sendiri tanpa perlu data klinis tambahan."
            + CORRELATION_GUIDANCE
        )

        messages = [
            {"role": "system", "content": system_message},
            {"role": "user", "content": [
                {"type": "text", "text": f"Instruksi detail:\n{data.user_prompt}\n\nAnalisis gambar medis ini. Jenis: {data.image_type}. Bagian: {data.body_part}. Catatan: {data.doctor_notes}. Harus format JSON. PENTING: risk_level WAJIB 'Rendah', 'Sedang', atau 'Tinggi'. Sertakan juga confidence_reason, needs_clinical_correlation dan clinical_correlation_reason."},
                {"type": "image_url", "image_url": {"url": f"data:image/jpeg;base64,{base64_image_for_llm}"}}
            ]}
        ]

        response = client.chat.completions.create(
            model="gpt-5.5",
            messages=messages, 
            response_format={"type": "json_object"}
        )

        result = extract_json(response.choices[0].message.content)

        if was_dicom:
            result["converted_image"] = base64_image_for_llm

        if "Finding" in result and isinstance(result["Finding"], list):
            annotated_b64 = draw_normalized_boxes_base64(image_bytes, result["Finding"])
            if annotated_b64:
                result["annotated_image"] = annotated_b64

        result["processing_time"] = round(time.time() - start, 2)
        return result

    except Exception as e:
        traceback.print_exc()
        print("ERROR IMAGE:", e)
        raise HTTPException(status_code=500, detail=str(e))

# ── Endpoint: Diagnosis Gabungan (Klinis + Citra) ──────────────────
@app.post("/analyze/combined")
async def analyze_combined(data: CombinedRequest):
    system_message = (
        "Anda adalah sistem pendukung keputusan klinis. Tugas Anda menggabungkan "
        "SATU episode pemeriksaan pasien yang sama, terdiri dari data klinis "
        "(input mentah + interpretasi AI) dan data citra medis (input mentah + "
        "interpretasi AI), menjadi satu diagnosis final gabungan.\n\n"
        "PENTING: Anda diberi DUA jenis data untuk masing-masing modalitas:\n"
        "1. Data MENTAH (input asli dari dokter/pasien) — ini sumber kebenaran utama.\n"
        "2. Interpretasi AI sebelumnya (diagnosis, summary, dsb) — ini HANYA referensi "
        "awal, BUKAN satu-satunya sumber. Jika interpretasi AI sebelumnya tampak "
        "melewatkan atau menyederhanakan sesuatu yang terlihat di data mentah, "
        "utamakan analisis Anda sendiri terhadap data mentah tersebut.\n\n"
        "ATURAN WAJIB:\n"
        "1. 'risk_level' final TIDAK BOLEH lebih rendah dari tingkat risiko tertinggi "
        "di antara risk_level klinis dan citra. Prinsip: risiko tertinggi menang.\n"
        "2. 'confidence' final harus mencerminkan ketidakpastian gabungan. Jangan "
        "buat confidence lebih tinggi dari confidence tertinggi di antara kedua "
        "input, kecuali ada korelasi kuat dan konsisten antara temuan klinis dan "
        "citra yang secara eksplisit menaikkan keyakinan diagnosis.\n"
        "3. 'recommendation' final harus MEWARISI rekomendasi paling mendesak dari "
        "kedua input (misal rujukan spesialis segera, pencitraan lanjutan), jangan "
        "menyederhanakan jadi rekomendasi generik seperti 'istirahat' jika salah "
        "satu input berisiko tinggi.\n"
        "4. Jika ada 'doctor_diagnosis' atau validation_status='corrected' pada "
        "salah satu input, perlakukan itu sebagai lebih otoritatif dibanding hasil "
        "AI mentahnya.\n"
        "5. Jangan berhalusinasi temuan yang tidak ada di data yang diberikan.\n"
        "6. Field 'correlation' HANYA menjawab: apakah temuan klinis dan citra "
        "SALING MENDUKUNG/KONSISTEN satu sama lain secara langsung ('Ya'), atau "
        "tidak cukup data untuk menyimpulkan konsistensi tersebut ('Tidak'). "
        "Field ini TIDAK sama dengan 'apakah dibutuhkan pemeriksaan lanjutan' — "
        "kebutuhan pemeriksaan lanjutan cukup dijelaskan di 'recommendation' saja, "
        "jangan sampai isi 'correlation' kontradiktif dengan 'recommendation'.\n\n"
        "Field 'risk_level' WAJIB salah satu dari: 'Rendah', 'Sedang', 'Tinggi'.\n"
        "Balas HANYA dengan JSON valid berisi: diagnosis, risk_level, confidence "
        "(integer 0-100), confidence_reason, summary, correlation ('Ya'/'Tidak' beserta alasan singkat "
        "di dalam summary), recommendation, treatment_information, medication, potential_complications."
    )

    user_message = f"""
=== DATA KLINIS ===

Data Mentah (Input Asli):
- Level Template: {data.clinical_template_level}
- Suhu Tubuh: {data.clinical_body_temperature}
- Nadi: {data.clinical_heart_rate}
- Laju Napas: {data.clinical_respiratory_rate}
- Tekanan Darah: {data.clinical_blood_pressure}
- Riwayat Penyakit: {data.clinical_medical_history}
- Alergi: {data.clinical_allergies}
- Gejala: {data.clinical_symptoms}
- Catatan Dokter (saat input): {data.clinical_doctor_notes}
- Info Tambahan: {data.clinical_other_info}

Interpretasi AI Sebelumnya (referensi, bukan satu-satunya sumber):
- Diagnosis (AI): {data.clinical_diagnosis}
- Risk Level (AI): {data.clinical_risk_level}
- Confidence (AI): {data.clinical_confidence}
- Ringkasan (AI): {data.clinical_summary}
- Diagnosis Banding (AI): {data.clinical_differential}
- Rekomendasi (AI): {data.clinical_recommendation}
- Rekomendasi Obat (AI): {data.clinical_medication}
- Koreksi Dokter: {data.clinical_doctor_diagnosis or '-'}
- Status Validasi: {data.clinical_validation_status or '-'}

=== DATA CITRA ===

Data Mentah (Input Asli):
- Jenis Citra: {data.image_type}
- Bagian Tubuh: {data.body_part}
- Level Template: {data.image_template_level}
- Catatan Dokter (saat input): {data.image_doctor_notes}

Interpretasi AI Sebelumnya (referensi, bukan satu-satunya sumber):
- Diagnosis (AI): {data.image_diagnosis}
- Risk Level (AI): {data.image_risk_level}
- Confidence (AI): {data.image_confidence}
- Ringkasan (AI): {data.image_summary}
- Rekomendasi (AI): {data.image_recommendation}
- Rekomendasi Obat (AI): {data.image_medication}
- Koreksi Dokter: {data.image_doctor_diagnosis or '-'}
- Status Validasi: {data.image_validation_status or '-'}

Balas HANYA dengan JSON valid sesuai format yang ditentukan di system prompt.
Pertimbangkan DATA MENTAH di atas sebagai sumber utama analisis Anda, bukan
hanya menyalin/menggabungkan kesimpulan AI sebelumnya.
"""

    try:
        response = client.chat.completions.create(
            model="gpt-5.5",
            messages=[
                {"role": "system", "content": system_message},
                {"role": "user", "content": user_message},
            ],
            response_format={"type": "json_object"},
        )
        result = extract_json(response.choices[0].message.content)
        return result
    except Exception as e:
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=str(e))

# ── Endpoint: Chat Interaktif ──────────────────────────────────────
@app.post("/chat/clinical")
async def chat_clinical(data: ChatRequest):
    system_prompt = "Anda adalah asisten AI medis yang profesional. Tugas Anda adalah menjawab pertanyaan dokter berdasarkan konteks hasil analisis klinis pasien berikut ini. Jawab dengan singkat, padat, dan jelas.\n\nKonteks Analisis:\n"
    system_prompt += json.dumps(data.context, indent=2)

    messages = [{"role": "system", "content": system_prompt}]
    for msg in data.history:
        # Pengecualian jika pesan dari user atau assistant
        if msg.role in ["user", "assistant"]:
            messages.append({"role": msg.role, "content": msg.message})
    messages.append({"role": "user", "content": data.question})

    try:
        response = client.chat.completions.create(
            model="gpt-5.5",
            messages=messages,
        )
        return {"answer": response.choices[0].message.content}
    except Exception as e:
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/chat/image")
async def chat_image(data: ChatRequest):
    system_prompt = "Anda adalah asisten AI radiologi yang profesional. Tugas Anda adalah menjawab pertanyaan dokter berdasarkan konteks hasil analisis citra medis berikut ini. Jawab dengan singkat, padat, dan jelas.\n\nKonteks Analisis Citra:\n"
    system_prompt += json.dumps(data.context, indent=2)

    messages = [{"role": "system", "content": system_prompt}]
    for msg in data.history:
        if msg.role in ["user", "assistant"]:
            messages.append({"role": msg.role, "content": msg.message})
    messages.append({"role": "user", "content": data.question})

    try:
        response = client.chat.completions.create(
            model="gpt-5.5",
            messages=messages, 
        )
        return {"answer": response.choices[0].message.content}
    except Exception as e:
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/chat/combined")
async def chat_combined(data: ChatRequest):
    system_prompt = "Anda adalah asisten AI medis yang profesional. Tugas Anda adalah menjawab pertanyaan dokter berdasarkan konteks hasil DIAGNOSIS GABUNGAN (klinis + citra) pasien berikut ini. Jawab dengan singkat, padat, dan jelas.\n\nKonteks Analisis Gabungan:\n"
    system_prompt += json.dumps(data.context, indent=2)

    messages = [{"role": "system", "content": system_prompt}]
    for msg in data.history:
        if msg.role in ["user", "assistant"]:
            messages.append({"role": msg.role, "content": msg.message})
    messages.append({"role": "user", "content": data.question})

    try:
        response = client.chat.completions.create(
            model="gpt-5.5",
            messages=messages,
        )
        return {"answer": response.choices[0].message.content}
    except Exception as e:
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=str(e))