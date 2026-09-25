import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.application import MIMEApplication
from typing import Optional
from app.config.config import settings

def send_quotation_email(
    to_email: str, 
    company_name: str, 
    pdf_bytes: bytes, 
    filename: str = "Quotation.pdf",
    origin: Optional[str] = "N/A",
    destination: Optional[str] = "N/A",
    service_type: Optional[str] = "Freight",
    estimated_amount: Optional[float] = 0.0,
    inquiry_code: Optional[str] = None
) -> bool:
    sender_email = settings.SMTP_SENDER
    sender_password = settings.SMTP_PASSWORD

    # 1. Setup email headers
    msg = MIMEMultipart()
    msg['From'] = f"Priority  <{sender_email}>"
    msg['To'] = to_email
    msg['Subject'] = f"Official Freight Quotation - {inquiry_code or 'Priority Logistics'}"

    logo_url = "https://ueexljyfzygzhgjluqhm.supabase.co/storage/v1/object/public/assets/logo1.jpg"
    formatted_amount = f"PHP {estimated_amount:,.2f}" if estimated_amount else "See PDF attachment"

    # 2. Modern HTML Email Body
    html_content = f"""
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; padding: 30px 10px; margin: 0;">
            <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                
                <!-- HEADER BANNER -->
                <div style="background-color: #1e3a8a; padding: 25px 30px; text-align: center;">
                    <img src="{logo_url}" alt="Priority Logistics" style="max-width: 140px; height: auto; margin-bottom: 10px; display: inline-block;" />
                    <h1 style="color: #ffffff; font-size: 18px; margin: 0; font-weight: 600; tracking: 0.5px;">Priority Handling Logistics Inc.</h1>
                </div>

                <!-- MAIN CONTENT -->
                <div style="padding: 32px 30px;">
                    <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 16px;">
                        Official Price Quotation
                    </h2>
                    
                    <p style="font-size: 14px; line-height: 1.6; color: #374151; margin-bottom: 12px;">
                        Dear <strong>{company_name}</strong>,
                    </p>
                    <p style="font-size: 14px; line-height: 1.6; color: #4b5563; margin-top: 0; margin-bottom: 24px;">
                        Thank you for reaching out to us. We are pleased to provide our official price quotation for your freight transport request.
                    </p>
                    
                    <!-- QUICK SUMMARY BOX -->
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
                        <h3 style="font-size: 12px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; margin-top: 0; margin-bottom: 12px; letter-spacing: 0.5px;">
                            📋 Quotation Quick Summary
                        </h3>
                        <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 4px 0; color: #64748b; width: 40%;">Ref / Inquiry Code:</td>
                                <td style="padding: 4px 0; color: #0f172a; font-weight: 600;">{inquiry_code or 'N/A'}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; color: #64748b;">Route:</td>
                                <td style="padding: 4px 0; color: #0f172a; font-weight: 600;">{origin} ➔ {destination}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; color: #64748b;">Service Type:</td>
                                <td style="padding: 4px 0; color: #0f172a; font-weight: 600;">{service_type}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; color: #64748b;">Total Estimated Amount:</td>
                                <td style="padding: 4px 0; color: #2563eb; font-weight: 700; font-size: 14px;">{formatted_amount}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- ATTACHMENT NOTICE -->
                    <div style="background-color: #eff6ff; padding: 14px 16px; border-radius: 6px; border-left: 4px solid #2563eb; margin-bottom: 24px;">
                        <p style="font-size: 13px; color: #1e40af; margin: 0; line-height: 1.5;">
                            📎 <strong>Attached File:</strong> Please find the attached official PDF document (<code>{filename}</code>) for the complete itemized breakdown, validity period, and terms & conditions.
                        </p>
                    </div>

                    <p style="font-size: 13px; line-height: 1.5; color: #6b7280; margin-bottom: 24px;">
                        If you wish to accept this quotation or request modifications, feel free to reply directly to this email or reach out to our team.
                    </p>
                    
                    <hr style="border: 0; border-top: 1px solid #f3f4f6; margin-bottom: 20px;">
                    
                    <p style="font-size: 11px; color: #9ca3af; text-align: center; margin: 0;">
                        Priority Handling Logistics Inc. • Reliable & Express Freight Solutions<br/>
                        This is an automated quotation notification email.
                    </p>
                </div>
            </div>
        </body>
    </html>
    """

    msg.attach(MIMEText(html_content, 'html', 'utf-8'))

    # 3. Attach PDF Byte Data
    pdf_attachment = MIMEApplication(pdf_bytes, _subtype="pdf")
    pdf_attachment.add_header('Content-Disposition', 'attachment', filename=filename)
    msg.attach(pdf_attachment)

    # 4. Send via SMTP
    try:
        with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
            server.login(sender_email, sender_password)
            server.sendmail(sender_email, to_email, msg.as_string())
        return True
    except Exception as e:
        print(f"SMTP Quotation Email Error: {str(e)}")
        return False