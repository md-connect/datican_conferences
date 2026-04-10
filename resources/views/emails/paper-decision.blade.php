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
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>DATI CAN Conference</h1>
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
                    'accept' => 'Congratulations! Your Paper Has Been Accepted',
                    'accept_with_minor_revision' => 'Paper Accepted with Minor Revisions',
                    'accept_with_major_revision' => 'Paper Accepted with Major Revisions',
                    'reject' => 'Paper Decision Notification',
                ];
                $decisionMessages = [
                    'accept' => 'We are pleased to inform you that your paper has been accepted for presentation at the DATICAN Conference.',
                    'accept_with_minor_revision' => 'Your paper has been accepted pending minor revisions. Please submit the revised version by the deadline.',
                    'accept_with_major_revision' => 'Your paper has been accepted pending major revisions. Please carefully address the reviewers comments and submit the revised version by the deadline.',
                    'reject' => 'Thank you for your submission. After careful review, we regret to inform you that your paper has not been accepted for the conference.',
                ];
            @endphp
            
            <div style="text-align: center;">
                <div class="decision-badge {{ $decisionClasses[$decision] ?? 'decision-reject' }}">
                    {{ $decisionTitles[$decision] ?? 'Decision Notification' }}
                </div>
            </div>
            
            <p>Dear <strong>{{ $paper->authors->first()->first_name }} {{ $paper->authors->first()->last_name }}</strong>,</p>
            
            <p>{{ $decisionMessages[$decision] ?? $decisionMessages['reject'] }}</p>
            
            <!-- Review Summary -->
            @php
                $completedReviews = $paper->reviewAssignments->where('status', 'completed');
            @endphp
            @if($completedReviews->count() > 0)
                @php
                    $avgScore = $completedReviews->avg('total_score');
                @endphp
                <div class="info-box">
                    <p style="margin: 0 0 10px 0;"><strong>📊 Review Summary</strong></p>
                    <p style="margin: 0;">Average Score: <strong>{{ round($avgScore, 1) }}/100</strong></p>
                    <p style="margin: 10px 0 0 0;">Number of Reviews: <strong>{{ $completedReviews->count() }}</strong></p>
                </div>
            @endif
            
            <!-- Revision Deadline -->
            @if($revisionDeadline && in_array($decision, ['accept_with_minor_revision', 'accept_with_major_revision']))
                <div class="deadline-box">
                    <p style="margin: 0 0 10px 0;"><strong>Revision Deadline</strong></p>
                    <p style="margin: 0;">Please submit your revised paper by: <strong>{{ \Carbon\Carbon::parse($revisionDeadline)->format('F d, Y') }}</strong></p>
                    <p style="margin: 10px 0 0 0; font-size: 14px;">Submit your revision through your conference dashboard.</p>
                </div>
            @endif
            
            <!-- Decision Notes -->
            @if($decisionNotes)
                <div class="info-box">
                    <p style="margin: 0 0 10px 0;"><strong>Decision Notes</strong></p>
                    <p style="margin: 0;">{{ $decisionNotes }}</p>
                </div>
            @endif
            
            <!-- Paper Details -->
            <div class="info-box">
                <p style="margin: 0 0 10px 0;"><strong>Paper Details</strong></p>
                <p style="margin: 0;"><strong>Title:</strong> {{ $paper->title }}</p>
                <p style="margin: 10px 0 0 0;"><strong>Paper ID:</strong> {{ $paper->anonymous_id }}</p>
                <p style="margin: 10px 0 0 0;"><strong>Topic Area:</strong> {{ $paper->topic_area }}</p>
            </div>
            
            <!-- Action Buttons -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('papers.show', $paper->id) }}" class="button">View Paper Details</a>
                <a href="{{ route('dashboard') }}" class="button button-primary">Go to Dashboard</a>
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