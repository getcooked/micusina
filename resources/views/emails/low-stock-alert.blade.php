<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Low stock alert</title>
</head>
<body>
    <h1>Low stock alert</h1>
    <p><strong>{{ $food->title }}</strong> now has <strong>{{ $food->stock }}</strong> item(s) remaining.</p>
    <p>Your low-stock threshold is {{ $threshold }}. Please restock this item soon.</p>
</body>
</html>
