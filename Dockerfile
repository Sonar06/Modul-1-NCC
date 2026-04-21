# ==============================
# STAGE 1 — Builder 
# ==============================
FROM python:3.11-alpine AS builder

WORKDIR /app

# Install build dependencies 
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
ENV PYTHONPATH=/app/packages

WORKDIR /app

RUN apk add --no-cache curl

# Security: Jalankan aplikasi sebagai user biasa 
RUN addgroup -S appgroup && adduser -S appuser -G appgroup
USER appuser

# Ambil hasil instalasi library dari stage builder
COPY --from=builder /app/packages /app/packages

COPY app.py .

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD curl -f http://localhost:${PORT}/health || exit 1

EXPOSE ${PORT}

CMD ["python", "app.py"]
