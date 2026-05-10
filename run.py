from app import app

if __name__ == "__main__":
    # Baris ini tidak akan dieksekusi saat pytest mengimpor app
    app.run(debug=True)