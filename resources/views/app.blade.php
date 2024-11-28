<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite('resources/js/app.js')
    {{-- Add This line of code --}}
    <script src="https://widget.cloudinary.com/v2.0/global/all.js" type="text/javascript"></script>
    @routes
    @inertiaHead
</head>

<body>
    @inertia
</body>

</html>
