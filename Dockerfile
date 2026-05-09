# STAGE 1: Builder
FROM python:3.11-slim AS builder

WORKDIR /app

# Install build dependencies
RUN apt-get update && apt-get install -y \
    gcc \
    python3-dev \
    && rm -rf /var/lib/apt/lists/*

# Install library ke folder lokal
COPY requirements.txt .
RUN pip install --user --no-cache-dir -r requirements.txt


# STAGE 2: Runner (Image Akhir)
FROM python:3.11-slim AS runner

WORKDIR /app

# Ambil hasil install library dari stage builder
COPY --from=builder /root/.local /root/.local
# Ambil kode aplikasi
COPY . .

# Pastikan path python mengarah ke folder library yang di-copy
ENV PATH=/root/.local/bin:$PATH
ENV FLASK_APP=app.py

EXPOSE 5000

CMD ["python", "app.py"]