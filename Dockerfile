<<<<<<< HEAD
FROM python:3.11-alpine

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 5000

=======
# Gunakan base image Python yang ringan
FROM python:3.11-alpine

# Set working directory di dalam container
WORKDIR /app

# Copy file requirements dan install library
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy seluruh kode aplikasi ke container
COPY . .

# Ekspos port yang digunakan Flask
EXPOSE 5000

# Perintah untuk menjalankan aplikasi
>>>>>>> 5a0c9c6 (Initial commit: tes endpoint health)
CMD ["python", "app.py"]
