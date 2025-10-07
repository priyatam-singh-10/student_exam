<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
        .row { margin: 6px 0; }
        .muted { color: #666; }
    </style>
    </head>
<body>
    <div class="header">
        <div class="title">Exam Payment Receipt</div>
        <div class="muted">Thank you for your payment.</div>
    </div>
    <div class="row">Receipt #: {{ $receipt_number }}</div>
    <div class="row">Date: {{ $paid_at }}</div>
    <div class="row">Name: {{ $user_name }}</div>
    <div class="row">Email: {{ $user_email }}</div>
    <div class="row">Form: {{ $form_title }}</div>
    <div class="row">Submission ID: {{ $submission_id }}</div>
    <div class="row">Provider: {{ strtoupper($provider) }}</div>
    <div class="row">Amount: {{ $currency }} {{ number_format($amount, 2) }}</div>
    <div class="row">Status: {{ ucfirst($status) }}</div>
    <hr>
    <div class="row muted">This is a system generated receipt.</div>
</body>
</html>

