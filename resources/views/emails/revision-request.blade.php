<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission of Revised Abstract – DATICAN Conference</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #2C3E50 0%, #1A252F 100%);
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            color: white;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #ccc;
            margin: 10px 0 0 0;
        }
        .content {
            background: white;
            padding: 30px;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 10px 10px;
        }
        .info-box {
            background-color: #F3F4F6;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .deadline-box {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #2C3E50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .button-primary {
            background-color: #E74C3C;
        }
        hr {
            margin: 30px 0;
            border: none;
            border-top: 1px solid #e0e0e0;
        }
        .footer {
            font-size: 12px;
            color: #999;
            text-align: center;
        }
        .list {
            margin: 15px 0;
            padding-left: 20px;
        }
        .list li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>DATI CAN Conference</h1>
            <p>Improving Medical Diagnostics in Nigeria Using AI and Data Science</p>
            <p>May 13-14, 2026</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <p>Dear <strong>{{ $authorName ?? $paper->authors->first()->first_name . ' ' . $paper->authors->first()->last_name }}</strong>,</p>
            
            <p>I hope this message finds you well.</p>
            
            <p>Following the acceptance of the abstract of your paper titled 
            <strong>"{{ $paper->title }}"</strong> submitted for the DATICAN Conference scheduled 
            for <strong>13th and 14th May, 2026</strong>, you are kindly requested to complete the following steps:</p>
            
            <div class="info-box">
                <ol class="list" style="margin: 0; padding-left: 20px;">
                    <li><strong>Revise your abstract</strong> in line with the reviewers' comments.</li>
                    <li><strong>Prepare a Microsoft Word document</strong> of the revised abstract, ensuring it includes:
                        <ul style="margin-top: 8px;">
                            <li>Abstract Title</li>
                            <li>Authors' Names</li>
                            <li>Affiliations</li>
                            <li>Abstract (maximum of 250 words)</li>
                            <li>Keywords</li>
                        </ul>
                    </li>
                    <li><strong>Log in to the conference portal</strong> and upload the prepared document.</li>
                </ol>
            </div>
            
            <p>This will enable us to prepare the <strong>Book of Abstracts</strong> for the conference.</p>
            
            <div class="deadline-box">
                <p style="margin: 0; font-weight: bold;">Deadline:</p>
                <p style="margin: 5px 0 0 0;">Kindly note that the deadline for uploading the final version of your abstract is <strong>Friday, May 1, 2026</strong>.</p>
            </div>
            
            <p>Thank you for your cooperation.</p>
            
            <p>Best regards,<br>
            <strong>DATICAN Conference LOC</strong></p>
            
            <hr>
            
            <div class="footer">
                If you have any questions, please contact us at <a href="mailto:manager.datican@gmail.com">manager.datican@gmail.com</a>
            </div>
        </div>
    </div>
</body>
</html>