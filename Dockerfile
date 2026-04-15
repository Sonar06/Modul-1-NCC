# ==============================
# STAGE 1 — Builder 
# ==============================
FROM python:3.11-alpine AS builder

WORKDIR /app

# Install build dependencies (diperlukan untuk beberapa library python di Alpine)
RUN apk add --no-cache gcc musl-dev linux-headers

# Copy requirements dan install ke folder packages
COPY requirements.txt .
RUN pip install --no-cache-dir --target=/app/packages -r requirements.txt


# ==============================
# STAGE 2 — Final 
# ==============================
FROM python:3.11-alpine AS final

# Optimasi Python
ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1
ENV PORT=5000
# Mengarahkan Python agar mencari library di folder packages tadi
ENV PYTHONPATH=/app/packages

WORKDIR /app

# Install curl (Wajib untuk instruksi HEALTHCHECK)
RUN apk add --no-cache curl

# Security: Jalankan aplikasi sebagai user biasa (bukan root)
RUN addgroup -S appgroup && adduser -S appuser -G appgroup
USER appuser

# Ambil hasil instalasi library dari stage builder
COPY --from=builder /app/packages /app/packages
# Ambil source code (karena workdir di root, kita copy app.py)
COPY app.py .

# --- INSTRUKSI HEALTHCHECK ---
# Mengecek setiap 30 detik apakah endpoint /health merespon
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD curl -f http://localhost:${PORT}/health || exit 1

EXPOSE ${PORT}

CMD ["python", "app.py"]