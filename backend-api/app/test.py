import smtplib
from email.mime.text import MIMEText

sender = "ianmasong7@gmail.com" 
password = "ujbflvgqhdyohuvq" 

print(f"Subsubukang mag-login gamit ang: {sender}")

msg = MIMEText("Subok na mensahe mula sa Tarub Development.")
msg['Subject'] = "Test Connection Direct"
msg['From'] = sender
msg['To'] = sender

try:
    with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
        server.login(sender, password)
        server.sendmail(sender, sender, msg.as_string())
    print("SUCCESS! Gumana yeahey")
except Exception as e:
    print("\n SUMABLAY PA RIN. Ibig sabihin maling App Password talaga ang nakuha ko sa Google: kulit mo")
    print(str(e))