# 1. Base image sa Python
FROM python:3.12-slim

# Iwasan ang interactive prompts sa apt-get
ENV DEBIAN_FRONTEND=noninteractive

# 2. Install required Linux system C-libraries para sa WeasyPrint PDF Rendering
RUN apt-get update -o Acquire::Retries=3 && \
    apt-get install -y --no-install-recommends \
    build-essential \
    python3-dev \
    libffi-dev \
    libcairo2 \
    libpango-1.0-0 \
    libpangocairo-1.0-0 \
    libgdk-pixbuf-2.0-0 \
    shared-mime-info \
    fonts-liberation \
    && rm -rf /var/lib/apt/lists/*

# 3. Set working directory 
WORKDIR /app

# 4. Kopyahin ang requirements.txt at i-install 
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# 5. Kopyahin ang buong backend application code
COPY . .

# 6. Expose port 8000 para sa FastAPI
EXPOSE 8000

# 7. Command para patakbuhin ang FastAPI gamit ang Uvicorn sa production
CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8000"]