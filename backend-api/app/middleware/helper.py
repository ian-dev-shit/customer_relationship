
# OTP helper

import resend
from app.config.config import settings

# Resend api Setup
resend.api_key = settings.RESEND_API_KEY

def send_otp_email(to_email: str, otp_code: str):
    try:

        html_content = f"""
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px;">
            <h2 style="color: #333; text-align: center;">Your One-Time Password (OTP)</h2>
            <p>Hello,</p>
            <p>You requested a One-Time Password (OTP) to log in to your account. This code is valid for <strong>5 minutes</strong>.</p>
            <div style="text-align: center; margin: 30px 0;">
                <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #4F46E5; background-color: #F3F4F6; padding: 10px 20px; border-radius: 5px;">{otp_code}</span>
            </div>
            <p>If you did not request this code, please ignore this email or contact support if you have concerns.</p>
            <hr style="border: 0; border-top: 1px solid #e0e0e0;" />
            <p style="font-size: 12px; color: #777; text-align: center;">Customer Relationship Management System &bull; Secure Auth Service</p>
        </div>
        """

        response = resend.Emails.send({
            "from": "Acme <onboarding@resend.dev>",
            "to": to_email,
            "subject": f"{otp_code} is your Login Verification Code",
            "html": html_content
        })

        return response
    except Exception as e:
        print(f"Failed to send email: {str(e)}")
        return None