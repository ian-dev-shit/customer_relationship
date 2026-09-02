
# OTP helper
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from app.config.config import settings

def send_otp_email(to_email: str, otp_code: str):

    sender_email = settings.SMTP_SENDER
    sender_password = settings.SMTP_PASSWORD

    # 1. set up ang email Header

    msg = MIMEMultipart()
    msg['From'] = f"Priority Handling Logistics Inc. <{sender_email}>"
    msg['To'] = to_email
    msg['Subject'] = f"{otp_code} is your Priority Handling Logistics Inc. Verification Code"

    logo_url = "https://ueexljyfzygzhgjluqhm.supabase.co/storage/v1/object/public/assets/logo1.jpg"

    # HTML Body clean look 
    html_content = f"""
    <html>
        <body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #ffffff; color: #333333; padding: 40px 20px; margin: 0;">
            <div style="max-width: 560px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px; border: 1px solid #e1e1e1; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">

            <!--  LOGO HERE -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="{logo_url}" alt="Dragon" style="max-width: 120px; height: auto; display: block; margin: 0 auto;" />
                </div
                
                <!-- Title / Header -->
                <h2 style="font-size: 22px; font-weight: 600; color: #111111; text-align: center; margin-bottom: 30px; margin-top: 0;">
                    Your One-Time Password (OTP)
                </h2>
                
                <!-- Body Text -->
                <p style="font-size: 14px; line-height: 1.6; color: #444444; margin-bottom: 10px;">
                    Hello,
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #444444; margin-top: 0; margin-bottom: 30px;">
                    You requested a One-Time Password (OTP) to log in to your account. This code is valid for <strong style="color: #ff3838;">5 minutes</strong>.
                </p>
                
                <!-- OTP Box -->
                <div style="text-align: center; margin: 35px 0;">
                    <span style="font-size: 32px; font-weight: bold; letter-spacing: 4px; color: #ff3838; background-color: #fff5f5; padding: 12px 35px; border-radius: 4px; border: 1px dashed #ffb8b8; display: inline-block;">
                        {otp_code}
                    </span>
                </div>
                
                <!-- Notice Text -->
                <p style="font-size: 13px; line-height: 1.5; color: #666666; margin-top: 30px; margin-bottom: 30px;">
                    If you did not request this code, please ignore this email or contact support if you have concerns.
                </p>
                
                <!-- Footer Divider -->
                <hr style="border: 0; border-top: 1px solid #eeeeee; margin-bottom: 20px;">
                
                <!-- Footer Text -->
                <p style="font-size: 11px; color: #888888; text-align: center; margin: 0; letter-spacing: 0.5px;">
                    Priority Handling Logistics Inc. • Secure Auth Service
                </p>
            </div>
        </body>
    </html>
    """

    msg.attach(MIMEText(html_content, 'html', 'utf-8'))

    try:
        # connect sa SMTP server ng google PORT 465
        with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
            server.login(sender_email, sender_password)
            server.sendmail(sender_email, to_email, msg.as_string())

        return True
    except Exception as e:
        print(f"SMTP Email Error: {str(e)}")
        return False