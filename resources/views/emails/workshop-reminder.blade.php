<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<h1>Workshop Reminder 📅</h1>
<p>Hi {{ $workshop->title }} is scheduled for tomorrow!</p>
<ul>
    <li><strong>Title:</strong> {{ $workshop->title }}</li>
    <li><strong>Date:</strong> {{ $workshop->starts_at->format('d/m/Y') }}</li>
    <li><strong>Time:</strong> {{ $workshop->starts_at->format('H:i') }} - {{ $workshop->ends_at->format('H:i') }}</li>
</ul>
<p>See you there! 🚀</p>
</body>
</html>
