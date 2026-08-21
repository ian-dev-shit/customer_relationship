import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from app.config.config import settings

def send_customer_welcome_email(
    to_email: str,
    first_name: str,
    password: str,
    customer_id: str,
    company_name: str
):
    sender_email = settings.SMTP_SENDER
    sender_password = settings.SMTP_PASSWORD

    msg = MIMEMultipart()
    msg['From'] = f"SwiftFreight Admin <{sender_email}>"
    msg['To'] = to_email
    msg['Subject'] = "Welcome to SwiftFreight - Your Customer Portal Account Credentials"

    # SwiftFreight Logo URL or placeholder
    logo_url = "https://ueexljyfzygzhgjluqhm.supabase.co/storage/v1/object/public/assets/logoW-1.png"

    html_content = f"""
    <html>
        <body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; padding: 40px 20px; margin: 0;">
            <div style="max-width: 560px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">

                <!-- LOGO HERE -->
                <div style="text-align: center; margin-bottom: 25px;">
                    <img src="{logo_url}" alt="SwiftFreight Logo" style="max-width: 130px; height: auto; display: block; margin: 0 auto;" />
                </div>
                
                <!-- Title / Header -->
                <h2 style="font-size: 22px; font-weight: 700; color: #0f172a; text-align: center; margin-bottom: 24px; margin-top: 0;">
                    Welcome to SwiftFreight!
                </h2>
                
                <!-- Body Text -->
                <p style="font-size: 14px; line-height: 1.6; color: #475569; margin-bottom: 12px;">
                    Hello <strong>{first_name}</strong>,
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #475569; margin-top: 0; margin-bottom: 24px;">
                    Your customer portal account for <strong>{company_name}</strong> has been successfully created by our administrator. You can now log in using the credentials below:
                </p>
                
                <!-- Credentials Box -->
                <div style="background-color: #f1f5f9; padding: 20px; border-radius: 8px; border-left: 4px solid #4f46e5; margin: 25px 0;">
                    <p style="font-size: 13px; color: #64748b; margin: 0 0 6px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Account Details</p>
                    <p style="font-size: 14px; color: #1e293b; margin: 4px 0;"><strong>Account Reference ID:</strong> <code style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-size: 13px;">{customer_id}</code></p>
                    <p style="font-size: 14px; color: #1e293b; margin: 4px 0;"><strong>Login Email:</strong> {to_email}</p>
                    <p style="font-size: 14px; color: #1e293b; margin: 4px 0;"><strong>Temporary Password:</strong> <strong style="color: #4f46e5; font-family: monospace; font-size: 15px;">{password}</strong></p>
                </div>

                <!-- CTA Button -->
                <div style="text-align: center; margin: 30px 0;">
                    <a href="http://127.0.0.1:3000/login.php" 
                       style="background-color: #4f46e5; color: #ffffff; padding: 12px 28px; border-radius: 6px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-block;">
                       Access Customer Portal
                    </a>
                </div>
                
                <!-- Notice Text -->
                <p style="font-size: 12px; line-height: 1.5; color: #64748b; margin-top: 25px; margin-bottom: 25px;">
                    For security purposes, we recommend changing your temporary password immediately after your first login.
                </p>
                
                <!-- Footer Divider -->
                <hr style="border: 0; border-top: 1px solid #f1f5f9; margin-bottom: 20px;">
                
                <!-- Footer Text -->
                <p style="font-size: 11px; color: #94a3b8; text-align: center; margin: 0; letter-spacing: 0.5px;">
                    SwiftFreight Operations System • Customer Portal Service
                </p>
            </div>
        </body>
    </html>
    """

    msg.attach(MIMEText(html_content, 'html', 'utf-8'))

    try:
        with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
            server.login(sender_email, sender_password)
            server.sendmail(sender_email, to_email, msg.as_string())

        return True
    except Exception as e:
        print(f"SMTP Email Error: {str(e)}")
        return False