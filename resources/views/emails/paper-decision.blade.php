<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Paper Decision' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
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
        .decision-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .decision-accept { background-color: #10B981; color: white; }
        .decision-minor { background-color: #F59E0B; color: white; }
        .decision-major { background-color: #F97316; color: white; }
        .decision-reject { background-color: #EF4444; color: white; }
        .info-box {
            background-color: #F3F4F6;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .deadline-box {
            background-color: #FEF3C7;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #2C3E50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
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
        .congrats {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #D1FAE5;
            border-radius: 8px;
            color: #065F46;
        }
        .full-paper-deadline {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .journal-note {
            background-color: #EFF6FF;
            border-left: 4px solid #3B82F6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .revision-instructions {
            background-color: #FEF2F2;
            border-left: 4px solid #EF4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>DATICAN Conference</h1>
            <p>Improving Medical Diagnostics in Nigeria Using AI and Data Science</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            @php
                $decisionClasses = [
                    'accept' => 'decision-accept',
                    'accept_with_minor_revision' => 'decision-minor',
                    'accept_with_major_revision' => 'decision-major',
                    'reject' => 'decision-reject',
                ];
                $decisionTitles = [
                    'accept' => 'Abstract Acceptance Notification',
                    'accept_with_minor_revision' => 'Abstract Acceptance Notification (Minor Revisions Required)',
                    'accept_with_major_revision' => 'Abstract Acceptance Notification (Major Revisions Required)',
                    'reject' => 'Paper Decision Notification',
                ];
            @endphp
            
            <div style="text-align: center;">
                <div class="decision-badge {{ $decisionClasses[$decision] ?? 'decision-reject' }}">
                    {{ $decisionTitles[$decision] ?? 'Decision Notification' }}
                </div>
            </div>
            
            <p>Dear <strong>{{ $paper->authors->first()->first_name }} {{ $paper->authors->first()->last_name }}</strong>,</p>
            
            <p>Thank you for your recent abstract submission to the DATICAN International Conference. Please find below the update regarding your submission titled, <strong>"{{ $paper->title }}"</strong>.</p>
            
            <!-- Common Acceptance Message for ALL acceptance types -->
            @if(in_array($decision, ['accept', 'accept_with_minor_revision', 'accept_with_major_revision']))
                <p>After an initial review by our team of seasoned reviewers and experts in the field, we are pleased to inform you that your abstract has been <strong>accepted for presentation</strong> at the conference.</p>
                
                <!-- Revision Instructions (for minor/major revision) -->
                @if($decision == 'accept_with_minor_revision')
                    <div class="revision-instructions">
                        <p style="margin: 0 0 10px 0;"><strong>Minor Revisions Required:</strong></p>
                        <p style="margin: 0;">The reviewers have recommended minor revisions to improve your paper. Please carefully address the reviewers' comments and suggestions provided below.</p>
                        <div>
                            @if(!empty($decisionNotes))
                                <p style="margin: 0 0 10px 0;"><strong>Comments:</strong></p>
                                <p style="margin: 0;"><strong></strong>{{ nl2br(e($decisionNotes)) }}</strong></p>
                            @endif
                        </div>
                    </div>
                    <p style="margin: 0;"><strong></strong>Kindly login to your dashboard on the conference website and click view button/icon to see more comments from reviewers.</strong></p>

                @endif


                @if($decision == 'accept_with_major_revision')
                    <div class="revision-instructions">
                        <p style="margin: 0 0 10px 0;"><strong>Major Revisions Required</strong></p>
                        <p style="margin: 0;">The reviewers have recommended major revisions to improve your paper. Please carefully address all reviewers' comments and provide a detailed response to each concern raised.</p>
                        <div>
                            @if(!empty($decisionNotes))
                                <p style="margin: 0 0 10px 0;"><strong>Comments:</strong></p>
                                <p style="margin: 0;"><strong></strong>{{ nl2br(e($decisionNotes)) }}</strong></p>
                            @endif
                        </div>
                    </div>
                    <p style="margin: 0;"><strong></strong>Kindly login to your dashboard on the conference website and click view button/icon to see more comments from reviewers.</strong></p>

                @endif
                
                <!-- Full Paper Submission Deadline -->
                @php
                    $fullPaperDeadline = \Carbon\Carbon::parse('2026-05-21');
                @endphp
                <div class="full-paper-deadline">
                    <p style="margin: 0 0 10px 0;"><strong>Full Paper Submission Required</strong></p>
                    <p style="margin: 0;">You are kindly requested to submit your full paper by: <strong>{{ $fullPaperDeadline->format('F d, Y') }}</strong></p>
                    <p style="margin: 10px 0 0 0; font-size: 14px;">Please ensure all required revisions are incorporated into your full paper submission.</p>
                    <p style="margin: 10px 0 0 0; font-size: 14px;">This will enable us to proceed with the submission to the PG Journal of LASU for peer-review process.</p>
                </div>
                
                <!-- Journal Peer Review Note -->
                <div class="journal-note">
                    <p style="margin: 0 0 10px 0;"><strong>Journal Publication Process</strong></p>
                    <p style="margin: 0;">Your full paper will undergo a thorough peer-review by experts in the field. You will be notified of the outcome, along with further guidelines regarding the presentation, in due course.</p>
                </div>
                
                <!-- Participation Appreciation -->
                <div class="info-box">
                    <p style="margin: 0;">We appreciate your interest in contributing to the success of DATICAN's International Conference and look forward to your participation.</p>
                </div>
                
                <!-- Contact Information -->
                <div class="info-box">
                    <p style="margin: 0;">If you have any questions or require additional information, please do not hesitate to contact us through our conference portal.</p>
                </div>
                
                <!-- Congratulations Message -->
                <div class="congrats">
                    <p style="margin: 0; font-size: 18px; font-weight: bold;">Congratulations!</p>
                </div>
            @endif
            
            <!-- Reject Message -->
            @if($decision == 'reject')
                <p>Thank you for your submission. After careful review by our team of seasoned reviewers and experts in the field, we regret to inform you that your abstract has not been accepted for presentation at the conference.</p>
                
                <div class="info-box">
                    <p style="margin: 0 0 10px 0;"><strong>Reviewer Comments</strong></p>
                    <p style="margin: 0;">The reviewers have provided detailed feedback to help strengthen your work for future submissions. Please find their comments below.</p>
                </div>
                
                <div class="info-box">
                    <p style="margin: 0;">We appreciate your interest in DATICAN and encourage you to consider submitting your future work to our conference.</p>
                </div>
            @endif


           
            <!-- Paper Details -->
            <!-- <div class="info-box">
                <p style="margin: 0 0 10px 0;"><strong>Paper Details</strong></p>
                <p style="margin: 0;"><strong>Title:</strong> {{ $paper->title }}</p>
                <p style="margin: 10px 0 0 0;"><strong>Paper ID:</strong> {{ $paper->anonymous_id }}</p>
                <p style="margin: 10px 0 0 0;"><strong>Topic Area:</strong> {{ $paper->topic_area }}</p>
            </div> -->
            
            <!-- Action Buttons -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('papers.show', $paper->id) }}" class="button" style="color: #ffffffff;">View Paper Details</a>
                <a href="{{ route('dashboard') }}" class="button button-primary" style="color: #ffffffff;">Go to Dashboard</a>
            </div>
            
            <hr>
            
            <div class="footer">
                <p>This is an automated message from the DATICAN Conference System.<br>
                If you have any questions, please contact us at manager.datican@gmail.com</p>
            </div>
        </div>
    </div>
</body>
</html>